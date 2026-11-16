<?php
namespace App\Jobs;

use App\Models\DigitizationBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FinalizeDigitizationBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $batchId) {}

    public function handle(): void
    {
        $batch = DigitizationBatch::withCount([
            'items',
            'items as failed_count' => fn ($q) => $q->where('status', 'FAILED'),
            'items as verification_count' => fn ($q) => $q->where('status', 'VERIFICATION_REQUIRED'),
            'items as completed_count' => fn ($q) => $q->whereIn('status', ['EXTRACTED']),
            'items as active_count' => fn ($q) => $q->whereIn('status', [
                'PENDING', 'PROCESSING', 'OCR_PENDING', 'RECOGNIZING',
            ]),
        ])->findOrFail($this->batchId);

        if ($batch->active_count > 0) {
            self::dispatch($batch->id)->delay(now()->addSeconds(30));
            return;
        }

        $status = match (true) {
            $batch->failed_count > 0 => 'COMPLETED_WITH_ERRORS',
            $batch->verification_count > 0 => 'PENDING_VERIFICATION',
            default => 'COMPLETED',
        };

        $batch->update([
            'status' => $status,
            'failed_files' => $batch->failed_count,
            'verification_files' => $batch->verification_count,
            'processed_files' => $batch->completed_count + $batch->verification_count,
            'processing_finished_at' => now(),
        ]);
    }
}
