<?php
namespace App\Jobs;

use App\Domain\Ocr\Services\ApplyVerifiedExtraction;
use App\Models\DocumentExtraction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutoApplyTrustedExtraction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $extractionId) {}

    public function handle(ApplyVerifiedExtraction $service): void
    {
        $extraction = DocumentExtraction::with(['fields', 'document', 'item'])
            ->findOrFail($this->extractionId);

        $unresolved = $extraction->fields->contains(fn ($field) =>
            $field->requires_verification ||
            in_array($field->verification_status, ['PENDING', 'MISSING'], true)
        );

        if ($unresolved) {
            return;
        }

        $service->apply($extraction, $extraction->document->registered_by);
    }
}
