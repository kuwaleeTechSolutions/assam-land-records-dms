<?php

namespace App\Jobs;

use App\Domain\Documents\Services\RegisterDocument;
use App\Domain\Storage\Services\SecurePdfIngestor;
use App\Models\DigitizationBatch;
use App\Models\DigitizationBatchItem;
use App\Models\State;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;
use ZipArchive;

class ProcessDigitizationZip implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 3600;

    public int $tries = 2;

    public function __construct(
        public int $batchId,
    ) {
    }

    public function handle(
        RegisterDocument $register,
        SecurePdfIngestor $ingestor,
    ): void {
        $batch = DigitizationBatch::findOrFail($this->batchId);

        $batch->update([
            'status' => 'EXTRACTING',
            'processing_started_at' => now(),
            'processing_finished_at' => null,
            'failure_message' => null,
        ]);

        $zipPath = Storage::disk($batch->source_disk)
            ->path($batch->source_zip_key);

        $zip = new ZipArchive();

        $openResult = $zip->open($zipPath);

        if ($openResult !== true) {
            throw new RuntimeException(
                'Unable to open ZIP archive. ZIP error code: '.$openResult,
            );
        }

        try {
            $entries = $this->findPdfEntries($zip);

            if ($entries === []) {
                throw new RuntimeException(
                    'No PDF files were found inside the ZIP archive.',
                );
            }

            $maximumFiles = (int) config('dms.max_zip_files', 5000);

            if (count($entries) > $maximumFiles) {
                throw new RuntimeException(
                    "ZIP contains more than {$maximumFiles} PDF files.",
                );
            }

            $batch->update([
                'total_files' => count($entries),
                'status' => 'REGISTERING',
            ]);

            $state = State::where('code', 'AS')->firstOrFail();

            foreach ($entries as $entry) {
                $this->processEntry(
                    batch: $batch,
                    stateId: $state->id,
                    entry: $entry,
                    zip: $zip,
                    register: $register,
                    ingestor: $ingestor,
                );
            }
        } finally {
            $zip->close();
        }



        /*
         * Do not mark the batch completed here.
         *
         * Recognition jobs are now running independently. The finalizer will
         * mark the batch COMPLETED, PENDING_VERIFICATION, or
         * COMPLETED_WITH_ERRORS after all entries finish.
         */
        $batch->update([
            'status' => 'OCR_PENDING',
            'processing_finished_at' => null,
        ]);

        FinalizeDigitizationBatch::dispatch($batch->id)
            ->onQueue('database')
            ->delay(now()->addSeconds(30));
    }

    private function findPdfEntries(ZipArchive $zip): array
    {
        $entries = [];

        for ($index = 0; $index < $zip->numFiles; $index++) {
            $stat = $zip->statIndex($index);

            if ($stat === false) {
                continue;
            }

            $name = (string) ($stat['name'] ?? '');

            if ($name === '' || str_ends_with($name, '/')) {
                continue;
            }

            $normalized = str_replace('\\', '/', $name);

            $this->assertSafeArchivePath($normalized);

            /*
             * Ignore macOS metadata and hidden files.
             */
            if (
                str_starts_with($normalized, '__MACOSX/')
                || str_starts_with(basename($normalized), '._')
                || basename($normalized) === '.DS_Store'
            ) {
                continue;
            }

            if (
                strtolower(pathinfo($normalized, PATHINFO_EXTENSION))
                !== 'pdf'
            ) {
                continue;
            }

            $entries[] = [
                'index' => $index,
                'archive_path' => $normalized,
                'size_bytes' => (int) ($stat['size'] ?? 0),
            ];
        }

        return $entries;
    }

    private function assertSafeArchivePath(string $path): void
    {
        if (
            str_starts_with($path, '/')
            || str_contains($path, '../')
            || str_contains($path, '/..')
            || preg_match('/^[A-Za-z]:\//', $path)
        ) {
            throw new RuntimeException(
                'Unsafe ZIP path detected: '.$path,
            );
        }
    }

    private function processEntry(
        DigitizationBatch $batch,
        int $stateId,
        array $entry,
        ZipArchive $zip,
        RegisterDocument $register,
        SecurePdfIngestor $ingestor,
    ): void {
        $archivePath = $entry['archive_path'];
    
        $archivePathHash = hash(
            'sha256',
            $archivePath
        );
    
        $item = DigitizationBatchItem::firstOrCreate(
            [
                'digitization_batch_id' => $batch->id,
                'archive_path_hash' => $archivePathHash,
            ],
            [
                'ulid' => (string) Str::ulid(),
                'archive_path' => $archivePath,
                'original_filename' => basename($archivePath),
                'size_bytes' => $entry['size_bytes'],
                'status' => 'PROCESSING',
                'ocr_status' => 'NOT_STARTED',
                'htr_status' => 'NOT_STARTED',
                'started_at' => now(),
            ],
        );

        /*
         * This makes retrying the ZIP job safer.
         *
         * If the document was registered before a worker interruption,
         * do not create it again. Requeue recognition only when needed.
         */
        if ($item->document_id) {
            if (in_array(
                $item->status,
                ['PROCESSING', 'OCR_PENDING', 'RECOGNITION_FAILED'],
                true,
            )) {
                $item->update([
                    'status' => 'OCR_PENDING',
                    'ocr_status' => 'QUEUED',
                    'failure_code' => null,
                    'failure_message' => null,
                    'finished_at' => null,
                ]);

                RunDocumentRecognition::dispatch($item->id)
                    ->onQueue('recognition');
            }

            return;
        }

        $temporaryPath = tempnam(
            sys_get_temp_dir(),
            'dmszip_',
        );

        if ($temporaryPath === false) {
            throw new RuntimeException(
                'Unable to create a temporary extraction file.',
            );
        }

        try {
            $input = $zip->getStream($archivePath);

            if ($input === false) {
                throw new RuntimeException(
                    'Cannot read ZIP entry: '.$archivePath,
                );
            }

            $output = fopen($temporaryPath, 'wb');

            if ($output === false) {
                fclose($input);

                throw new RuntimeException(
                    'Cannot create temporary PDF for: '.$archivePath,
                );
            }

            try {
                $copiedBytes = stream_copy_to_stream(
                    $input,
                    $output,
                );
            } finally {
                fclose($input);
                fclose($output);
            }

            if ($copiedBytes === false || $copiedBytes === 0) {
                throw new RuntimeException(
                    'The extracted PDF is empty: '.$archivePath,
                );
            }

            /*
             * Confirm that the extracted size is reasonable.
             */
            $actualSize = filesize($temporaryPath);

            if ($actualSize === false || $actualSize === 0) {
                throw new RuntimeException(
                    'Unable to determine extracted PDF size.',
                );
            }

            $sha256 = hash_file('sha256', $temporaryPath);

            if ($sha256 === false) {
                throw new RuntimeException(
                    'Unable to calculate PDF SHA-256 checksum.',
                );
            }

            $item->update([
                'size_bytes' => $actualSize,
                'sha256' => $sha256,
                'status' => 'REGISTERING',
            ]);

            $document = $register->execute(
                [
                    'digitization_batch_id' => $batch->id,
                    'document_category_id' => $batch->document_category_id,
                    'document_type_id' => $batch->document_type_id,
                    'state_id' => $stateId,
                    'district_id' => $batch->district_id,
                    'lac_id' => $batch->lac_id,
                    'revenue_circle_id' => $batch->revenue_circle_id,
                    'mouza_id' => $batch->mouza_id,
                    'village_id' => $batch->village_id,
                    'title' => pathinfo(
                        $item->original_filename,
                        PATHINFO_FILENAME,
                    ),
                    'record_year' => $batch->default_record_year,
                    'physical_file_reference' => $archivePath,
                    'remarks' => 'Created through Speed Digitization batch '
                        .$batch->batch_reference,
                ],
                $batch->opened_by,
            );

            $upload = new UploadedFile(
                path: $temporaryPath,
                originalName: $item->original_filename,
                mimeType: 'application/pdf',
                error: null,
                test: true,
            );

            $file = $ingestor->ingest(
                $document,
                $upload,
                $batch->opened_by,
            );

            $item->update([
                'document_id' => $document->id,
                'status' => 'OCR_PENDING',
                'ocr_status' => 'QUEUED',
                'htr_status' => 'NOT_STARTED',
                'failure_code' => null,
                'failure_message' => null,
                'finished_at' => null,
            ]);

            /*
             * Derivative generation and recognition are separate jobs.
             * Recognition uses the MASTER PDF, so it does not need to wait
             * for the preview derivative.
             */
            GenerateDocumentDerivatives::dispatch($file->id)
                ->onQueue('default');

            /*
             * Critical: dispatch recognition for every item inside the loop.
             */
            RunDocumentRecognition::dispatch($item->id)
                ->onQueue('recognition');
        } catch (Throwable $exception) {
            report($exception);

            $item->update([
                'status' => 'FAILED',
                'ocr_status' => 'FAILED',
                'failure_code' => 'INGEST_FAILED',
                'failure_message' => Str::limit(
                    $exception->getMessage(),
                    5000,
                ),
                'finished_at' => now(),
            ]);
        } finally {
            if (is_file($temporaryPath)) {
                @unlink($temporaryPath);
            }
        }
    }

    public function failed(Throwable $exception): void
    {
        DigitizationBatch::whereKey($this->batchId)->update([
            'status' => 'FAILED',
            'failure_message' => Str::limit(
                $exception->getMessage(),
                5000,
            ),
            'processing_finished_at' => now(),
        ]);
    }
}