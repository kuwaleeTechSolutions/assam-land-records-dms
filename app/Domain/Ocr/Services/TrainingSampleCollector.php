<?php
namespace App\Domain\Ocr\Services;

use App\Models\DocumentExtractedField;
use App\Models\HtrTrainingSample;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class TrainingSampleCollector
{
    public function collect(DocumentExtractedField $field, int $userId): ?HtrTrainingSample
    {
        $field->loadMissing('extraction.item.document.currentVersion.files');
        $extraction = $field->extraction;
        $document = $extraction?->document;
        $master = $document?->currentVersion?->files?->firstWhere('file_role', 'MASTER');

        if (! $master || trim((string) $field->normalized_value) === '') {
            return null;
        }

        // Phase 5 stores the source page/PDF reference. Phase 6 may add field bounding-box crops.
        $sourcePath = Storage::disk($master->storage_disk)->path($master->storage_key);
        if (! is_file($sourcePath)) {
            throw new RuntimeException('Source file is unavailable for training-sample collection.');
        }

        $sha = hash_file('sha256', $sourcePath);

        return HtrTrainingSample::firstOrCreate(
            [
                'document_extracted_field_id' => $field->id,
                'image_sha256' => $sha,
            ],
            [
                'ulid' => (string) Str::ulid(),
                'document_id' => $document->id,
                'field_name' => $field->field_name,
                'language' => 'asm',
                'source_disk' => $master->storage_disk,
                'source_key' => $master->storage_key,
                'model_prediction' => $field->source_value,
                'verified_text' => $field->normalized_value,
                'model_confidence' => $field->confidence,
                'sample_status' => 'READY',
                'verified_by' => $userId,
                'verified_at' => now(),
            ],
        );
    }
}
