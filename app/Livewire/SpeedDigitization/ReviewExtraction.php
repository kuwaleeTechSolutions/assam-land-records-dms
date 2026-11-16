<?php
namespace App\Livewire\SpeedDigitization;

use App\Domain\Ocr\Services\ApplyVerifiedExtraction;
use App\Models\DocumentExtraction;
use App\Models\DocumentExtractedField;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ReviewExtraction extends Component
{
    public DocumentExtraction $extraction;
    public array $values = [];
    public array $decisions = [];
    public string $remarks = '';

    public function mount(DocumentExtraction $extraction): void
    {
        abort_unless(
            app(\App\Domain\Authorization\Services\PermissionResolver::class)
                ->allows(auth()->user(), 'documents.verify'),
            403,
        );

        $this->extraction = $extraction->load([
            'document.currentVersion.files',
            'document.district',
            'document.revenueCircle',
            'document.mouza',
            'document.village',
            'document.lac',
            'fields',
            'item.batch',
        ]);

        foreach ($this->extraction->fields as $field) {
            $this->values[$field->id] = $field->normalized_value ?? '';
            $this->decisions[$field->id] = in_array(
                $field->verification_status,
                ['VERIFIED', 'CORRECTED', 'REJECTED'],
                true,
            ) ? $field->verification_status : '';
        }
    }

    public function accept(
        int $fieldId,
        \App\Domain\Ocr\Services\TrainingSampleCollector $collector
    ): void {
        $field = $this->ownedField($fieldId);
    
        $value = trim((string) ($this->values[$fieldId] ?? ''));
    
        if ($value === '') {
            $this->addError(
                "values.$fieldId",
                'Enter or confirm a value before accepting it.'
            );
    
            return;
        }
    
        $field->update([
            'normalized_value' => $value,
    
            'requires_verification' => false,
    
            'verification_status' =>
                $value === (string) $field->source_value
                    ? 'VERIFIED'
                    : 'CORRECTED',
    
            'verified_by' => auth()->id(),
    
            'verified_at' => now(),
    
            'verification_note' =>
                $this->remarks ?: null,
        ]);
    
        /*
         * Only human-corrected recognition becomes
         * HTR training material.
         */
        $field->refresh();
    
        if ($field->verification_status === 'CORRECTED') {
            $collector->collect(
                $field->load(
                    'extraction.item.document.currentVersion.files'
                ),
                auth()->id(),
            );
        }
    
        $this->decisions[$fieldId] =
            $field->verification_status;
    
        $this->resetErrorBag(
            "values.$fieldId"
        );
    }

    public function reject(int $fieldId): void
    {
        $field = $this->ownedField($fieldId);

        $field->update([
            'normalized_value' => null,
            'requires_verification' => false,
            'verification_status' => 'REJECTED',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'verification_note' => $this->remarks ?: 'Rejected during OCR/HTR verification.',
        ]);

        $this->values[$fieldId] = '';
        $this->decisions[$fieldId] = 'REJECTED';
    }

    public function apply(ApplyVerifiedExtraction $service): void
    {
        try {
            $service->apply($this->extraction->fresh(['fields', 'document', 'item']), auth()->id());
            session()->flash('success', 'Verified OCR/HTR values inserted into the document record.');
            $this->redirectRoute('speed.extractions.queue', navigate: true);
        } catch (\Throwable $e) {
            report($e);
            $this->addError('apply', $e->getMessage());
        }
    }

    private function ownedField(int $fieldId): DocumentExtractedField
    {
        return DocumentExtractedField::query()
            ->where('document_extraction_id', $this->extraction->id)
            ->findOrFail($fieldId);
    }

    public function render()
    {
        $file = $this->extraction->document?->currentVersion?->files
            ?->firstWhere('file_role', 'PREVIEW')
            ?? $this->extraction->document?->currentVersion?->files
                ?->firstWhere('file_role', 'OPTIMIZED')
            ?? $this->extraction->document?->currentVersion?->files
                ?->firstWhere('file_role', 'MASTER');

        $this->extraction->load('fields');

        return view('livewire.speed-digitization.review-extraction', [
            'file' => $file,
            'unresolved' => $this->extraction->fields
                ->whereIn('verification_status', ['PENDING', 'MISSING'])
                ->count(),
        ]);
    }

}
