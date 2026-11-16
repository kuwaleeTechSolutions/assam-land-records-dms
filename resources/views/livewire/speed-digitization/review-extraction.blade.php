<div>
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h1 class="h4 mb-1">Verify OCR/HTR Extraction</h1>
            <div class="text-muted">
                {{ $extraction->document?->reference_no }} ·
                {{ $extraction->document?->village?->name_en }} ·
                {{ number_format((float) $extraction->overall_confidence, 1) }}% overall confidence
            </div>
        </div>
        <a href="{{ route('speed.extractions.queue') }}" class="btn btn-outline-secondary">Back to queue</a>
    </div>

    @error('apply')<div class="alert alert-danger">{{ $message }}</div>@enderror

    <div class="row g-3">
        <div class="col-xl-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @if ($file)
                        <iframe
                            src="{{ route('documents.file.preview', $extraction->document) }}#toolbar=1"
                            style="height: 78vh; width: 100%; border: 0"
                            title="Source document"
                        ></iframe>
                    @else
                        <div class="p-5 text-center text-muted">No preview file is available.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h6 text-uppercase text-muted mb-0">Extracted fields</h2>
                        <span class="badge {{ $unresolved ? 'text-bg-warning' : 'text-bg-success' }}">
                            {{ $unresolved }} unresolved
                        </span>
                    </div>

                    @foreach ($extraction->fields as $field)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <strong>{{ str($field->field_name)->replace('_', ' ')->title() }}</strong>
                                <span class="badge {{ (float) $field->confidence >= 97 ? 'text-bg-success' : ((float) $field->confidence >= 85 ? 'text-bg-warning' : 'text-bg-danger') }}">
                                    {{ number_format((float) $field->confidence, 1) }}%
                                </span>
                            </div>

                            <div class="small text-muted mb-1">Recognized source</div>
                            <div class="bg-light rounded p-2 mb-2" lang="as">{{ $field->source_value ?: 'Not detected' }}</div>

                            <label class="form-label">Database value</label>
                            <input wire:model="values.{{ $field->id }}" class="form-control" lang="as">
                            @error('values.'.$field->id)<div class="text-danger small">{{ $message }}</div>@enderror

                            <div class="d-flex gap-2 mt-2">
                                <button wire:click="accept({{ $field->id }})" class="btn btn-sm btn-success">Confirm / Correct</button>
                                <button wire:click="reject({{ $field->id }})" class="btn btn-sm btn-outline-danger">Not available</button>
                            </div>

                            @if (! empty($decisions[$field->id]))
                                <div class="small text-success mt-2">Saved: {{ $decisions[$field->id] }}</div>
                            @endif
                        </div>
                    @endforeach

                    <label class="form-label">Verifier remarks</label>
                    <textarea wire:model="remarks" class="form-control" rows="3"></textarea>

                    <button wire:click="apply" class="btn btn-primary w-100 mt-3" @disabled($unresolved > 0)>
                        Insert verified values into database
                    </button>
                    @if ($unresolved > 0)
                        <div class="small text-muted mt-2">Resolve every missing or uncertain field before insertion.</div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted">Batch geography</h2>
                    <dl class="mb-0">
                        <dt>District</dt><dd>{{ $extraction->document?->district?->name_en ?: '-' }}</dd>
                        <dt>LAC</dt><dd>{{ $extraction->document?->lac?->name_en ?: '-' }}</dd>
                        <dt>Circle</dt><dd>{{ $extraction->document?->revenueCircle?->name_en ?: '-' }}</dd>
                        <dt>Mouza</dt><dd>{{ $extraction->document?->mouza?->name_en ?: '-' }}</dd>
                        <dt>Village</dt><dd>{{ $extraction->document?->village?->name_en ?: '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
