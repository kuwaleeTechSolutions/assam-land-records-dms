<?php
namespace App\Livewire\SpeedDigitization;

use App\Models\DigitizationBatch;
use App\Models\DigitizationBatchItem;
use App\Models\DocumentExtractedField;
use App\Models\HtrTrainingSample;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class RecognitionDashboard extends Component
{
    public function mount(): void
    {
        abort_unless(app(\App\Domain\Authorization\Services\PermissionResolver::class)->allows(auth()->user(), 'reports.view'), 403);
    }

    public function render()
    {
        $metrics = [
            'batches' => DigitizationBatch::count(),
            'documents' => DigitizationBatchItem::whereNotNull('document_id')->count(),
            'recognizing' => DigitizationBatchItem::where('status', 'RECOGNIZING')->count(),
            'verification' => DigitizationBatchItem::where('status', 'VERIFICATION_REQUIRED')->count(),
            'failed' => DigitizationBatchItem::where('status', 'FAILED')->count(),
            'training_samples' => HtrTrainingSample::count(),
        ];

        $fields = DocumentExtractedField::query()
            ->selectRaw('field_name, COUNT(*) total')
            ->selectRaw("SUM(verification_status = 'AUTO_ACCEPTED') auto_accepted")
            ->selectRaw("SUM(verification_status = 'CORRECTED') corrected")
            ->selectRaw("SUM(verification_status = 'MISSING') missing")
            ->selectRaw('ROUND(AVG(confidence),2) avg_confidence')
            ->groupBy('field_name')
            ->orderBy('field_name')
            ->get();

        return view('livewire.speed-digitization.recognition-dashboard', compact('metrics','fields'));
    }
}
