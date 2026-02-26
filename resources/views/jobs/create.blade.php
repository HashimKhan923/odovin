@extends('layouts.app')
@section('title', 'Post a Service Job')
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
.pg-container { max-width:760px; margin:0 auto; padding:2rem 1.5rem; }
.back-link { display:inline-flex; align-items:center; gap:.5rem; color:var(--accent-cyan); text-decoration:none; font-size:.875rem; font-weight:600; margin-bottom:1.5rem; }
.back-link:hover { gap:.875rem; }
.page-title { font-family:'Orbitron',sans-serif; font-size:1.75rem; font-weight:800; margin-bottom:.5rem; }
.page-title span { color:var(--accent-cyan); }
.page-sub { color:var(--text-tertiary); font-size:.875rem; margin-bottom:2rem; }
.form-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:2rem; margin-bottom:1.5rem; position:relative; overflow:hidden; }
.form-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,var(--accent-cyan),var(--accent-green)); }
.section-title { font-family:'Orbitron',sans-serif; font-size:.875rem; font-weight:700; color:var(--accent-cyan); text-transform:uppercase; letter-spacing:.08em; margin-bottom:1.25rem; }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; }
.form-group { margin-bottom:1.25rem; }
.form-group.full { grid-column:1/-1; }
.form-label { display:block; font-size:.8rem; font-weight:600; color:var(--text-secondary); margin-bottom:.5rem; }
.form-label .req { color:var(--accent-warning); }
.form-input, .form-select, .form-textarea { width:100%; padding:.75rem 1rem; background:var(--input-bg); border:1px solid var(--border-color); border-radius:10px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.875rem; transition:all .3s; box-sizing:border-box; }
.form-input:focus, .form-select:focus, .form-textarea:focus { outline:none; border-color:var(--accent-cyan); box-shadow:0 0 0 3px rgba(0,212,255,.1); }
.form-select option { background:rgba(18,24,39,1); }
.form-textarea { resize:vertical; min-height:100px; }
.budget-row { display:grid; grid-template-columns:1fr auto 1fr; gap:.75rem; align-items:center; }
.budget-sep { text-align:center; color:var(--text-tertiary); font-weight:600; }
.location-detect { display:flex; gap:.75rem; align-items:flex-start; }
.location-detect .form-input { flex:1; }
.btn-detect { display:inline-flex; align-items:center; gap:.5rem; padding:.75rem 1rem; background:rgba(0,212,255,.1); border:1px solid rgba(0,212,255,.3); border-radius:10px; color:var(--accent-cyan); font-size:.8rem; font-weight:600; cursor:pointer; white-space:nowrap; transition:all .3s; }
.btn-detect:hover { background:rgba(0,212,255,.2); }
.btn-detect.detecting { opacity:.7; pointer-events:none; }
.location-status { font-size:.75rem; margin-top:.5rem; }
.location-status.success { color:var(--accent-green); }
.location-status.error   { color:#ff8099; }
.radius-hint { font-size:.75rem; color:var(--text-tertiary); margin-top:.375rem; }
.info-box { background:rgba(0,212,255,.06); border:1px solid rgba(0,212,255,.15); border-radius:10px; padding:1rem 1.25rem; font-size:.825rem; color:var(--text-secondary); display:flex; gap:.75rem; margin-bottom:1.5rem; }
.info-box svg { width:18px; height:18px; color:var(--accent-cyan); flex-shrink:0; margin-top:1px; }
.btn-submit { width:100%; padding:1rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:12px; color:#000; font-family:'Orbitron',sans-serif; font-weight:800; font-size:.9rem; letter-spacing:.05em; cursor:pointer; transition:all .3s; box-shadow:0 4px 15px rgba(0,212,255,.3); }
.btn-submit:hover { transform:translateY(-2px); box-shadow:0 6px 25px rgba(0,212,255,.5); }
.error-msg { color:#ff8099; font-size:.775rem; margin-top:.375rem; }
</style>

<div class="pg-container">
    <a href="{{ route('jobs.index') }}" class="back-link">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to My Jobs
    </a>

    <div class="page-title">Post a <span>Service Job</span></div>
    <p class="page-sub">Tell us what you need — nearby providers will compete to offer you the best price.</p>

    <div class="info-box">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>Your job post will be visible to verified service providers within your selected radius for <strong>24 hours</strong>. Once you accept an offer, the rest are automatically declined.</div>
    </div>

    <form method="POST" action="{{ route('jobs.store') }}" id="jobForm">
        @csrf

        {{-- Hidden location fields --}}
        <input type="hidden" name="latitude"  id="lat_input"  value="{{ old('latitude') }}">
        <input type="hidden" name="longitude" id="lng_input"  value="{{ old('longitude') }}">

        {{-- Vehicle & Service --}}
        <div class="form-card">
            <div class="section-title">Vehicle & Service</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Your Vehicle <span class="req">*</span></label>
                    <select name="vehicle_id" class="form-select" required>
                        <option value="">Select vehicle...</option>
                        @foreach($vehicles as $v)
                        <option value="{{ $v->id }}" {{ old('vehicle_id') == $v->id ? 'selected' : '' }}>
                            {{ $v->full_name }}
                        </option>
                        @endforeach
                    </select>
                    @error('vehicle_id')<p class="error-msg">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Service Type <span class="req">*</span></label>
                    <select name="service_type" class="form-select" required>
                        <option value="">Select service...</option>
                        @foreach($serviceTypes as $svc)
                        <option value="{{ $svc }}" {{ old('service_type') === $svc ? 'selected' : '' }}>{{ $svc }}</option>
                        @endforeach
                    </select>
                    @error('service_type')<p class="error-msg">{{ $message }}</p>@enderror
                </div>

                <div class="form-group full">
                    <label class="form-label">Job Description <span class="req">*</span></label>
                    <textarea name="description" class="form-textarea" placeholder="Describe the service you need in detail (symptoms, issues, etc.)..." required>{{ old('description') }}</textarea>
                    @error('description')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Budget & Schedule --}}
        <div class="form-card">
            <div class="section-title">Budget & Schedule</div>
            <div class="form-grid">
                <div class="form-group full">
                    <label class="form-label">Your Budget Range (optional)</label>
                    <div class="budget-row">
                        <input type="number" name="budget_min" class="form-input" placeholder="Min $" min="0" step="5" value="{{ old('budget_min') }}">
                        <div class="budget-sep">to</div>
                        <input type="number" name="budget_max" class="form-input" placeholder="Max $" min="0" step="5" value="{{ old('budget_max') }}">
                    </div>
                    @error('budget_max')<p class="error-msg">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Preferred Date</label>
                    <input type="date" name="preferred_date" class="form-input" min="{{ now()->addDay()->format('Y-m-d') }}" value="{{ old('preferred_date') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Preferred Time</label>
                    <select name="preferred_time" class="form-select">
                        <option value="">Any time</option>
                        @foreach($timePreferences as $t)
                        <option value="{{ $t }}" {{ old('preferred_time') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group full">
                    <label class="form-label">Additional Notes</label>
                    <textarea name="customer_notes" class="form-textarea" rows="3" placeholder="Any special instructions or requirements...">{{ old('customer_notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Location --}}
        <div class="form-card">
            <div class="section-title">Your Location</div>
            <p style="font-size:.825rem;color:var(--text-secondary);margin-bottom:1.25rem;">We use your location to show this job to nearby service providers.</p>

            <div class="form-group">
                <label class="form-label">Address / Area <span class="req">*</span></label>
                <div class="location-detect">
                    <input type="text" name="location_address" id="location_address" class="form-input"
                        placeholder="Enter your address or click detect..." value="{{ old('location_address') }}">
                    <button type="button" class="btn-detect" id="detectBtn" onclick="detectLocation()">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Detect
                    </button>
                </div>
                <div id="locationStatus" class="location-status"></div>
                @error('latitude')<p class="error-msg">Please detect or enter your location.</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Notification Radius</label>
                <select name="radius" class="form-select">
                    @foreach([10, 15, 25, 40, 60] as $r)
                    <option value="{{ $r }}" {{ old('radius', 25) == $r ? 'selected' : '' }}>{{ $r }} miles</option>
                    @endforeach
                </select>
                <div class="radius-hint">Providers within this radius will be notified about your job.</div>
            </div>
        </div>

        <button type="submit" class="btn-submit" id="submitBtn">
            🚀 Post My Job
        </button>
    </form>
</div>

<script>
function detectLocation() {
    const btn    = document.getElementById('detectBtn');
    const status = document.getElementById('locationStatus');
    const latIn  = document.getElementById('lat_input');
    const lngIn  = document.getElementById('lng_input');
    const addrIn = document.getElementById('location_address');

    if (!navigator.geolocation) {
        status.textContent = 'Geolocation not supported by your browser.';
        status.className   = 'location-status error';
        return;
    }

    btn.classList.add('detecting');
    btn.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Detecting...';

    navigator.geolocation.getCurrentPosition(
        pos => {
            latIn.value = pos.coords.latitude.toFixed(7);
            lngIn.value = pos.coords.longitude.toFixed(7);

            // Reverse geocode using nominatim (free, no API key)
            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${pos.coords.latitude}&lon=${pos.coords.longitude}&format=json`)
                .then(r => r.json())
                .then(data => {
                    const display = data.display_name || `${pos.coords.latitude.toFixed(4)}, ${pos.coords.longitude.toFixed(4)}`;
                    addrIn.value = display;
                })
                .catch(() => {
                    addrIn.value = `${pos.coords.latitude.toFixed(4)}, ${pos.coords.longitude.toFixed(4)}`;
                });

            status.textContent = '✓ Location detected successfully!';
            status.className   = 'location-status success';
            btn.classList.remove('detecting');
            btn.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Detected';
        },
        err => {
            status.textContent = 'Could not detect location. Please type your address.';
            status.className   = 'location-status error';
            btn.classList.remove('detecting');
            btn.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Detect';
        }
    );
}

// Auto-detect on load if coords missing
document.addEventListener('DOMContentLoaded', () => {
    const lat = document.getElementById('lat_input').value;
    if (!lat) {
        // Silently try to pre-fill
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(pos => {
                document.getElementById('lat_input').value = pos.coords.latitude.toFixed(7);
                document.getElementById('lng_input').value = pos.coords.longitude.toFixed(7);
                fetch(`https://nominatim.openstreetmap.org/reverse?lat=${pos.coords.latitude}&lon=${pos.coords.longitude}&format=json`)
                    .then(r => r.json())
                    .then(data => {
                        document.getElementById('location_address').value = data.display_name || '';
                        document.getElementById('locationStatus').textContent = '✓ Location auto-detected';
                        document.getElementById('locationStatus').className = 'location-status success';
                    });
            });
        }
    }
});
</script>
@endsection