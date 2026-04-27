@extends('provider.layouts.app')
@section('title', isset($serviceRecord) ? 'Edit Service Record' : 'Add Service Record')

@section('content')
<style>
:root[data-theme="dark"] {
    --card-bg:rgba(26,32,48,.85); --border-color:rgba(0,212,255,.1);
    --input-bg:rgba(0,212,255,.05); --text-primary:#fff;
    --text-secondary:rgba(255,255,255,.7); --text-tertiary:rgba(255,255,255,.45);
    --accent-cyan:#00d4ff; --accent-green:#00ffaa; --accent-warning:#ffaa00;
}
:root[data-theme="light"] {
    --card-bg:rgba(255,255,255,.9); --border-color:rgba(0,0,0,.1);
    --input-bg:rgba(0,0,0,.03); --text-primary:#1a1f36;
    --text-secondary:rgba(26,31,54,.7); --text-tertiary:rgba(26,31,54,.45);
    --accent-cyan:#0066ff; --accent-green:#00cc88; --accent-warning:#ff9500;
}
.pg { max-width:900px; margin:0 auto; padding:2rem; }
.back-link { display:inline-flex; align-items:center; gap:.5rem; color:var(--accent-cyan); text-decoration:none; font-size:.875rem; font-weight:600; margin-bottom:1.5rem; transition:all .3s; }
.back-link:hover { gap:.875rem; }
.page-title { font-family:'Orbitron',sans-serif; font-size:1.5rem; font-weight:800; margin-bottom:.35rem; }
.page-title span { color:var(--accent-cyan); }
.form-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:2rem; margin-bottom:1.5rem; position:relative; overflow:hidden; }
.form-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,var(--accent-cyan),var(--accent-green)); }
.card-title { font-family:'Orbitron',sans-serif; font-size:.9rem; font-weight:700; margin-bottom:1.5rem; padding-bottom:.75rem; border-bottom:1px solid var(--border-color); color:var(--text-primary); display:flex; align-items:center; gap:.625rem; }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; }
.form-grid.triple { grid-template-columns:1fr 1fr 1fr; }
.form-group { display:flex; flex-direction:column; gap:.5rem; }
.form-group.full { grid-column:1/-1; }
.form-label { font-size:.8rem; font-weight:600; color:var(--text-secondary); }
.form-label .req { color:#ff8099; }
.form-input,.form-select,.form-textarea { padding:.75rem 1rem; background:var(--input-bg); border:1px solid var(--border-color); border-radius:10px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.875rem; transition:all .3s; width:100%; box-sizing:border-box; }
.form-input:focus,.form-select:focus,.form-textarea:focus { outline:none; border-color:var(--accent-cyan); box-shadow:0 0 0 3px rgba(0,212,255,.1); }
.form-select option { background:#121827; }
.form-textarea { resize:vertical; min-height:90px; }
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

/* ── Issues section ── */
.issues-card { background:var(--card-bg); border:1px solid rgba(255,102,0,.25); border-radius:16px; padding:2rem; margin-bottom:1.5rem; position:relative; overflow:hidden; }
.issues-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,#ff6600,#ff3366); }
.issue-item { background:rgba(255,255,255,.03); border:1px solid var(--border-color); border-radius:12px; padding:1.25rem; margin-bottom:1rem; position:relative; transition:border-color .3s; }
.issue-item:hover { border-color:rgba(255,102,0,.3); }
.issue-item-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem; }
.issue-num { font-family:'Orbitron',sans-serif; font-size:.7rem; font-weight:700; color:var(--text-tertiary); letter-spacing:.08em; }
.btn-remove-issue { background:rgba(255,51,102,.1); border:1px solid rgba(255,51,102,.2); border-radius:8px; color:#ff8099; font-size:.75rem; padding:.3rem .7rem; cursor:pointer; transition:all .3s; }
.btn-remove-issue:hover { background:rgba(255,51,102,.2); border-color:#ff3366; }
.severity-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:.5rem; }
.severity-btn { padding:.5rem; border-radius:8px; border:1px solid var(--border-color); font-size:.75rem; font-weight:700; cursor:pointer; text-align:center; transition:all .25s; background:transparent; color:var(--text-tertiary); }
.severity-btn[data-severity="low"]      { border-color:rgba(0,255,170,.2); }
.severity-btn[data-severity="medium"]   { border-color:rgba(255,170,0,.2); }
.severity-btn[data-severity="high"]     { border-color:rgba(255,102,0,.2); }
.severity-btn[data-severity="critical"] { border-color:rgba(255,51,102,.2); }
.severity-btn.active[data-severity="low"]      { background:rgba(0,255,170,.12); color:#00ffaa; border-color:#00ffaa; }
.severity-btn.active[data-severity="medium"]   { background:rgba(255,170,0,.12); color:#ffaa00; border-color:#ffaa00; }
.severity-btn.active[data-severity="high"]     { background:rgba(255,102,0,.12); color:#ff6600; border-color:#ff6600; }
.severity-btn.active[data-severity="critical"] { background:rgba(255,51,102,.12); color:#ff3366; border-color:#ff3366; }
.safety-toggle { display:flex; align-items:center; gap:.75rem; padding:.75rem 1rem; background:rgba(255,51,102,.05); border:1px solid rgba(255,51,102,.2); border-radius:10px; cursor:pointer; transition:all .3s; user-select:none; }
.safety-toggle.active { background:rgba(255,51,102,.1); border-color:#ff3366; }
.safety-toggle input { display:none; }
.safety-icon { font-size:1.25rem; transition:transform .3s; }
.safety-toggle.active .safety-icon { transform:scale(1.15); }
.cost-range-row { display:grid; grid-template-columns:1fr auto 1fr; gap:.75rem; align-items:center; }
.cost-sep { text-align:center; color:var(--text-tertiary); font-weight:600; font-size:.8rem; }
.btn-add-issue { display:inline-flex; align-items:center; gap:.625rem; padding:.75rem 1.25rem; background:rgba(255,102,0,.08); border:1px dashed rgba(255,102,0,.4); border-radius:10px; color:#ff9944; font-size:.825rem; font-weight:600; cursor:pointer; transition:all .3s; width:100%; justify-content:center; margin-top:.5rem; }
.btn-add-issue:hover { background:rgba(255,102,0,.15); border-color:#ff6600; }
.empty-issues { text-align:center; padding:2rem; color:var(--text-tertiary); font-size:.875rem; }
.empty-issues .icon { font-size:2.5rem; margin-bottom:.75rem; opacity:.5; }
@media(max-width:640px) { .form-grid,.form-grid.triple { grid-template-columns:1fr; } .severity-grid { grid-template-columns:1fr 1fr; } }
</style>

<div class="pg">
    <a href="{{ route('provider.service-records.index') }}" class="back-link">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Service Records
    </a>

    <div class="page-title">{{ isset($serviceRecord) ? 'Edit' : 'Add' }} <span>Service Record</span></div>
    <p style="color:var(--text-tertiary);font-size:.875rem;margin-bottom:1.5rem;">
        {{ isset($serviceRecord) ? 'Update this service record and any flagged service diagnostics.' : 'Log the completed service and flag any issues you spotted during inspection.' }}
    </p>

    @if(isset($prefill))
    <div class="prefill-banner">
        <svg width="20" height="20" fill="none" stroke="var(--accent-cyan)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Pre-filling from Job #{{ $prefill->job_number }} — {{ $prefill->service_type }} for {{ $prefill->vehicle->year }} {{ $prefill->vehicle->make }} {{ $prefill->vehicle->model }}
    </div>
    @endif

    @if(session('error'))
    <div style="background:rgba(255,51,102,.1);border:1px solid rgba(255,51,102,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:#ff8099;font-size:.875rem;">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ isset($serviceRecord) ? route('provider.service-records.update', $serviceRecord) : route('provider.service-records.store') }}" id="mainForm" enctype="multipart/form-data">
        @csrf
        @if(isset($serviceRecord)) @method('PUT') @endif

        {{-- Vehicle Info --}}
        <div class="form-card">
            <div class="card-title">🚗 Vehicle Information</div>
            <div class="form-grid triple">
                <div class="form-group">
                    <label class="form-label">Make <span class="req">*</span></label>
                    <input type="text" name="vehicle_make" class="form-input" placeholder="Toyota"
                        value="{{ old('vehicle_make', $prefill?->vehicle?->make ?? $serviceRecord?->vehicle?->make ?? '') }}" required>
                    @error('vehicle_make')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Model <span class="req">*</span></label>
                    <input type="text" name="vehicle_model" class="form-input" placeholder="Camry"
                        value="{{ old('vehicle_model', $prefill?->vehicle?->model ?? $serviceRecord?->vehicle?->model ?? '') }}" required>
                    @error('vehicle_model')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Year <span class="req">*</span></label>
                    <input type="number" name="vehicle_year" class="form-input" placeholder="2022" min="1990" max="{{ date('Y') + 2 }}"
                        value="{{ old('vehicle_year', $prefill?->vehicle?->year ?? $serviceRecord?->vehicle?->year ?? '') }}" required>
                    @error('vehicle_year')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">License Plate</label>
                    <input type="text" name="vehicle_plate" class="form-input" placeholder="Optional"
                        value="{{ old('vehicle_plate', $serviceRecord?->vehicle?->license_plate ?? '') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Mileage at Service</label>
                    <input type="number" name="mileage_at_service" class="form-input" placeholder="45000" min="0"
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
                    <label class="form-label">Work Performed <span class="req">*</span></label>
                    <textarea name="description" class="form-textarea" required placeholder="Describe exactly what was done...">{{ old('description', $prefill?->description ?? $serviceRecord?->description ?? '') }}</textarea>
                    @error('description')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Parts Replaced</label>
                    <input type="text" name="parts_replaced" class="form-input" placeholder="Oil filter, Air filter..."
                        value="{{ old('parts_replaced', isset($serviceRecord?->parts_replaced) ? implode(', ', (array)$serviceRecord->parts_replaced) : '') }}">
                    <div class="hint">Separate with commas</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Invoice Number</label>
                    <input type="text" name="invoice_number" class="form-input" placeholder="Optional"
                        value="{{ old('invoice_number', $serviceRecord?->invoice_number ?? '') }}">
                </div>
                <div class="form-group full">
                    <label class="form-label">Additional Notes</label>
                    <textarea name="notes" class="form-textarea" style="min-height:75px;" placeholder="Any notes for your records...">{{ old('notes', $serviceRecord?->notes ?? '') }}</textarea>
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
                        <input type="number" name="cost" class="form-input" step="0.01" min="0" placeholder="0.00"
                            value="{{ old('cost', $prefill?->final_cost ?? $serviceRecord?->cost ?? '') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Next Service Date</label>
                    <input type="date" name="next_service_date" class="form-input"
                        value="{{ old('next_service_date', $serviceRecord?->next_service_date?->format('Y-m-d') ?? '') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Next Service Mileage</label>
                    <input type="number" name="next_service_mileage" class="form-input" placeholder="50000" min="0"
                        value="{{ old('next_service_mileage', $serviceRecord?->next_service_mileage ?? '') }}">
                </div>
            </div>
        </div>

        {{-- ── Service Diagnostics Found ────────────────────────────────────────── --}}
        <div class="issues-card">
            <div class="card-title">
                <svg width="18" height="18" fill="none" stroke="#ff6600" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Issues Found During Inspection
                <span style="margin-left:auto;font-family:'Chakra Petch',sans-serif;font-size:.72rem;font-weight:400;color:var(--text-tertiary);">Visible to consumer & other providers</span>
            </div>

            <div style="background:rgba(255,170,0,.06);border:1px solid rgba(255,170,0,.15);border-radius:10px;padding:.875rem 1rem;margin-bottom:1.5rem;font-size:.8rem;color:var(--text-secondary);display:flex;gap:.75rem;align-items:flex-start;">
                <span style="font-size:1rem;flex-shrink:0;">💡</span>
                <div>Flag any issues you noticed beyond the scope of this service — worn parts, leaks, warning signs, safety concerns. This helps the vehicle owner prioritize future repairs and builds trust.</div>
            </div>

            <div id="issuesList">
                {{-- Existing issues on edit --}}
                @if(isset($serviceRecord) && $serviceRecord->serviceDiagnostics->isNotEmpty())
                    @foreach($serviceRecord->serviceDiagnostics as $i => $issue)
                    <div class="issue-item" id="issue-row-existing-{{ $issue->id }}">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.875rem;">
                            <span class="issue-num">ISSUE #{{ $i + 1 }}</span>
                            <div style="display:flex;align-items:center;gap:.75rem;">
                                @php
                                    $statusColors = ['open'=>'#ff3366','acknowledged'=>'#ffaa00','in_progress'=>'#00d4ff','monitoring'=>'#aa88ff','resolved'=>'#00ffaa','ignored'=>'#555577'];
                                    $sc = $statusColors[$issue->status] ?? '#888';
                                @endphp
                                <span style="font-size:.7rem;padding:.2rem .6rem;border-radius:6px;background:rgba(0,0,0,.3);border:1px solid {{ $sc }};color:{{ $sc }};">{{ $issue->status_label }}</span>
                                <span style="font-size:.75rem;color:var(--text-tertiary);">Logged {{ $issue->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div style="font-size:.825rem;color:var(--text-secondary);">
                            <strong style="color:var(--text-primary);">{{ $issue->category_icon }} {{ $issue->title }}</strong>
                            <span style="margin-left:.5rem;font-size:.75rem;padding:.1rem .5rem;border-radius:5px;background:{{ $issue->severity_bg }};color:{{ $issue->severity_color }};">{{ ucfirst($issue->severity) }}</span>
                        </div>
                    </div>
                    @endforeach
                    <hr style="border:none;border-top:1px solid var(--border-color);margin:1rem 0;">
                    <p style="font-size:.78rem;color:var(--text-tertiary);margin-bottom:1rem;">Add new issues below (existing ones above can be managed from the Issues page):</p>
                @endif

                {{-- New issue rows added via JS --}}
                <div id="newIssueRows"></div>
            </div>

            <div id="emptyState" class="empty-issues" style="{{ (isset($serviceRecord) && $serviceRecord->serviceDiagnostics->isNotEmpty()) ? 'display:none;' : '' }}">
                <div class="icon">🔍</div>
                <div style="font-weight:600;color:var(--text-secondary);margin-bottom:.35rem;">No issues flagged yet</div>
                <div>Click the button below to log a service diagnostic you spotted</div>
            </div>

            <button type="button" class="btn-add-issue" onclick="addIssueRow()">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Issue Found
            </button>
        </div>

        {{-- Before/After Photo Evidence --}}
        <div style="background:var(--card-bg);border:1px solid rgba(0,212,255,.2);border-radius:16px;padding:1.75rem;margin-bottom:1.5rem;position:relative;overflow:hidden;">
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#6366f1,var(--accent-cyan));"></div>
            <div style="font-family:'Orbitron',sans-serif;font-size:.875rem;font-weight:700;margin-bottom:.375rem;">📸 Before / After Photos</div>
            <div style="font-size:.78rem;color:var(--text-tertiary);margin-bottom:1.5rem;">Optional — up to 6 photos per set · JPG, PNG, WebP · max 8MB each</div>

            @if(isset($serviceRecord) && $serviceRecord->hasEvidence())
            <div style="background:rgba(0,255,170,.06);border:1px solid rgba(0,255,170,.2);border-radius:10px;padding:.875rem 1rem;margin-bottom:1.25rem;font-size:.8rem;color:var(--accent-green);">
                ✓ This record already has {{ count($serviceRecord->before_photos ?? []) }} before and {{ count($serviceRecord->after_photos ?? []) }} after photo(s). New uploads will be added to the existing ones.
            </div>
            @endif

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                <div>
                    <div style="font-size:.78rem;font-weight:700;color:var(--text-secondary);margin-bottom:.625rem;display:flex;align-items:center;gap:.5rem;">
                        <span style="width:8px;height:8px;border-radius:50%;background:#ff8099;display:inline-block;"></span>
                        Before Photos
                        <span id="srBeforeCount" style="display:none;font-size:.7rem;background:rgba(0,212,255,.1);border:1px solid rgba(0,212,255,.2);color:var(--accent-cyan);padding:.1rem .5rem;border-radius:6px;font-weight:700;"></span>
                    </div>
                    <div style="border:2px dashed rgba(0,212,255,.2);border-radius:10px;padding:1.25rem;text-align:center;cursor:pointer;position:relative;transition:all .3s;" id="srBeforeZone">
                        <input type="file" name="before_photos[]" id="srBeforeInput" accept="image/jpeg,image/png,image/webp" multiple style="position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;" onchange="srHandlePhotos(this,'before')">
                        <div style="font-size:1.5rem;margin-bottom:.35rem;">📷</div>
                        <div style="font-size:.78rem;color:var(--text-secondary);font-weight:600;">Before photos</div>
                        <div style="font-size:.7rem;color:var(--text-tertiary);">Tap or drag & drop</div>
                    </div>
                    <div id="srBeforePreviews" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(70px,1fr));gap:.5rem;margin-top:.625rem;"></div>
                </div>
                <div>
                    <div style="font-size:.78rem;font-weight:700;color:var(--text-secondary);margin-bottom:.625rem;display:flex;align-items:center;gap:.5rem;">
                        <span style="width:8px;height:8px;border-radius:50%;background:var(--accent-green);display:inline-block;"></span>
                        After Photos
                        <span id="srAfterCount" style="display:none;font-size:.7rem;background:rgba(0,212,255,.1);border:1px solid rgba(0,212,255,.2);color:var(--accent-cyan);padding:.1rem .5rem;border-radius:6px;font-weight:700;"></span>
                    </div>
                    <div style="border:2px dashed rgba(0,212,255,.2);border-radius:10px;padding:1.25rem;text-align:center;cursor:pointer;position:relative;transition:all .3s;" id="srAfterZone">
                        <input type="file" name="after_photos[]" id="srAfterInput" accept="image/jpeg,image/png,image/webp" multiple style="position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;" onchange="srHandlePhotos(this,'after')">
                        <div style="font-size:1.5rem;margin-bottom:.35rem;">✅</div>
                        <div style="font-size:.78rem;color:var(--text-secondary);font-weight:600;">After photos</div>
                        <div style="font-size:.7rem;color:var(--text-tertiary);">Show the completed work</div>
                    </div>
                    <div id="srAfterPreviews" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(70px,1fr));gap:.5rem;margin-top:.625rem;"></div>
                </div>
            </div>

            <div style="margin-top:1.25rem;">
                <label style="display:block;font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-tertiary);margin-bottom:.4rem;">Evidence Notes (optional)</label>
                <textarea name="evidence_notes" style="width:100%;padding:.75rem 1rem;background:rgba(0,212,255,.04);border:1px solid var(--border-color);border-radius:10px;color:var(--text-primary);font-family:'Chakra Petch',sans-serif;font-size:.875rem;resize:vertical;min-height:70px;outline:none;box-sizing:border-box;" placeholder="Describe what the photos show..." maxlength="1000">{{ isset($serviceRecord) ? $serviceRecord->evidence_notes : old('evidence_notes') }}</textarea>
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

<script>
let issueCount = 0;

function addIssueRow() {
    issueCount++;
    document.getElementById('emptyState').style.display = 'none';
    const idx = issueCount;

    const html = `
    <div class="issue-item" id="issue-row-${idx}">
        <div class="issue-item-header">
            <span class="issue-num">ISSUE #${idx}</span>
            <button type="button" class="btn-remove-issue" onclick="removeIssueRow(${idx})">✕ Remove</button>
        </div>

        <div class="form-grid" style="margin-bottom:1rem;">
            {{-- Title --}}
            <div class="form-group full">
                <label class="form-label">Issue Title <span class="req">*</span></label>
                <input type="text" name="issues[${idx}][title]" class="form-input" required
                    placeholder="e.g. Worn front brake pads, Oil leak near valve cover...">
            </div>

            {{-- Category + Location --}}
            <div class="form-group">
                <label class="form-label">Category</label>
                <select name="issues[${idx}][category]" class="form-select">
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
                <input type="text" name="issues[${idx}][location]" class="form-input"
                    placeholder="e.g. Front left, Engine bay, Undercarriage...">
            </div>

            {{-- Description --}}
            <div class="form-group full">
                <label class="form-label">Description <span class="req">*</span></label>
                <textarea name="issues[${idx}][description]" class="form-textarea" required
                    placeholder="Describe the issue in detail — what you saw, how bad it is, what could happen if not fixed..."></textarea>
            </div>
        </div>

        {{-- Severity --}}
        <div style="margin-bottom:1rem;">
            <label class="form-label" style="margin-bottom:.625rem;">Severity</label>
            <div class="severity-grid">
                <button type="button" class="severity-btn" data-severity="low" data-idx="${idx}" onclick="setSeverity(${idx},'low')">
                    🟢 Low<br><span style="font-size:.65rem;font-weight:400;opacity:.7;">Monitor it</span>
                </button>
                <button type="button" class="severity-btn active" data-severity="medium" data-idx="${idx}" onclick="setSeverity(${idx},'medium')">
                    🟡 Medium<br><span style="font-size:.65rem;font-weight:400;opacity:.7;">Fix soon</span>
                </button>
                <button type="button" class="severity-btn" data-severity="high" data-idx="${idx}" onclick="setSeverity(${idx},'high')">
                    🟠 High<br><span style="font-size:.65rem;font-weight:400;opacity:.7;">Fix promptly</span>
                </button>
                <button type="button" class="severity-btn" data-severity="critical" data-idx="${idx}" onclick="setSeverity(${idx},'critical')">
                    🔴 Critical<br><span style="font-size:.65rem;font-weight:400;opacity:.7;">Urgent</span>
                </button>
            </div>
            <input type="hidden" name="issues[${idx}][severity]" id="severity-val-${idx}" value="medium">
        </div>

        {{-- Safety critical toggle --}}
        <div style="margin-bottom:1rem;">
            <label class="safety-toggle" id="safety-toggle-${idx}" onclick="toggleSafety(${idx})">
                <input type="hidden" name="issues[${idx}][is_safety_critical]" id="safety-val-${idx}" value="0">
                <span class="safety-icon">⚠️</span>
                <div>
                    <div style="font-weight:700;font-size:.825rem;color:#ff8099;">Safety Critical</div>
                    <div style="font-size:.75rem;color:var(--text-tertiary);">Vehicle may be unsafe to drive — consumer will be warned immediately</div>
                </div>
                <div style="margin-left:auto;width:20px;height:20px;border-radius:50%;border:2px solid rgba(255,51,102,.4);background:transparent;flex-shrink:0;transition:all .3s;" id="safety-check-${idx}"></div>
            </label>
        </div>

        {{-- Cost estimate --}}
        <div>
            <label class="form-label" style="margin-bottom:.625rem;">Estimated Repair Cost <span style="font-weight:400;opacity:.7;">(optional)</span></label>
            <div class="cost-range-row">
                <div class="prefix-wrap">
                    <span class="prefix">$</span>
                    <input type="number" name="issues[${idx}][estimated_cost_min]" class="form-input" placeholder="Min" min="0" step="10">
                </div>
                <div class="cost-sep">to</div>
                <div class="prefix-wrap">
                    <span class="prefix">$</span>
                    <input type="number" name="issues[${idx}][estimated_cost_max]" class="form-input" placeholder="Max" min="0" step="10">
                </div>
            </div>
            <div class="hint">Gives the consumer a ballpark before they seek quotes</div>
        </div>
    </div>`;

    document.getElementById('newIssueRows').insertAdjacentHTML('beforeend', html);
}

function removeIssueRow(idx) {
    const row = document.getElementById(`issue-row-${idx}`);
    if (row) row.remove();
    // Show empty state if no issue rows remain
    const remaining = document.querySelectorAll('#newIssueRows .issue-item');
    const hasExisting = document.querySelectorAll('#issuesList .issue-item[id^="issue-row-existing"]').length;
    if (remaining.length === 0 && hasExisting === 0) {
        document.getElementById('emptyState').style.display = '';
    }
}

function setSeverity(idx, val) {
    document.getElementById(`severity-val-${idx}`).value = val;
    document.querySelectorAll(`.severity-btn[data-idx="${idx}"]`).forEach(btn => {
        btn.classList.toggle('active', btn.dataset.severity === val);
    });
}

function toggleSafety(idx) {
    const toggle = document.getElementById(`safety-toggle-${idx}`);
    const valInput = document.getElementById(`safety-val-${idx}`);
    const check = document.getElementById(`safety-check-${idx}`);
    const isActive = toggle.classList.toggle('active');
    valInput.value = isActive ? '1' : '0';
    check.style.background = isActive ? '#ff3366' : 'transparent';
    check.style.borderColor = isActive ? '#ff3366' : 'rgba(255,51,102,.4)';
}
</script>
<script>
// ── Service record photo upload handling ────────────────────────────────────
const srFiles = { before: [], after: [] };

function srHandlePhotos(input, side) {
    const files  = Array.from(input.files);
    const merged = [...srFiles[side], ...files];
    if (merged.length > 6) { alert('Maximum 6 photos per set.'); input.value = ''; return; }
    srFiles[side] = merged;
    srRenderPreviews(side);
    srUpdateCount(side);
}

function srRenderPreviews(side) {
    const container = document.getElementById('sr' + side.charAt(0).toUpperCase() + side.slice(1) + 'Previews');
    container.innerHTML = '';
    srFiles[side].forEach((file, idx) => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.style.cssText = 'position:relative;border-radius:8px;overflow:hidden;aspect-ratio:1;background:rgba(0,0,0,.3);';
            div.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">
                <button type="button" onclick="srRemove('${side}',${idx})" style="position:absolute;top:3px;right:3px;background:rgba(0,0,0,.7);border:none;border-radius:50%;width:20px;height:20px;color:#fff;font-size:.65rem;cursor:pointer;display:flex;align-items:center;justify-content:center;">✕</button>`;
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

function srRemove(side, idx) {
    srFiles[side].splice(idx, 1);
    const dt = new DataTransfer();
    srFiles[side].forEach(f => dt.items.add(f));
    document.getElementById('sr' + side.charAt(0).toUpperCase() + side.slice(1) + 'Input').files = dt.files;
    srRenderPreviews(side);
    srUpdateCount(side);
}

function srUpdateCount(side) {
    const badge = document.getElementById('sr' + side.charAt(0).toUpperCase() + side.slice(1) + 'Count');
    const n = srFiles[side].length;
    badge.style.display = n > 0 ? 'inline-flex' : 'none';
    badge.textContent = n + ' photo' + (n !== 1 ? 's' : '');
}

['Before','After'].forEach(side => {
    const zone = document.getElementById('sr' + side + 'Zone');
    if (!zone) return;
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.style.borderColor='var(--accent-cyan)'; zone.style.background='rgba(0,212,255,.04)'; });
    zone.addEventListener('dragleave', () => { zone.style.borderColor='rgba(0,212,255,.2)'; zone.style.background=''; });
    zone.addEventListener('drop',      () => { zone.style.borderColor='rgba(0,212,255,.2)'; zone.style.background=''; });
});
</script>

@endsection