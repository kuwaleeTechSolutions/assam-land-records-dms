<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">OCR/HTR Verification</h1>
            <p class="text-muted mb-0">Review only missing or uncertain extracted fields.</p>
        </div>
        <a href="{{ route('speed.batches') }}" class="btn btn-outline-secondary">Digitization Batches</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <label class="form-label">Extraction status</label>
            <select wire:model.live="status" class="form-select" style="max-width: 260px">
                <option value="STAGED">Staged</option>
                <option value="APPLIED">Applied</option>
                <option value="">All unresolved</option>
            </select>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Document</th>
                        <th>Village / Batch</th>
                        <th>Engine</th>
                        <th>Confidence</th>
                        <th>Unresolved</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $row->document?->reference_no }}</div>
                                <div class="small text-muted">{{ $row->item?->original_filename }}</div>
                            </td>
                            <td>
                                {{ $row->item?->batch?->village?->name_en ?: '-' }}
                                <div class="small text-muted">{{ $row->item?->batch?->batch_reference }}</div>
                            </td>
                            <td>{{ $row->engine }}</td>
                            <td>{{ number_format((float) $row->overall_confidence, 1) }}%</td>
                            <td><span class="badge text-bg-warning">{{ $row->unresolved_fields_count }}</span></td>
                            <td>
                                <a href="{{ route('speed.extractions.review', $row) }}" class="btn btn-sm btn-primary">
                                    Verify
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">No unresolved OCR/HTR fields.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body">{{ $rows->links() }}</div>
    </div>
</div>
