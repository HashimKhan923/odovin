@extends('provider.layouts.app')
@section('title', isset($serviceRecord) ? 'Edit Service Record' : 'Add Service Record')

@section('content')
<style>
.pg { max-width:860px; margin:0 auto; padding:2rem; }
.back-link { display:inline-flex; align-items:center; gap:.5rem; color:var(--accent-cyan); text-decoration:none; font-size:.875rem; font-weight:600; margin-bottom:1.5rem; transition:all .3s; }
.back-link:hover { gap:.875rem; }
.page-title { font-family:'Orbitron',sans-serif; font-size:1.5rem; font-weight:800; margin-bottom:.35rem; }
.page-title span { color:var(--accent-cyan); }
.form-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:2rem; margin-bottom:1.5rem; }
.card-title { font-family:'Orbitron',sans-serif; font-size:.95rem; font-weight:700; margin-bottom:1.5rem; padding-bottom:.75rem; border-bottom:1px solid var(--border-color); }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; }
.form-grid.triple { grid-template-columns:1fr 1fr 1fr; }
.form-group { display:flex; flex-direction:column; gap:.5rem; }
.form-group.full { grid-column:1 / -1; }
.form-label { font-size:.8rem; font-weight:600; color:var(--text-secondary); }
.form-label .req { color:#ff8099; }
.form-input, .form-select, .form-textarea { padding:.75rem 1rem; background:var(--input-bg,rgba(0,212,255,.05)); border:1px solid var(--border-color); border-radius:10px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.875rem; transition:all .3s; width:100%; box-sizing:border-box; }
.form-input:focus, .form-select:focus, .form-textarea:focus { outline:none; border-color:var(--accent-cyan); box-shadow:0 0 0 3px rgba(0,212,255,.1); }
.form-select option { background:#121827; }
.form-textarea { resize:vertical; min-height:100px; }
.error-msg { font-size:.78rem; color:#ff8099; margin-top:.25rem; }
.hint { font-size:.75rem; color:var(--text-tertiary); margin-top:.25rem; }
.prefix-wrap { position:relative; }
.prefix-wrap .prefix { position:absolute; left:.875rem; top:50%; transform:translateY(-50%); color:var(--text-tertiary); font-weight:700; pointer-events:none; }
.prefix-wrap .form-input { padding-left:1.75rem; }
.btn-submit { display:inline-flex; align-items:center; gap:.5rem; padding:.875rem 2rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:12px; color:#000; font-family:'Orbitron',sans-serif; font-weight:700; font-size:.875rem; cursor:pointer; transition:all .3s; }
.btn-submit:hover { transform:translateY(-1px); box-shadow:0 4px 20px rgba(0,212,255,.4); }
.btn-cancel { display:inline-flex; align-items:center; gap:.5rem; padding:.875rem 1.5rem; background:transparent; border:1px solid var(--border-color); border-radius:12px; color:var(--text-secondary); font-size:.875rem; text-decoration:none; transition:all .3s; }
.btn-cancel:hover { border-color:var(--accent-cyan); color:var(--text-primary); }
.prefill-banner { background:rgba(0,212,255,.06); border:1px solid rgba(0,212,255,.2); border-radius:12px; padding:1rem 1.25rem; margin-bottom:1.5rem; font-size:.875rem; color:var(--text-secondary); display:flex; align-items:center; gap:.75rem; }
@media(max-width:640px) { .form-grid, .form-grid.triple { grid-template-columns:1fr; } }
</style>

<div class="pg">
    <a href="{{ route('provider.service-records.index') }}" class="back-link">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Service Records
    </a>

    <div class="page-title">
        {{ isset($serviceRecord) ? 'Edit' : 'Add' }} <span>Service Record</span>
    </div>
    <p style="color:var(--text-tertiary);font-size:.875rem;margin-top:.25rem;margin-bottom:1.5rem;">
        {{ isset($serviceRecord) ? 'Update the details of this service record.' : 'Log a completed service job for your records and revenue tracking.' }}
    </p>

    {{-- Pre-fill banner from job --}}
    @if(isset($prefill))
    <div class="prefill-banner">
        <svg width="20" height="20" fill="none" stroke="var(--accent-cyan)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Pre-filling from Job #{{ $prefill->job_number }} — {{ $prefill->service_type }} for {{ $prefill->vehicle->year }} {{ $prefill->vehicle->make }} {{ $prefill->vehicle->model }}
    </div>
    @endif

    @if(session('error'))
    <div style="background:rgba(255,51,102,.1);border:1px solid rgba(255,51,102,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:#ff8099;font-size:.875rem;">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ isset($serviceRecord) ? route('provider.service-records.update', $serviceRecord) : route('provider.service-records.store') }}">
        @csrf
        @if(isset($serviceRecord)) @method('PUT') @endif

        {{-- Vehicle Info --}}
        <div class="form-card">
            <div class="card-title">🚗 Vehicle Information</div>
            <div class="form-grid triple">
                <div class="form-group">
                    <label class="form-label">Make <span class="req">*</span></label>
                    <input type="text" name="vehicle_make" class="form-input"
                        placeholder="e.g. Toyota"
                        value="{{ old('vehicle_make', $prefill?->vehicle?->make ?? ($serviceRecord?->vehicle?->make ?? '')) }}" required>
                    @error('vehicle_make')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Model <span class="req">*</span></label>
                    <input type="text" name="vehicle_model" class="form-input"
                        placeholder="e.g. Camry"
                        value="{{ old('vehicle_model', $prefill?->vehicle?->model ?? ($serviceRecord?->vehicle?->model ?? '')) }}" required>
                    @error('vehicle_model')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Year <span class="req">*</span></label>
                    <input type="number" name="vehicle_year" class="form-input"
                        placeholder="e.g. 2022" min="1990" max="{{ date('Y') + 2 }}"
                        value="{{ old('vehicle_year', $prefill?->vehicle?->year ?? ($serviceRecord?->vehicle?->year ?? '')) }}" required>
                    @error('vehicle_year')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">License Plate</label>
                    <input type="text" name="vehicle_plate" class="form-input"
                        placeholder="Optional"
                        value="{{ old('vehicle_plate', $serviceRecord?->vehicle?->license_plate ?? '') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Mileage at Service</label>
                    <input type="number" name="mileage_at_service" class="form-input"
                        placeholder="e.g. 45000" min="0"
                        value="{{ old('mileage_at_service', $serviceRecord?->mileage_at_service ?? '') }}">
                </div>
                @if(isset($prefill))
                <input type="hidden" name="vehicle_id" value="{{ $prefill->vehicle_id }}">
                @endif
            </div>
        </div>

        {{-- Service Details --}}
        <div class="form-card">
            <div class="card-title">🔧 Service Details</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Service Type <span class="req">*</span></label>
                    <select name="service_type" class="form-select" required>
                        <option value="">Select type...</option>
                        @foreach(['Oil Change','Tire Rotation','Brake Service','Battery Replacement','Air Filter','Transmission Service','Engine Diagnostics','Wheel Alignment','Detailing','Full Inspection','AC Service','Coolant Flush','Spark Plugs','EV Battery Check','Windshield Repair','Towing','Other'] as $t)
                        <option value="{{ $t }}" {{ old('service_type', $prefill?->service_type ?? $serviceRecord?->service_type) === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                    @error('service_type')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Service Date <span class="req">*</span></label>
                    <input type="date" name="service_date" class="form-input"
                        value="{{ old('service_date', $serviceRecord?->service_date?->format('Y-m-d') ?? date('Y-m-d')) }}" required>
                    @error('service_date')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="form-group full">
                    <label class="form-label">Description <span class="req">*</span></label>
                    <textarea name="description" class="form-textarea" required placeholder="Describe the work performed...">{{ old('description', $prefill?->description ?? $serviceRecord?->description ?? '') }}</textarea>
                    @error('description')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Parts Replaced</label>
                    <input type="text" name="parts_replaced" class="form-input"
                        placeholder="e.g. Oil filter, Brake pads"
                        value="{{ old('parts_replaced', isset($serviceRecord?->parts_replaced) ? implode(', ', (array)$serviceRecord->parts_replaced) : '') }}">
                    <div class="hint">Separate multiple parts with commas</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Invoice Number</label>
                    <input type="text" name="invoice_number" class="form-input"
                        placeholder="Optional"
                        value="{{ old('invoice_number', $serviceRecord?->invoice_number ?? '') }}">
                </div>
                <div class="form-group full">
                    <label class="form-label">Additional Notes</label>
                    <textarea name="notes" class="form-textarea" style="min-height:80px;" placeholder="Any additional notes for your records...">{{ old('notes', $serviceRecord?->notes ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Cost & Next Service --}}
        <div class="form-card">
            <div class="card-title">💰 Cost & Next Service</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Cost Charged</label>
                    <div class="prefix-wrap">
                        <span class="prefix">$</span>
                        <input type="number" name="cost" class="form-input"
                            step="0.01" min="0" placeholder="0.00"
                            value="{{ old('cost', $prefill?->final_cost ?? $serviceRecord?->cost ?? '') }}">
                    </div>
                    @error('cost')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Next Service Date</label>
                    <input type="date" name="next_service_date" class="form-input"
                        value="{{ old('next_service_date', $serviceRecord?->next_service_date?->format('Y-m-d') ?? '') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Next Service Mileage</label>
                    <input type="number" name="next_service_mileage" class="form-input"
                        placeholder="e.g. 50000" min="0"
                        value="{{ old('next_service_mileage', $serviceRecord?->next_service_mileage ?? '') }}">
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div style="display:flex;gap:1rem;align-items:center;flex-wrap:wrap;">
            <button type="submit" class="btn-submit">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ isset($serviceRecord) ? 'Update Record' : 'Save Record' }}
            </button>
            <a href="{{ route('provider.service-records.index') }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
@endsection