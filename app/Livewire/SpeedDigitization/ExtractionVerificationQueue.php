<?php
namespace App\Livewire\SpeedDigitization;

use App\Models\DocumentExtraction;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ExtractionVerificationQueue extends Component
{
    use WithPagination;

    public string $status = 'STAGED';

    public function mount(): void
    {
        abort_unless(
            app(\App\Domain\Authorization\Services\PermissionResolver::class)
                ->allows(auth()->user(), 'documents.verify'),
            403,
        );
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $rows = DocumentExtraction::query()
            ->with([
                'document:id,ulid,reference_no,title,patta_no,dag_no,record_year',
                'item:id,ulid,digitization_batch_id,original_filename,status',
                'item.batch:id,ulid,batch_reference,village_id',
                'item.batch.village:id,name_en,name_as',
            ])
            ->withCount([
                'fields as unresolved_fields_count' => fn ($q) => $q
                    ->whereIn('verification_status', ['PENDING', 'MISSING']),
            ])
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->whereHas('fields', fn ($q) => $q
                ->whereIn('verification_status', ['PENDING', 'MISSING']))
            ->oldest()
            ->paginate(25);

        return view('livewire.speed-digitization.extraction-verification-queue', [
            'rows' => $rows,
        ]);
    }
}
