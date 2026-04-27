@extends('layouts.app')
@section('title', 'Create Service Report Link')
@section('content')
<style>
.sr-create { max-width:680px; margin:0 auto; padding:2rem 1.5rem; }
.back-link { display:inline-flex;align-items:center;gap:.5rem;color:var(--accent-cyan);text-decoration:none;font-size:.875rem;font-weight:600;margin-bottom:1.5rem;transition:gap .2s; }
.back-link:hover { gap:.875rem; }
.form-card { background:var(--card-bg);border:1px solid var(--border-color);border-radius:18px;padding:2rem; }
.card-title { font-family:'Orbitron',sans-serif;font-size:1rem;font-weight:700;margin-bottom:1.5rem;padding-bottom:.875rem;border-bottom:1px solid var(--border-color); }
.field-group { margin-bottom:1.25rem; }
.field-label { display:block;font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-tertiary);margin-bottom:.45rem; }
.field-label span { color:#ff8099; }
.field-input,.field-select { width:100%;padding:.75rem 1rem;background:rgba(0,212,255,.04);border:1px solid var(--border-color);border-radius:10px;color:var(--text-primary);font-family:'Chakra Petch',sans-serif;font-size:.875rem;outline:none;transition:border-color .2s;box-sizing:border-box; }
.field-input:focus,.field-select:focus { border-color:var(--accent-cyan); }
.field-select option { background:#121827; }
.field-hint { font-size:.72rem;color:var(--text-tertiary);margin-top:.35rem; }
.toggle-group { display:grid;grid-template-columns:1fr 1fr;gap:.75rem; }
.toggle-item { display:flex;align-items:center;gap:.75rem;padding:.875rem 1rem;background:rgba(0,212,255,.03);border:1px solid var(--border-color);border-radius:10px;cursor:pointer;transition:all .2s; }
.toggle-item:hover { border-color:rgba(0,212,255,.25); }
.toggle-item input { width:18px;height:18px;accent-color:var(--accent-cyan);cursor:pointer; }
.toggle-label { font-size:.82rem;color:var(--text-secondary); }
.toggle-label strong { display:block;font-weight:700;color:var(--text-primary);margin-bottom:.1rem; }
.field-grid { display:grid;grid-template-columns:1fr 1fr;gap:1rem; }
.btn-create { width:100%;padding:1rem;background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green));border:none;border-radius:12px;color:#000;font-family:'Orbitron',sans-serif;font-weight:800;font-size:.875rem;cursor:pointer;transition:all .3s; }
.btn-create:hover { transform:translateY(-1px);box-shadow:0 4px 20px rgba(0,212,255,.35); }
</style>

<div class="sr-create">
    <a href="{{ route('service-history.report.index') }}" class="back-link">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Share Links
    </a>

    @if($errors->any())
    <div style="background:rgba(255,51,102,.1);border:1px solid rgba(255,51,102,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:#ff8099;font-size:.875rem;">
        @foreach($errors->all() as $e)<div>✕ {{ $e }}</div>@endforeach
    </div>
    @endif

    <div class="form-card">
        <div class="card-title">🔗 Create Shareable Report Link</div>

        <form method="POST" action="{{ route('service-history.report.store') }}">
            @csrf

            {{-- Vehicle --}}
            <div class="field-group">
                <label class="field-label" for="vehicle_id">Vehicle <span>*</span></label>
                <select name="vehicle_id" id="vehicle_id" class="field-select" required>
                    @foreach($vehicles as $v)
                    <option value="{{ $v->id }}" {{ $selected?->id === $v->id ? 'selected' : '' }}>
                        {{ $v->year }} {{ $v->make }} {{ $v->model }}{{ $v->vin ? ' — ' . $v->vin : '' }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Label --}}
            <div class="field-group">
                <label class="field-label" for="label">Link Label (optional)</label>
                <input type="text" id="label" name="label" class="field-input"
                    value="{{ old('label') }}" maxlength="100"
                    placeholder="e.g. For insurance claim, For prospective buyer">
                <p class="field-hint">A private label only you see — helps identify what this link is for.</p>
            </div>

            {{-- Date range --}}
            <div class="field-grid" style="margin-bottom:1.25rem;">
                <div class="field-group" style="margin-bottom:0;">
                    <label class="field-label" for="from_date">From Date</label>
                    <input type="date" id="from_date" name="from_date" class="field-input" value="{{ old('from_date') }}">
                    <p class="field-hint">Leave blank to include all records</p>
                </div>
                <div class="field-group" style="margin-bottom:0;">
                    <label class="field-label" for="to_date">To Date</label>
                    <input type="date" id="to_date" name="to_date" class="field-input" value="{{ old('to_date') }}" max="{{ date('Y-m-d') }}">
                </div>
            </div>

            {{-- Expiry --}}
            <div class="field-group">
                <label class="field-label" for="expires_in">Link Expiry</label>
                <select name="expires_in" id="expires_in" class="field-select">
                    <option value="0">Never expires</option>
                    <option value="7">7 days</option>
                    <option value="30" selected>30 days</option>
                    <option value="90">90 days</option>
                    <option value="365">1 year</option>
                </select>
            </div>

            {{-- What to include --}}
            <div class="field-group">
                <label class="field-label">What to Include</label>
                <div class="toggle-group">
                    @foreach([
                        ['include_costs',            '💰', 'Service Costs',      'Show amounts paid per service'],
                        ['include_diagnostics',      '🔧', 'Diagnostics',        'Include flagged vehicle issues'],
                        ['include_provider_details', '🏪', 'Provider Names',     'Show which workshop did the work'],
                        ['include_photos',           '📸', 'Before/After Photos','Include job evidence photos'],
                    ] as [$name, $icon, $label, $hint])
                    <label class="toggle-item">
                        <input type="checkbox" name="{{ $name }}" value="1" checked>
                        <div class="toggle-label">
                            <strong>{{ $icon }} {{ $label }}</strong>
                            {{ $hint }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="btn-create">Generate Share Link →</button>
            <p style="font-size:.72rem;color:var(--text-tertiary);text-align:center;margin-top:.75rem;">
                You can revoke this link at any time. Vehicle owner information is never shared.
            </p>
        </form>
    </div>
</div>
@endsection