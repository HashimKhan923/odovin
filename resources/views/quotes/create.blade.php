@extends('layouts.app')
@section('title', 'Request a Quote — ' . $provider->name)

@section('content')
<style>
.qr-wrap { max-width: 760px; margin: 0 auto; padding: 2rem 1.5rem; }
.back-link { display:inline-flex; align-items:center; gap:.5rem; color:var(--accent-cyan); text-decoration:none; font-size:.875rem; font-weight:600; margin-bottom:1.5rem; transition:all .3s; }
.back-link:hover { gap:.875rem; }
.provider-mini { display:flex; align-items:center; gap:1rem; background:var(--card-bg); border:1px solid var(--border-color); border-radius:14px; padding:1.1rem 1.25rem; margin-bottom:1.75rem; }
.provider-avatar { width:48px; height:48px; border-radius:12px; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); display:flex; align-items:center; justify-content:center; font-family:'Orbitron',sans-serif; font-weight:800; font-size:1.2rem; color:#000; flex-shrink:0; }
.provider-mini-name { font-weight:700; font-size:.95rem; color:var(--text-primary); }
.provider-mini-type { font-size:.75rem; color:var(--text-tertiary); margin-top:.15rem; }
.form-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:18px; padding:2rem; }
.form-card-title { font-family:'Orbitron',sans-serif; font-size:1rem; font-weight:700; margin-bottom:1.5rem; padding-bottom:.875rem; border-bottom:1px solid var(--border-color); }
.field-group { margin-bottom:1.25rem; }
.field-label { display:block; font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--text-tertiary); margin-bottom:.5rem; }
.field-label span { color:#ff8099; }
.field-input, .field-select, .field-textarea { width:100%; padding:.75rem 1rem; background:rgba(0,212,255,.04); border:1px solid var(--border-color); border-radius:10px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.875rem; transition:border-color .2s; }
.field-input:focus, .field-select:focus, .field-textarea:focus { outline:none; border-color:var(--accent-cyan); box-shadow:0 0 0 3px rgba(0,212,255,.08); }
.field-select option { background:rgba(18,24,39,1); }
.field-textarea { resize:vertical; min-height:110px; line-height:1.6; }
.field-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
@media(max-width:600px) { .field-grid { grid-template-columns:1fr; } }
.urgency-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:.75rem; }
@media(max-width:500px) { .urgency-grid { grid-template-columns:1fr; } }
.urgency-option { position:relative; }
.urgency-option input[type=radio] { position:absolute; opacity:0; width:0; height:0; }
.urgency-label { display:flex; flex-direction:column; align-items:center; gap:.4rem; padding:.875rem .5rem; background:rgba(0,212,255,.04); border:1px solid var(--border-color); border-radius:12px; cursor:pointer; transition:all .2s; text-align:center; }
.urgency-option input:checked + .urgency-label { border-color:var(--accent-cyan); background:rgba(0,212,255,.1); }
.urgency-label:hover { border-color:rgba(0,212,255,.3); }
.urgency-icon { font-size:1.25rem; }
.urgency-text { font-size:.78rem; font-weight:600; color:var(--text-secondary); }
.urgency-sub { font-size:.68rem; color:var(--text-tertiary); }
.budget-prefix { position:absolute; left:.875rem; top:50%; transform:translateY(-50%); color:var(--text-tertiary); font-weight:700; pointer-events:none; }
.budget-input-wrap { position:relative; }
.budget-input-wrap .field-input { padding-left:1.75rem; }
.btn-submit { width:100%; padding:1rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:12px; color:#000; font-family:'Orbitron',sans-serif; font-weight:800; font-size:.875rem; letter-spacing:.04em; cursor:pointer; transition:all .3s; box-shadow:0 4px 15px rgba(0,212,255,.25); margin-top:.5rem; }
.btn-submit:hover { transform:translateY(-2px); box-shadow:0 6px 25px rgba(0,212,255,.4); }
.char-count { font-size:.72rem; color:var(--text-tertiary); text-align:right; margin-top:.3rem; }
.field-hint { font-size:.72rem; color:var(--text-tertiary); margin-top:.35rem; }
</style>

<div class="qr-wrap">
    <a href="{{ route('providers.show', $provider) }}" class="back-link">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to {{ $provider->name }}
    </a>

    {{-- Provider mini-card --}}
    <div class="provider-mini">
        <div class="provider-avatar">{{ strtoupper(substr($provider->name, 0, 1)) }}</div>
        <div>
            <div class="provider-mini-name">{{ $provider->name }}</div>
            <div class="provider-mini-type">
                {{ ucfirst(str_replace('_', ' ', $provider->type)) }}
                @if($provider->is_verified) · <span style="color:var(--accent-green);">✓ Verified</span>@endif
                @if($provider->rating > 0) · ★ {{ number_format($provider->rating, 1) }}@endif
                · {{ $provider->city }}, {{ $provider->state }}
            </div>
        </div>
    </div>

    @if(session('error'))
    <div style="background:rgba(255,51,102,.1);border:1px solid rgba(255,51,102,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:#ff8099;font-size:.875rem;">
        {!! session('error') !!}
    </div>
    @endif

    <div class="form-card">
        <div class="form-card-title">📋 Request a Quote</div>

        <form method="POST" action="{{ route('quotes.store', $provider) }}">
            @csrf

            {{-- Vehicle --}}
            <div class="field-group">
                <label class="field-label" for="vehicle_id">Your Vehicle</label>
                @if($vehicles->isEmpty())
                <p style="font-size:.82rem;color:var(--text-tertiary);">
                    No vehicles found. <a href="{{ route('vehicles.create') }}" style="color:var(--accent-cyan);">Add a vehicle first →</a>
                </p>
                @else
                <select name="vehicle_id" id="vehicle_id" class="field-select">
                    <option value="">Select vehicle (optional)</option>
                    @foreach($vehicles as $v)
                    <option value="{{ $v->id }}" {{ old('vehicle_id') == $v->id ? 'selected' : '' }}>
                        {{ $v->year }} {{ $v->make }} {{ $v->model }}
                    </option>
                    @endforeach
                </select>
                @endif
            </div>

            {{-- Service type --}}
            <div class="field-group">
                <label class="field-label" for="service_type">Service Needed <span>*</span></label>
                <select name="service_type" id="service_type" class="field-select" required>
                    <option value="">Select a service...</option>
                    @foreach($serviceTypes as $svc)
                    <option value="{{ $svc }}"
                        {{ (old('service_type', $preselectedService) === $svc) ? 'selected' : '' }}>
                        {{ $svc }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Description --}}
            <div class="field-group">
                <label class="field-label" for="description">Describe the Issue or Work Needed <span>*</span></label>
                <textarea name="description" id="description" class="field-textarea"
                    placeholder="Be specific — include symptoms, when the issue started, any warning lights, etc. The more detail you provide, the more accurate the quote."
                    maxlength="2000" oninput="updateCharCount(this)" required>{{ old('description') }}</textarea>
                <div class="char-count"><span id="charCount">0</span> / 2000</div>
                @error('description')<div style="color:#ff8099;font-size:.75rem;margin-top:.3rem;">{{ $message }}</div>@enderror
            </div>

            {{-- Urgency --}}
            <div class="field-group">
                <label class="field-label">How Urgent Is This? <span>*</span></label>
                <div class="urgency-grid">
                    @foreach([
                        ['flexible',  '🟢', 'Flexible',  'No rush, best price'],
                        ['this_week', '🟡', 'This Week', 'Within 7 days'],
                        ['today',     '🔴', 'Today',     'As soon as possible'],
                    ] as [$val, $icon, $label, $sub])
                    <div class="urgency-option">
                        <input type="radio" name="urgency" id="urgency_{{ $val }}" value="{{ $val }}"
                            {{ old('urgency', 'flexible') === $val ? 'checked' : '' }}>
                        <label class="urgency-label" for="urgency_{{ $val }}">
                            <span class="urgency-icon">{{ $icon }}</span>
                            <span class="urgency-text">{{ $label }}</span>
                            <span class="urgency-sub">{{ $sub }}</span>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Preferred date & time --}}
            <div class="field-grid">
                <div class="field-group">
                    <label class="field-label" for="preferred_date">Preferred Date</label>
                    <input type="date" name="preferred_date" id="preferred_date" class="field-input"
                        value="{{ old('preferred_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                </div>
                <div class="field-group">
                    <label class="field-label" for="preferred_time">Preferred Time</label>
                    <select name="preferred_time" id="preferred_time" class="field-select">
                        <option value="">Any time</option>
                        @foreach(['Morning (8am–12pm)', 'Afternoon (12pm–5pm)', 'Evening (5pm–8pm)'] as $t)
                        <option value="{{ $t }}" {{ old('preferred_time') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Budget range --}}
            <div class="field-group">
                <label class="field-label">Budget Range (optional)</label>
                <div class="field-grid">
                    <div>
                        <div class="budget-input-wrap">
                            <span class="budget-prefix">$</span>
                            <input type="number" name="budget_min" class="field-input"
                                placeholder="Min" min="0" step="1" value="{{ old('budget_min') }}">
                        </div>
                        <div class="field-hint">Minimum you expect to pay</div>
                    </div>
                    <div>
                        <div class="budget-input-wrap">
                            <span class="budget-prefix">$</span>
                            <input type="number" name="budget_max" class="field-input"
                                placeholder="Max" min="0" step="1" value="{{ old('budget_max') }}">
                        </div>
                        <div class="field-hint">Maximum you're willing to pay</div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-submit">Send Quote Request →</button>
            <p style="font-size:.72rem;color:var(--text-tertiary);text-align:center;margin-top:.875rem;">
                {{ $provider->name }} typically responds within 24 hours. You'll get a notification when they reply.
            </p>
        </form>
    </div>
</div>

<script>
function updateCharCount(el) {
    document.getElementById('charCount').textContent = el.value.length;
}
// Init on load
document.addEventListener('DOMContentLoaded', function () {
    var desc = document.getElementById('description');
    if (desc) document.getElementById('charCount').textContent = desc.value.length;
});
</script>
@endsection