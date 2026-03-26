@extends('layouts.app')
@section('title', 'Log a Trip')
@section('content')
<style>
:root[data-theme="dark"] {
    --card-bg:rgba(26,32,48,0.85); --border-color:rgba(0,212,255,0.1);
    --text-primary:#fff; --text-secondary:rgba(255,255,255,0.7); --text-tertiary:rgba(255,255,255,0.45);
    --accent-cyan:#00d4ff; --accent-green:#00ffaa; --accent-warning:#ffaa00; --accent-danger:#ff3366;
    --input-bg:rgba(0,212,255,0.05);
}
:root[data-theme="light"] {
    --card-bg:rgba(255,255,255,0.95); --border-color:rgba(0,0,0,0.1);
    --text-primary:#1a1f36; --text-secondary:rgba(26,31,54,0.7); --text-tertiary:rgba(26,31,54,0.45);
    --accent-cyan:#0066ff; --accent-green:#00cc88; --accent-warning:#ff9500; --accent-danger:#ff3366;
    --input-bg:rgba(0,0,0,0.03);
}
.pg { max-width:760px; margin:0 auto; padding:2rem 1.5rem; }
.back-link { display:inline-flex; align-items:center; gap:.5rem; color:var(--accent-cyan);
    text-decoration:none; font-size:.875rem; font-weight:600; margin-bottom:1.5rem; transition:all .3s; }
.back-link:hover { gap:.875rem; }
.page-title { font-family:'Orbitron',sans-serif; font-size:2rem; font-weight:800;
    background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green));
    -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
    margin:0 0 .35rem; }
.page-sub { color:var(--text-tertiary); font-size:.875rem; margin-bottom:2rem; }
.form-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px;
    padding:2rem; margin-bottom:1.25rem; position:relative; overflow:hidden; }
.form-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px;
    background:linear-gradient(90deg,var(--accent-cyan),var(--accent-green)); }
.section-title { font-family:'Orbitron',sans-serif; font-size:.8rem; font-weight:700;
    color:var(--accent-cyan); text-transform:uppercase; letter-spacing:.08em; margin-bottom:1.25rem; }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; }
.form-group { margin-bottom:0; }
.form-group.full { grid-column:1/-1; }
.form-label { display:block; font-size:.8rem; font-weight:600; color:var(--text-secondary);
    margin-bottom:.5rem; }
.form-label .req { color:var(--accent-warning); }
.form-input, .form-select, .form-textarea { width:100%; padding:.75rem 1rem;
    background:var(--input-bg); border:1px solid var(--border-color); border-radius:10px;
    color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.875rem;
    transition:all .3s; box-sizing:border-box; }
.form-input:focus, .form-select:focus, .form-textarea:focus { outline:none;
    border-color:var(--accent-cyan); box-shadow:0 0 0 3px rgba(0,212,255,.1); }
.form-select option { background:#121827; }
.form-textarea { resize:vertical; min-height:90px; }
.form-hint { font-size:.72rem; color:var(--text-tertiary); margin-top:.35rem; }
.error-msg { font-size:.75rem; color:var(--accent-danger); margin-top:.35rem; }

/* Purpose selector */
.purpose-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:.75rem; }
.purpose-option { display:none; }
.purpose-label { display:flex; flex-direction:column; align-items:center; justify-content:center;
    gap:.5rem; padding:1.25rem; border-radius:12px; border:2px solid var(--border-color);
    cursor:pointer; transition:all .3s; text-align:center; }
.purpose-label:hover { border-color:rgba(0,212,255,.3); }
.purpose-option:checked + .purpose-label { border-color:var(--accent-cyan);
    background:rgba(0,212,255,.08); box-shadow:0 0 0 1px var(--accent-cyan); }
.purpose-emoji { font-size:1.75rem; }
.purpose-name { font-size:.8rem; font-weight:700; color:var(--text-primary); }
.purpose-desc { font-size:.7rem; color:var(--text-tertiary); }

/* Distance preview */
.distance-preview { background:rgba(0,212,255,.06); border:1px solid rgba(0,212,255,.2);
    border-radius:10px; padding:1rem 1.25rem; display:flex; align-items:center; justify-content:space-between; }
.distance-label { font-size:.8rem; color:var(--text-secondary); }
.distance-num { font-family:'Orbitron',sans-serif; font-size:1.5rem; font-weight:800;
    color:var(--accent-cyan); }
.distance-unit { font-size:.75rem; color:var(--text-tertiary); }

/* Submit */
.btn-submit { width:100%; padding:1rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green));
    border:none; border-radius:12px; color:#000; font-family:'Orbitron',sans-serif;
    font-weight:700; font-size:.95rem; cursor:pointer; transition:all .3s; letter-spacing:.05em; }
.btn-submit:hover { transform:translateY(-2px); box-shadow:0 6px 25px rgba(0,212,255,.5); }
@media(max-width:600px){ .form-grid { grid-template-columns:1fr; } .purpose-grid { grid-template-columns:1fr; } }
</style>

