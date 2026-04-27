@extends('provider.layouts.app')
@section('title', 'Complete Job #' . $job->job_number)

@section('content')
<style>
.pg { max-width:900px; margin:0 auto; padding:2rem; }
.back-link { display:inline-flex; align-items:center; gap:.5rem; color:var(--accent-cyan); text-decoration:none; font-size:.875rem; font-weight:600; margin-bottom:1.5rem; transition:all .3s; }
.back-link:hover { gap:.875rem; }
.page-header { margin-bottom:2rem; }
.job-num { font-family:'Orbitron',sans-serif; font-size:.8rem; color:var(--accent-cyan); margin-bottom:.375rem; }
.page-title { font-family:'Orbitron',sans-serif; font-size:1.5rem; font-weight:800; }
.page-title span { color:var(--accent-green); }
.page-sub { color:var(--text-tertiary); font-size:.875rem; margin-top:.375rem; }
.form-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:2rem; margin-bottom:1.5rem; position:relative; overflow:hidden; }
.form-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg, var(--accent-color, var(--accent-cyan)), transparent); }
.card-title { font-family:'Orbitron',sans-serif; font-size:.9rem; font-weight:700; margin-bottom:1.5rem; padding-bottom:.75rem; border-bottom:1px solid var(--border-color); display:flex; align-items:center; gap:.625rem; }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; }
.form-grid.triple { grid-template-columns:1fr 1fr 1fr; }
.form-group { display:flex; flex-direction:column; gap:.5rem; }
.form-group.full { grid-column:1 / -1; }
.form-label { font-size:.8rem; font-weight:600; color:var(--text-secondary); }
.req { color:#ff8099; }
.form-input, .form-select, .form-textarea { padding:.75rem 1rem; background:rgba(0,212,255,.05); border:1px solid var(--border-color); border-radius:10px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.875rem; transition:all .3s; width:100%; box-sizing:border-box; }
.form-input:focus, .form-select:focus, .form-textarea:focus { outline:none; border-color:var(--accent-cyan); box-shadow:0 0 0 3px rgba(0,212,255,.1); }
.form-select option { background:#121827; }
.form-textarea { resize:vertical; min-height:90px; }
.error-msg { font-size:.78rem; color:#ff8099; }
.hint { font-size:.75rem; color:var(--text-tertiary); }
.prefix-wrap { position:relative; }
.prefix-wrap .prefix { position:absolute; left:.875rem; top:50%; transform:translateY(-50%); color:var(--text-tertiary); font-weight:700; pointer-events:none; }
.prefix-wrap .form-input { padding-left:1.75rem; }
.job-strip { background:rgba(0,212,255,.05); border:1px solid rgba(0,212,255,.15); border-radius:12px; padding:1.25rem 1.5rem; margin-bottom:1.5rem; display:flex; flex-wrap:wrap; gap:1.5rem; }
.strip-item { display:flex; flex-direction:column; gap:.2rem; }
.strip-lbl { font-size:.72rem; color:var(--text-tertiary); text-transform:uppercase; letter-spacing:.05em; font-weight:600; }
.strip-val { font-size:.9rem; font-weight:700; color:var(--text-primary); }
.btn-complete { display:inline-flex; align-items:center; gap:.625rem; padding:1rem 2.5rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:12px; color:#000; font-family:'Orbitron',sans-serif; font-weight:800; font-size:.9rem; cursor:pointer; transition:all .3s; }
.btn-complete:hover { transform:translateY(-2px); box-shadow:0 6px 25px rgba(0,212,255,.5); }
.btn-back-link { display:inline-flex; align-items:center; gap:.5rem; padding:1rem 1.5rem; background:transparent; border:1px solid var(--border-color); border-radius:12px; color:var(--text-secondary); font-size:.875rem; text-decoration:none; transition:all .3s; }
.btn-back-link:hover { border-color:var(--accent-cyan); color:var(--text-primary); }

/* ── Photo evidence section ── */
.photo-section { background:var(--card-bg); border:1px solid rgba(0,212,255,.2); border-radius:16px; padding:2rem; margin-bottom:1.5rem; position:relative; overflow:hidden; }
.photo-section::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,#6366f1,var(--accent-cyan)); }
.photo-zone { border:2px dashed rgba(0,212,255,.2); border-radius:12px; padding:1.5rem; text-align:center; cursor:pointer; transition:all .3s; position:relative; min-height:120px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.5rem; }
.photo-zone:hover, .photo-zone.drag-over { border-color:var(--accent-cyan); background:rgba(0,212,255,.04); }
.photo-zone input[type=file] { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
.photo-zone-icon { font-size:2rem; }
.photo-zone-text { font-size:.82rem; color:var(--text-secondary); font-weight:600; }
.photo-zone-sub { font-size:.72rem; color:var(--text-tertiary); }
.photo-previews { display:grid; grid-template-columns:repeat(auto-fill,minmax(100px,1fr)); gap:.75rem; margin-top:1rem; }
.photo-preview-item { position:relative; border-radius:10px; overflow:hidden; aspect-ratio:1; background:rgba(0,0,0,.3); }
.photo-preview-item img { width:100%; height:100%; object-fit:cover; }
.photo-preview-remove { position:absolute; top:4px; right:4px; background:rgba(0,0,0,.7); border:none; border-radius:50%; width:22px; height:22px; color:#fff; font-size:.75rem; cursor:pointer; display:flex; align-items:center; justify-content:center; }
.photo-count-badge { display:inline-flex; align-items:center; gap:.3rem; padding:.2rem .6rem; background:rgba(0,212,255,.1); border:1px solid rgba(0,212,255,.2); border-radius:6px; font-size:.72rem; color:var(--accent-cyan); font-weight:700; margin-left:.5rem; }

/* ── Diagnostics section ── */
.diag-card { background:var(--card-bg); border:1px solid rgba(255,102,0,.25); border-radius:16px; padding:2rem; margin-bottom:1.5rem; position:relative; overflow:hidden; }
.diag-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,#ff6600,#ff3366); }
.diag-item { background:rgba(255,255,255,.03); border:1px solid var(--border-color); border-radius:12px; padding:1.25rem; margin-bottom:1rem; transition:border-color .3s; }
.diag-item:hover { border-color:rgba(255,102,0,.3); }
.diag-num { font-family:'Orbitron',sans-serif; font-size:.68rem; font-weight:700; color:var(--text-tertiary); letter-spacing:.08em; margin-bottom:.875rem; display:flex; align-items:center; justify-content:space-between; }
.btn-remove-diag { background:rgba(255,51,102,.1); border:1px solid rgba(255,51,102,.2); border-radius:8px; color:#ff8099; font-size:.75rem; padding:.3rem .7rem; cursor:pointer; transition:all .3s; }
.btn-remove-diag:hover { background:rgba(255,51,102,.2); }
.severity-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:.5rem; margin-top:.625rem; }
.sev-btn { padding:.5rem; border-radius:8px; border:1px solid var(--border-color); font-size:.72rem; font-weight:700; cursor:pointer; text-align:center; transition:all .25s; background:transparent; color:var(--text-tertiary); }
.sev-btn[data-sev="low"]      { border-color:rgba(0,255,170,.2); }
.sev-btn[data-sev="medium"]   { border-color:rgba(255,170,0,.2); }
.sev-btn[data-sev="high"]     { border-color:rgba(255,102,0,.2); }
.sev-btn[data-sev="critical"] { border-color:rgba(255,51,102,.2); }
.sev-btn.active[data-sev="low"]      { background:rgba(0,255,170,.12); color:#00ffaa; border-color:#00ffaa; }
.sev-btn.active[data-sev="medium"]   { background:rgba(255,170,0,.12); color:#ffaa00; border-color:#ffaa00; }
.sev-btn.active[data-sev="high"]     { background:rgba(255,102,0,.12); color:#ff6600; border-color:#ff6600; }
.sev-btn.active[data-sev="critical"] { background:rgba(255,51,102,.12); color:#ff3366; border-color:#ff3366; }
.safety-row { display:flex; align-items:center; gap:.75rem; padding:.75rem 1rem; background:rgba(255,51,102,.05); border:1px solid rgba(255,51,102,.2); border-radius:10px; cursor:pointer; transition:all .3s; user-select:none; margin-top:.75rem; }
.safety-row.active { background:rgba(255,51,102,.1); border-color:#ff3366; }
.cost-range { display:grid; grid-template-columns:1fr auto 1fr; gap:.75rem; align-items:center; }
.cost-sep { text-align:center; color:var(--text-tertiary); font-weight:600; font-size:.8rem; }
.btn-add-diag { display:inline-flex; align-items:center; gap:.625rem; padding:.75rem 1.25rem; background:rgba(255,102,0,.08); border:1px dashed rgba(255,102,0,.4); border-radius:10px; color:#ff9944; font-size:.825rem; font-weight:600; cursor:pointer; transition:all .3s; width:100%; justify-content:center; margin-top:.5rem; }
.btn-add-diag:hover { background:rgba(255,102,0,.15); border-color:#ff6600; }
.diag-empty { text-align:center; padding:1.75rem; color:var(--text-tertiary); font-size:.825rem; }

@media(max-width:640px) { .form-grid, .form-grid.triple { grid-template-columns:1fr; } .severity-grid { grid-template-columns:1fr 1fr; } .job-strip { flex-direction:column; gap:.875rem; } }
</style>

<div class="pg">
    <a href="{{ route('provider.jobs.work.show', $job) }}" class="back-link">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Job
    </a>

    <div class="page-header">
        <div class="job-num">JOB #{{ $job->job_number }}</div>
        <div class="page-title">Complete Job &amp; <span>Service Record</span></div>
        <div class="page-sub">Fill in the service details to complete this job. Everything saves in one step — vehicle mileage, expense, maintenance schedule, and any diagnostic findings all update automatically.</div>
    </div>

    @if($errors->any())
    <div style="background:rgba(255,51,102,.1);border:1px solid rgba(255,51,102,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:#ff8099;font-size:.875rem;">
        <strong>Please fix the following:</strong>
        <ul style="margin:.5rem 0 0 1.25rem;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Job summary strip --}}
    <div class="job-strip">
        <div class="strip-item"><div class="strip-lbl">Service</div><div class="strip-val">{{ $job->service_type }}</div></div>
        <div class="strip-item"><div class="strip-lbl">Vehicle</div><div class="strip-val">{{ $job->vehicle->year }} {{ $job->vehicle->make }} {{ $job->vehicle->model }}</div></div>
        <div class="strip-item"><div class="strip-lbl">Customer</div><div class="strip-val">{{ $job->user->name }}</div></div>
        @if($job->vehicle->current_mileage)
        <div class="strip-item"><div class="strip-lbl">Current Mileage</div><div class="strip-val">{{ number_format($job->vehicle->current_mileage) }} mi</div></div>
        @endif
        <div class="strip-item"><div class="strip-lbl">Your Offer</div><div class="strip-val" style="color:var(--accent-warning);">${{ number_format($offer->offered_price, 2) }}</div></div>
    </div>

    <form method="POST" action="{{ route('provider.jobs.work.complete-submit', $job) }}" enctype="multipart/form-data">
        @csrf

        {{-- Section 1: Completion --}}
        <div class="form-card" style="--accent-color:#00ffaa;">
            <div class="card-title">
                <svg width="18" height="18" fill="none" stroke="var(--accent-green)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Job Completion
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Final Cost Charged <span class="req">*</span></label>
                    <div class="prefix-wrap">
                        <span class="prefix">$</span>
                        <input type="number" name="final_cost" class="form-input" step="0.01" min="0.01"
                            placeholder="0.00" value="{{ old('final_cost', $job->final_cost ?? $offer->offered_price) }}" required>
                    </div>
                    @error('final_cost')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Service Date <span class="req">*</span></label>
                    <input type="date" name="service_date" class="form-input"
                        value="{{ old('service_date', $existingRecord?->service_date?->format('Y-m-d') ?? date('Y-m-d')) }}" required>
                    @error('service_date')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="form-group full">
                    <label class="form-label">Notes to Customer (optional)</label>
                    <textarea name="provider_notes" class="form-textarea" placeholder="Any notes for the customer about what was done...">{{ old('provider_notes', $job->provider_notes) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 2: Service Record --}}
        <div class="form-card" style="--accent-color:#00d4ff;">
            <div class="card-title">
                <svg width="18" height="18" fill="none" stroke="var(--accent-cyan)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Service Record Details
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Service Type <span class="req">*</span></label>
                    <select name="service_type" class="form-select" required>
                        @foreach(['Oil Change','Tire Rotation','Brake Service','Battery Replacement','Air Filter','Transmission Service','Engine Diagnostics','Wheel Alignment','Detailing','Full Inspection','AC Service','Coolant Flush','Spark Plugs','EV Battery Check','Windshield Repair','Towing','Other'] as $t)
                        <option value="{{ $t }}" {{ old('service_type', $existingRecord?->service_type ?? $job->service_type) === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                    @error('service_type')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Mileage at Service</label>
                    <input type="number" name="mileage_at_service" class="form-input"
                        placeholder="e.g. 45000" min="0"
                        value="{{ old('mileage_at_service', $existingRecord?->mileage_at_service ?? $job->vehicle?->current_mileage) }}">
                    <div class="hint">Updates vehicle's recorded mileage</div>
                </div>
                <div class="form-group full">
                    <label class="form-label">Work Description <span class="req">*</span></label>
                    <textarea name="description" class="form-textarea" required
                        placeholder="Describe exactly what was done...">{{ old('description', $existingRecord?->description ?? $job->description) }}</textarea>
                    @error('description')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Parts Replaced</label>
                    <input type="text" name="parts_replaced" class="form-input" placeholder="Oil filter, Brake pads"
                        value="{{ old('parts_replaced', isset($existingRecord?->parts_replaced) ? implode(', ', (array)$existingRecord->parts_replaced) : '') }}">
                    <div class="hint">Separate with commas</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Invoice Number</label>
                    <input type="text" name="invoice_number" class="form-input" placeholder="Optional"
                        value="{{ old('invoice_number', $existingRecord?->invoice_number) }}">
                </div>
                <div class="form-group full">
                    <label class="form-label">Internal Notes</label>
                    <textarea name="notes" class="form-textarea" style="min-height:70px;"
                        placeholder="Notes for your records only...">{{ old('notes', $existingRecord?->notes) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 3: Next Service --}}
        <div class="form-card" style="--accent-color:#a855f7;">
            <div class="card-title">
                <svg width="18" height="18" fill="none" stroke="#a855f7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Next Service Reminder
                <span style="font-size:.72rem;font-weight:400;color:var(--text-tertiary);margin-left:.5rem;">(optional)</span>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Next Service Date</label>
                    <input type="date" name="next_service_date" class="form-input"
                        value="{{ old('next_service_date', $existingRecord?->next_service_date?->format('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Next Service Mileage</label>
                    <input type="number" name="next_service_mileage" class="form-input" placeholder="e.g. 50000" min="0"
                        value="{{ old('next_service_mileage', $existingRecord?->next_service_mileage) }}">
                </div>
            </div>
            <div style="margin-top:.75rem;padding:.875rem 1rem;background:rgba(168,85,247,.06);border:1px solid rgba(168,85,247,.2);border-radius:10px;font-size:.8rem;color:var(--text-secondary);">
                💡 If a pending maintenance schedule exists for this service type it will be auto-completed and the next one set to your dates above.
            </div>
        </div>

        {{-- ── Section 4: Service Diagnostics ─────────────────────────────────── --}}
        <div class="diag-card">
            <div class="card-title">
                <svg width="18" height="18" fill="none" stroke="#ff6600" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                Service Diagnostics
                <span style="font-size:.72rem;font-weight:400;color:var(--text-tertiary);margin-left:auto;">Visible to customer &amp; future providers</span>
            </div>

            <div style="background:rgba(255,170,0,.06);border:1px solid rgba(255,170,0,.15);border-radius:10px;padding:.875rem 1rem;margin-bottom:1.5rem;font-size:.8rem;color:var(--text-secondary);display:flex;gap:.75rem;">
                <span style="flex-shrink:0;">💡</span>
                <span>Flag any issues you spotted beyond this service — worn parts, leaks, safety concerns. Helps the owner prioritise repairs and builds trust in your work.</span>
            </div>

            <div id="diagList">
                <div id="diagEmpty" class="diag-empty">
                    <div style="font-size:2rem;margin-bottom:.5rem;opacity:.4;">🔍</div>
                    No findings yet — click below to add one
                </div>
                <div id="diagRows"></div>
            </div>

            <button type="button" class="btn-add-diag" onclick="addDiag()">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Diagnostic Finding
            </button>
        </div>

        {{-- Submit --}}
        <div style="display:flex;gap:1rem;align-items:center;flex-wrap:wrap;">
    {{-- ── Before/After Photo Evidence ── --}}
    <div class="photo-section">
        <div class="card-title">
            📸 Before / After Photo Evidence
            <span style="font-size:.72rem;font-weight:400;color:var(--text-tertiary);margin-left:.5rem;">Optional — up to 6 photos per set · JPG, PNG, WebP · max 8MB each</span>
        </div>
        <div class="form-grid">
            {{-- Before photos --}}
            <div>
                <div class="form-label" style="margin-bottom:.75rem;">
                    Before Photos
                    <span id="beforeCount" class="photo-count-badge" style="display:none;">0 selected</span>
                </div>
                <div class="photo-zone" id="beforeZone">
                    <input type="file" name="before_photos[]" id="beforeInput"
                        accept="image/jpeg,image/png,image/webp" multiple
                        onchange="handlePhotos(this,'before')">
                    <div class="photo-zone-icon">📷</div>
                    <div class="photo-zone-text">Add before photos</div>
                    <div class="photo-zone-sub">Tap or drag & drop</div>
                </div>
                <div class="photo-previews" id="beforePreviews"></div>
            </div>

            {{-- After photos --}}
            <div>
                <div class="form-label" style="margin-bottom:.75rem;">
                    After Photos
                    <span id="afterCount" class="photo-count-badge" style="display:none;">0 selected</span>
                </div>
                <div class="photo-zone" id="afterZone">
                    <input type="file" name="after_photos[]" id="afterInput"
                        accept="image/jpeg,image/png,image/webp" multiple
                        onchange="handlePhotos(this,'after')">
                    <div class="photo-zone-icon">✅</div>
                    <div class="photo-zone-text">Add after photos</div>
                    <div class="photo-zone-sub">Show the completed work</div>
                </div>
                <div class="photo-previews" id="afterPreviews"></div>
            </div>
        </div>

        {{-- Evidence notes --}}
        <div class="form-group" style="margin-top:1.25rem;">
            <label class="form-label" for="evidence_notes">Evidence Notes (optional)</label>
            <textarea name="evidence_notes" id="evidence_notes" class="form-textarea"
                placeholder="Describe what the photos show — e.g. 'Before: corroded brake pads. After: new OEM pads installed, rotor resurfaced.'"
                maxlength="1000">{{ old('evidence_notes') }}</textarea>
        </div>

        <div style="margin-top:1rem;padding:.875rem 1rem;background:rgba(0,212,255,.04);border:1px solid rgba(0,212,255,.1);border-radius:10px;font-size:.78rem;color:var(--text-tertiary);line-height:1.6;">
            💡 <strong style="color:var(--text-secondary);">Why add photos?</strong>
            Before/after evidence builds consumer trust, protects you in disputes, and is shown on the service history record. The consumer sees your photos when reviewing the completed job.
        </div>
    </div>

            <button type="submit" class="btn-complete">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Complete Job &amp; Save Record
            </button>
            <a href="{{ route('provider.jobs.work.show', $job) }}" class="btn-back-link">Cancel</a>
        </div>

        <div style="margin-top:1.25rem;padding:1rem 1.25rem;background:rgba(0,255,170,.05);border:1px solid rgba(0,255,170,.15);border-radius:10px;font-size:.8rem;color:var(--text-secondary);">
            ✅ On submit: job marked complete · service record saved · vehicle mileage updated · expense logged · maintenance schedule updated · diagnostic findings saved &amp; customer notified
        </div>
    </form>
</div>

<script>
let diagCount = 0;

function addDiag() {
    diagCount++;
    document.getElementById('diagEmpty').style.display = 'none';
    const n = diagCount;

    const html = `
    <div class="diag-item" id="diag-${n}">
        <div class="diag-num">
            FINDING #${n}
            <button type="button" class="btn-remove-diag" onclick="removeDiag(${n})">✕ Remove</button>
        </div>
        <div class="form-grid" style="margin-bottom:1rem;">
            <div class="form-group full">
                <label class="form-label">Title <span class="req">*</span></label>
                <input type="text" name="diagnostics[${n}][title]" class="form-input" required
                    placeholder="e.g. Worn front brake pads, Oil leak near valve cover...">
            </div>
            <div class="form-group">
                <label class="form-label">Category</label>
                <select name="diagnostics[${n}][category]" class="form-select">
                    <option value="other">🔧 Other</option>
                    <option value="brakes">🛑 Brakes</option>
                    <option value="engine">⚙️ Engine</option>
                    <option value="transmission">🔄 Transmission</option>
                    <option value="suspension">🔩 Suspension</option>
                    <option value="electrical">⚡ Electrical</option>
                    <option value="tires">🔵 Tires</option>
                    <option value="body">🚗 Body</option>
                    <option value="fluids">💧 Fluids</option>
                    <option value="cooling">❄️ Cooling</option>
                    <option value="exhaust">💨 Exhaust</option>
                    <option value="safety">🛡️ Safety</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Location on Vehicle</label>
                <input type="text" name="diagnostics[${n}][location]" class="form-input"
                    placeholder="e.g. Front left, Engine bay...">
            </div>
            <div class="form-group full">
                <label class="form-label">Description <span class="req">*</span></label>
                <textarea name="diagnostics[${n}][description]" class="form-textarea" required
                    placeholder="What you observed, how severe it is, what could happen if ignored..."></textarea>
            </div>
        </div>

        <div style="margin-bottom:1rem;">
            <label class="form-label">Severity</label>
            <div class="severity-grid">
                <button type="button" class="sev-btn" data-sev="low" data-n="${n}" onclick="setSev(${n},'low')">🟢 Low<br><span style="font-size:.62rem;opacity:.7;">Monitor</span></button>
                <button type="button" class="sev-btn active" data-sev="medium" data-n="${n}" onclick="setSev(${n},'medium')">🟡 Medium<br><span style="font-size:.62rem;opacity:.7;">Fix soon</span></button>
                <button type="button" class="sev-btn" data-sev="high" data-n="${n}" onclick="setSev(${n},'high')">🟠 High<br><span style="font-size:.62rem;opacity:.7;">Promptly</span></button>
                <button type="button" class="sev-btn" data-sev="critical" data-n="${n}" onclick="setSev(${n},'critical')">🔴 Critical<br><span style="font-size:.62rem;opacity:.7;">Urgent</span></button>
            </div>
            <input type="hidden" name="diagnostics[${n}][severity]" id="sev-${n}" value="medium">
        </div>

        <label class="safety-row" id="safety-row-${n}" onclick="toggleSafety(${n})">
            <input type="hidden" name="diagnostics[${n}][is_safety_critical]" id="safety-${n}" value="0">
            <span style="font-size:1.1rem;">⚠️</span>
            <div>
                <div style="font-weight:700;font-size:.8rem;color:#ff8099;">Safety Critical</div>
                <div style="font-size:.73rem;color:var(--text-tertiary);">Vehicle may be unsafe to drive — customer alerted immediately</div>
            </div>
            <div style="margin-left:auto;width:18px;height:18px;border-radius:50%;border:2px solid rgba(255,51,102,.4);background:transparent;flex-shrink:0;transition:all .3s;" id="safety-dot-${n}"></div>
        </label>

        <div style="margin-top:.875rem;">
            <label class="form-label">Estimated Repair Cost <span style="font-weight:400;opacity:.7;">(optional)</span></label>
            <div class="cost-range" style="margin-top:.5rem;">
                <div class="prefix-wrap"><span class="prefix">$</span><input type="number" name="diagnostics[${n}][estimated_cost_min]" class="form-input" placeholder="Min" min="0" step="10"></div>
                <div class="cost-sep">to</div>
                <div class="prefix-wrap"><span class="prefix">$</span><input type="number" name="diagnostics[${n}][estimated_cost_max]" class="form-input" placeholder="Max" min="0" step="10"></div>
            </div>
            <div class="hint">Gives the customer a ballpark before seeking quotes</div>
        </div>
    </div>`;

    document.getElementById('diagRows').insertAdjacentHTML('beforeend', html);
}

function removeDiag(n) {
    document.getElementById(`diag-${n}`)?.remove();
    if (!document.querySelectorAll('#diagRows .diag-item').length) {
        document.getElementById('diagEmpty').style.display = '';
    }
}

function setSev(n, val) {
    document.getElementById(`sev-${n}`).value = val;
    document.querySelectorAll(`.sev-btn[data-n="${n}"]`).forEach(b => {
        b.classList.toggle('active', b.dataset.sev === val);
    });
}

function toggleSafety(n) {
    const row = document.getElementById(`safety-row-${n}`);
    const dot = document.getElementById(`safety-dot-${n}`);
    const inp = document.getElementById(`safety-${n}`);
    const on  = row.classList.toggle('active');
    inp.value = on ? '1' : '0';
    dot.style.background   = on ? '#ff3366' : 'transparent';
    dot.style.borderColor  = on ? '#ff3366' : 'rgba(255,51,102,.4)';
}
</script>
<script>
// ── Photo evidence upload handling ──────────────────────────────────────────
const photoFiles = { before: [], after: [] };

function handlePhotos(input, side) {
    const files = Array.from(input.files);
    const maxFiles = 6;

    if (files.length + photoFiles[side].length > maxFiles) {
        alert('Maximum ' + maxFiles + ' photos per set.');
        input.value = '';
        return;
    }

    photoFiles[side] = [...photoFiles[side], ...files];
    renderPreviews(side);
    updateCount(side);
}

function renderPreviews(side) {
    const container = document.getElementById(side + 'Previews');
    container.innerHTML = '';

    photoFiles[side].forEach((file, idx) => {
        const reader = new FileReader();
        reader.onload = e => {
            const item = document.createElement('div');
            item.className = 'photo-preview-item';
            item.innerHTML = `
                <img src="${e.target.result}" alt="preview">
                <button type="button" class="photo-preview-remove"
                    onclick="removePhoto('${side}',${idx})">✕</button>`;
            container.appendChild(item);
        };
        reader.readAsDataURL(file);
    });
}

function removePhoto(side, idx) {
    photoFiles[side].splice(idx, 1);
    rebuildFileInput(side);
    renderPreviews(side);
    updateCount(side);
}

function rebuildFileInput(side) {
    // Rebuild the file input with remaining files using DataTransfer
    const input = document.getElementById(side + 'Input');
    const dt = new DataTransfer();
    photoFiles[side].forEach(f => dt.items.add(f));
    input.files = dt.files;
}

function updateCount(side) {
    const badge = document.getElementById(side + 'Count');
    const n = photoFiles[side].length;
    badge.style.display = n > 0 ? 'inline-flex' : 'none';
    badge.textContent = n + ' photo' + (n !== 1 ? 's' : '');
}

// Drag-over styling
['before','after'].forEach(side => {
    const zone = document.getElementById(side + 'Zone');
    if (!zone) return;
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
    zone.addEventListener('drop', () => zone.classList.remove('drag-over'));
});
</script>

@endsection