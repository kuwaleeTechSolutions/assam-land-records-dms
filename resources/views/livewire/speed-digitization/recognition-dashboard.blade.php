<div wire:poll.15s>
    <div class="mb-4">
        <h1 class="h3 mb-1">Recognition Operations</h1>
        <p class="text-muted mb-0">OCR/HTR throughput, exceptions and training-data readiness.</p>
    </div>
    <div class="row g-3 mb-4">
        @foreach ([
            'batches' => 'Batches', 'documents' => 'Registered PDFs', 'recognizing' => 'Recognizing',
            'verification' => 'Needs Verification', 'failed' => 'Failed', 'training_samples' => 'Training Samples'
        ] as $key => $label)
            <div class="col-6 col-lg-2"><div class="card border-0 shadow-sm h-100"><div class="card-body">
                <div class="text-muted small">{{ $label }}</div><div class="h3 mb-0">{{ number_format($metrics[$key]) }}</div>
            </div></div></div>
        @endforeach
    </div>
    <div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table align-middle mb-0">
        <thead><tr><th>Field</th><th>Total</th><th>Auto accepted</th><th>Corrected</th><th>Missing</th><th>Avg confidence</th><th>Correction rate</th></tr></thead>
        <tbody>@forelse($fields as $f)<tr>
            <td class="fw-semibold">{{ str($f->field_name)->replace('_',' ')->title() }}</td>
            <td>{{ number_format($f->total) }}</td><td>{{ number_format($f->auto_accepted) }}</td>
            <td>{{ number_format($f->corrected) }}</td><td>{{ number_format($f->missing) }}</td>
            <td>{{ number_format((float)$f->avg_confidence,1) }}%</td>
            <td>{{ $f->total ? number_format(($f->corrected/$f->total)*100,1) : '0.0' }}%</td>
        </tr>@empty<tr><td colspan="7" class="text-center text-muted py-5">No recognition results yet.</td></tr>@endforelse</tbody>
    </table></div></div>
</div>