<div class="pg">
    <a href="{{ route('trips.index') }}" class="back-link">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Trip Log
    </a>

    <h1 class="page-title">Log a Trip</h1>
    <p class="page-sub">Record your journey details for mileage tracking and expense reporting</p>

    <form method="POST" action="{{ route('trips.store') }}">
        @csrf

        {{-- Trip Details --}}
        <div class="form-card">
            <div class="section-title">📍 Trip Details</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Vehicle <span class="req">*</span></label>
                    <select name="vehicle_id" class="form-select" required>
                        <option value="">Select vehicle…</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}" {{ old('vehicle_id') == $v->id ? 'selected' : '' }}>
                                {{ $v->year }} {{ $v->make }} {{ $v->model }}
                            </option>
                        @endforeach
                    </select>
                    @error('vehicle_id')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Trip Date <span class="req">*</span></label>
                    <input type="date" name="trip_date" class="form-input"
                        value="{{ old('trip_date', now()->format('Y-m-d')) }}"
                        max="{{ now()->format('Y-m-d') }}" required>
                    @error('trip_date')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Start Location</label>
                    <input type="text" name="start_location" class="form-input"
                        placeholder="e.g. Home, Office, 123 Main St"
                        value="{{ old('start_location') }}">
                    @error('start_location')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Destination <span class="req">*</span></label>
                    <input type="text" name="destination" class="form-input"
                        placeholder="e.g. Client Office, Airport"
                        value="{{ old('destination') }}" required>
                    @error('destination')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Odometer --}}
        <div class="form-card">
            <div class="section-title">🔢 Odometer Readings</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Start Odometer <span class="req">*</span></label>
                    <input type="number" name="start_odometer" id="startOdo" class="form-input"
                        placeholder="e.g. 45000" min="0"
                        value="{{ old('start_odometer') }}" required oninput="calcDistance()">
                    <div class="form-hint">Reading before the trip started</div>
                    @error('start_odometer')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">End Odometer <span class="req">*</span></label>
                    <input type="number" name="end_odometer" id="endOdo" class="form-input"
                        placeholder="e.g. 45125" min="0"
                        value="{{ old('end_odometer') }}" required oninput="calcDistance()">
                    <div class="form-hint">Reading at trip destination</div>
                    @error('end_odometer')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group full">
                    <div class="distance-preview">
                        <span class="distance-label">Calculated Distance</span>
                        <span>
                            <span class="distance-num" id="distPreview">—</span>
                            <span class="distance-unit"> miles</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Purpose --}}
        <div class="form-card">
            <div class="section-title">🎯 Trip Purpose</div>
            <div class="purpose-grid">
                <div>
                    <input type="radio" name="purpose" id="p_business" value="business" class="purpose-option"
                        {{ old('purpose') === 'business' ? 'checked' : '' }}>
                    <label for="p_business" class="purpose-label">
                        <span class="purpose-emoji">💼</span>
                        <span class="purpose-name">Business</span>
                        <span class="purpose-desc">Client visits, meetings, deliveries</span>
                    </label>
                </div>
                <div>
                    <input type="radio" name="purpose" id="p_personal" value="personal" class="purpose-option"
                        {{ old('purpose') === 'personal' ? 'checked' : '' }}>
                    <label for="p_personal" class="purpose-label">
                        <span class="purpose-emoji">🏠</span>
                        <span class="purpose-name">Personal</span>
                        <span class="purpose-desc">Errands, leisure, family</span>
                    </label>
                </div>
                <div>
                    <input type="radio" name="purpose" id="p_commute" value="commute" class="purpose-option"
                        {{ old('purpose') === 'commute' ? 'checked' : '' }}>
                    <label for="p_commute" class="purpose-label">
                        <span class="purpose-emoji">🚗</span>
                        <span class="purpose-name">Commute</span>
                        <span class="purpose-desc">Daily work commute</span>
                    </label>
                </div>
            </div>
            @error('purpose')<div class="error-msg" style="margin-top:.5rem;">{{ $message }}</div>@enderror
        </div>

        {{-- Notes --}}
        <div class="form-card">
            <div class="section-title">📝 Notes (Optional)</div>
            <div class="form-group">
                <textarea name="notes" class="form-textarea" rows="3"
                    placeholder="Additional details about this trip…">{{ old('notes') }}</textarea>
            </div>
        </div>

        <button type="submit" class="btn-submit">🗺️ Log Trip</button>
    </form>
</div>

<script>
function calcDistance() {
    const s = parseInt(document.getElementById('startOdo').value) || 0;
    const e = parseInt(document.getElementById('endOdo').value) || 0;
    const dist = e > s ? e - s : 0;
    document.getElementById('distPreview').textContent = dist > 0 ? dist.toLocaleString() : '—';
}
</script>
@endsection