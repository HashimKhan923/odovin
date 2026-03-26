@extends('layouts.app')
@section('title', 'Edit Trip')
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
    background:linear-gradient(135deg,var(--accent-warning),var(--accent-cyan));
    -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
    margin:0 0 .35rem; }
.page-sub { color:var(--text-tertiary); font-size:.875rem; margin-bottom:2rem; }
.form-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px;
    padding:2rem; margin-bottom:1.25rem; position:relative; overflow:hidden; }
.form-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px;
    background:linear-gradient(90deg,var(--accent-warning),var(--accent-cyan)); }
.section-title { font-family:'Orbitron',sans-serif; font-size:.8rem; font-weight:700;
    color:var(--accent-cyan); text-transform:uppercase; letter-spacing:.08em; margin-bottom:1.25rem; }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; }
.form-group.full { grid-column:1/-1; }
.form-label { display:block; font-size:.8rem; font-weight:600; color:var(--text-secondary); margin-bottom:.5rem; }
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
.distance-preview { background:rgba(0,212,255,.06); border:1px solid rgba(0,212,255,.2);
    border-radius:10px; padding:1rem 1.25rem; display:flex; align-items:center; justify-content:space-between; }
.distance-label { font-size:.8rem; color:var(--text-secondary); }
.distance-num { font-family:'Orbitron',sans-serif; font-size:1.5rem; font-weight:800; color:var(--accent-cyan); }
.distance-unit { font-size:.75rem; color:var(--text-tertiary); }
.btn-row { display:flex; gap:.75rem; }
.btn-submit { flex:1; padding:1rem; background:linear-gradient(135deg,var(--accent-warning),var(--accent-cyan));
    border:none; border-radius:12px; color:#000; font-family:'Orbitron',sans-serif;
    font-weight:700; font-size:.95rem; cursor:pointer; transition:all .3s; }
.btn-submit:hover { transform:translateY(-2px); box-shadow:0 6px 25px rgba(255,170,0,.4); }
.btn-delete { padding:1rem 1.5rem; background:rgba(255,51,102,.1); border:1px solid rgba(255,51,102,.25);
    border-radius:12px; color:var(--accent-danger); font-family:'Orbitron',sans-serif;
    font-weight:700; font-size:.875rem; cursor:pointer; transition:all .3s; }
.btn-delete:hover { background:rgba(255,51,102,.2); }
@media(max-width:600px){ .form-grid { grid-template-columns:1fr; } .purpose-grid { grid-template-columns:1fr; } }
</style>

<div class="pg">
    <a href="{{ route('trips.index') }}" class="back-link">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Trip Log
    </a>

    <h1 class="page-title">Edit Trip</h1>
    <p class="page-sub">{{ $tripLog->trip_date->format('l, F d, Y') }} · {{ number_format($tripLog->distance) }} miles</p>

    <form method="POST" action="{{ route('trips.update', $tripLog) }}">
        @csrf @method('PUT')

        <div class="form-card">
            <div class="section-title">📍 Trip Details</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Vehicle <span class="req">*</span></label>
                    <select name="vehicle_id" class="form-select" required>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}" {{ old('vehicle_id', $tripLog->vehicle_id) == $v->id ? 'selected' : '' }}>
                                {{ $v->year }} {{ $v->make }} {{ $v->model }}
                            </option>
                        @endforeach
                    </select>
                    @error('vehicle_id')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Trip Date <span class="req">*</span></label>
                    <input type="date" name="trip_date" class="form-input"
                        value="{{ old('trip_date', $tripLog->trip_date->format('Y-m-d')) }}"
                        max="{{ now()->format('Y-m-d') }}" required>
                    @error('trip_date')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Start Location</label>
                    <input type="text" name="start_location" class="form-input"
                        value="{{ old('start_location', $tripLog->start_location) }}"
                        placeholder="e.g. Home, Office">
                    @error('start_location')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Destination <span class="req">*</span></label>
                    <input type="text" name="destination" class="form-input"
                        value="{{ old('destination', $tripLog->destination) }}" required>
                    @error('destination')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="section-title">🔢 Odometer Readings</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Start Odometer <span class="req">*</span></label>
                    <input type="number" name="start_odometer" id="startOdo" class="form-input"
                        value="{{ old('start_odometer', $tripLog->start_odometer) }}"
                        min="0" required oninput="calcDistance()">
                    @error('start_odometer')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">End Odometer <span class="req">*</span></label>
                    <input type="number" name="end_odometer" id="endOdo" class="form-input"
                        value="{{ old('end_odometer', $tripLog->end_odometer) }}"
                        min="0" required oninput="calcDistance()">
                    @error('end_odometer')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group full">
                    <div class="distance-preview">
                        <span class="distance-label">Calculated Distance</span>
                        <span>
                            <span class="distance-num" id="distPreview">{{ number_format($tripLog->distance) }}</span>
                            <span class="distance-unit"> miles</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="section-title">🎯 Trip Purpose</div>
            <div class="purpose-grid">
                @foreach(['business' => ['💼','Business','Client visits, meetings'], 'personal' => ['🏠','Personal','Errands, leisure'], 'commute' => ['🚗','Commute','Daily work commute']] as $val => $info)
                <div>
                    <input type="radio" name="purpose" id="p_{{ $val }}" value="{{ $val }}" class="purpose-option"
                        {{ old('purpose', $tripLog->purpose) === $val ? 'checked' : '' }}>
                    <label for="p_{{ $val }}" class="purpose-label">
                        <span class="purpose-emoji">{{ $info[0] }}</span>
                        <span class="purpose-name">{{ $info[1] }}</span>
                        <span class="purpose-desc">{{ $info[2] }}</span>
                    </label>
                </div>
                @endforeach
            </div>
            @error('purpose')<div class="error-msg" style="margin-top:.5rem;">{{ $message }}</div>@enderror
        </div>

        <div class="form-card">
            <div class="section-title">📝 Notes</div>
            <textarea name="notes" class="form-textarea" rows="3"
                placeholder="Additional details…">{{ old('notes', $tripLog->notes) }}</textarea>
        </div>

        <div class="btn-row">
            <button type="submit" class="btn-submit">✏️ Update Trip</button>
        </div>
    </form>

    {{-- Delete --}}
    <div style="margin-top:1rem;">
        <form method="POST" action="{{ route('trips.destroy', $tripLog) }}" onsubmit="return confirm('Delete this trip permanently?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-delete" style="width:100%;">🗑 Delete This Trip</button>
        </form>
    </div>
</div>

<script>
function calcDistance() {
    const s = parseInt(document.getElementById('startOdo').value) || 0;
    const e = parseInt(document.getElementById('endOdo').value) || 0;
    const d = e > s ? e - s : 0;
    document.getElementById('distPreview').textContent = d > 0 ? d.toLocaleString() : '—';
}
</script>
@endsection