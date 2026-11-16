<?php
namespace App\Domain\Ocr\Services;

use App\Models\DocumentExtraction;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ApplyVerifiedExtraction
{
    private const ALLOWED_FIELDS = [
        'patta_no',
        'dag_no',
        'record_year',
        'landholder_name',
        'guardian_name',
    ];

    public function apply(DocumentExtraction $extraction, int $userId): void
    {
        $extraction->loadMissing(['fields', 'document']);

        if (! $extraction->document) {
            throw new RuntimeException('The extraction is not linked to a document.');
        }

        $unresolved = $extraction->fields
            ->whereIn('verification_status', ['PENDING', 'MISSING']);

        if ($unresolved->isNotEmpty()) {
            throw new RuntimeException('All required OCR/HTR fields must be resolved before insertion.');
        }

        $updates = [];

        foreach ($extraction->fields as $field) {
            if (! in_array($field->field_name, self::ALLOWED_FIELDS, true)) {
                continue;
            }

            if ($field->verification_status === 'REJECTED') {
                continue;
            }

            $value = trim((string) $field->normalized_value);

            if ($value === '') {
                continue;
            }

            if ($field->field_name === 'record_year') {
                $year = (int) $value;

                if ($year < 1700 || $year > (int) now()->year) {
                    throw new RuntimeException('Extracted record year is outside the permitted range.');
                }

                $updates[$field->field_name] = $year;
                continue;
            }

            $updates[$field->field_name] = $value;
        }

        DB::transaction(function () use ($extraction, $updates, $userId): void {
            if ($updates !== []) {
                $extraction->document->forceFill($updates)->save();
            }

            $extraction->update([
                'status' => 'APPLIED',
                'metadata' => array_merge($extraction->metadata ?? [], [
                    'applied_by' => $userId,
                    'applied_at' => now()->toIso8601String(),
                    'applied_fields' => array_keys($updates),
                ]),
            ]);

            $extraction->item?->update([
                'status' => 'EXTRACTED',
                'finished_at' => now(),
            ]);
        }, 3);
    }
}
