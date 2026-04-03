@extends('layouts.app')
@section('title', 'Post a Service Job')
@section('content')
<style>
:root[data-theme="dark"] {
    --card-bg:rgba(26,32,48,.85); --border-color:rgba(0,212,255,.1);
    --input-bg:rgba(0,212,255,.05); --text-primary:#fff;
    --text-secondary:rgba(255,255,255,.7); --text-tertiary:rgba(255,255,255,.45);
    --accent-cyan:#00d4ff; --accent-green:#00ffaa; --accent-warning:#ffaa00;
    --accent-danger:#ff3366;
}
:root[data-theme="light"] {
    --card-bg:rgba(255,255,255,.9); --border-color:rgba(0,0,0,.1);
    --input-bg:rgba(0,0,0,.03); --text-primary:#1a1f36;
    --text-secondary:rgba(26,31,54,.7); --text-tertiary:rgba(26,31,54,.45);
    --accent-cyan:#0066ff; --accent-green:#00cc88; --accent-warning:#ff9500;
    --accent-danger:#ff3366;
}
.pg-container { max-width:760px; margin:0 auto; padding:2rem 1.5rem; }
.back-link { display:inline-flex; align-items:center; gap:.5rem; color:var(--accent-cyan); text-decoration:none; font-size:.875rem; font-weight:600; margin-bottom:1.5rem; transition:all .3s; }
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
.error-msg { color:#ff8099; font-size:.775rem; margin-top:.375rem; }
.info-box { background:rgba(0,212,255,.06); border:1px solid rgba(0,212,255,.15); border-radius:10px; padding:1rem 1.25rem; font-size:.825rem; color:var(--text-secondary); display:flex; gap:.75rem; margin-bottom:1.5rem; }
.info-box svg { width:18px; height:18px; color:var(--accent-cyan); flex-shrink:0; margin-top:1px; }
.btn-submit { width:100%; padding:1rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:12px; color:#000; font-family:'Orbitron',sans-serif; font-weight:800; font-size:.9rem; letter-spacing:.05em; cursor:pointer; transition:all .3s; box-shadow:0 4px 15px rgba(0,212,255,.3); }
.btn-submit:hover { transform:translateY(-2px); box-shadow:0 6px 25px rgba(0,212,255,.5); }

/* ── Saved Locations ── */
.loc-tabs { display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:1rem; }
.loc-tab { display:inline-flex; align-items:center; gap:.4rem; padding:.45rem .9rem;
    background:var(--input-bg); border:1px solid var(--border-color); border-radius:20px;
    font-size:.78rem; font-weight:600; color:var(--text-secondary); cursor:pointer;
    transition:all .25s; user-select:none; }
.loc-tab:hover { border-color:var(--accent-cyan); color:var(--accent-cyan); }
.loc-tab.active { background:rgba(0,212,255,.12); border-color:var(--accent-cyan); color:var(--accent-cyan); }
.loc-tab .del-loc { margin-left:.25rem; color:var(--accent-danger); font-size:.7rem; opacity:.6; }
.loc-tab .del-loc:hover { opacity:1; }
.loc-detect-row { display:flex; gap:.75rem; align-items:flex-start; }
.loc-detect-row .form-input { flex:1; }
.btn-detect { display:inline-flex; align-items:center; gap:.5rem; padding:.75rem 1rem;
    background:rgba(0,212,255,.1); border:1px solid rgba(0,212,255,.3); border-radius:10px;
    color:var(--accent-cyan); font-size:.8rem; font-weight:600; cursor:pointer; white-space:nowrap; transition:all .3s; }
.btn-detect:hover { background:rgba(0,212,255,.2); }
.btn-detect:disabled { opacity:.6; pointer-events:none; }
.loc-status { font-size:.75rem; margin-top:.5rem; }
.loc-status.ok  { color:var(--accent-green); }
.loc-status.err { color:#ff8099; }
.save-loc-row { display:flex; gap:.5rem; align-items:center; margin-top:.75rem; }
.save-loc-input { flex:1; padding:.5rem .875rem; background:var(--input-bg); border:1px solid var(--border-color); border-radius:8px; color:var(--text-primary); font-size:.8rem; font-family:'Chakra Petch',sans-serif; }
.save-loc-input:focus { outline:none; border-color:var(--accent-cyan); }
.btn-save-loc { padding:.5rem .875rem; background:rgba(0,255,170,.1); border:1px solid rgba(0,255,170,.25); border-radius:8px; color:var(--accent-green); font-size:.78rem; font-weight:600; cursor:pointer; transition:all .25s; white-space:nowrap; }
.btn-save-loc:hover { background:rgba(0,255,170,.2); }
.radius-hint { font-size:.75rem; color:var(--text-tertiary); margin-top:.375rem; }

/* ── Provider Map ── */
.map-section { margin-top:1.5rem; }
.map-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:.875rem; }
.map-title { font-family:'Orbitron',sans-serif; font-size:.8rem; font-weight:700; color:var(--accent-cyan); text-transform:uppercase; letter-spacing:.08em; }
.map-count { font-size:.78rem; color:var(--text-tertiary); }
#providerMap { width:100%; height:360px; border-radius:14px; border:1px solid var(--border-color); overflow:hidden; background:var(--input-bg); }
.map-loading { display:flex; align-items:center; justify-content:center; height:360px; color:var(--text-tertiary); font-size:.875rem; gap:.75rem; }
.map-legend { display:flex; gap:1.25rem; margin-top:.75rem; flex-wrap:wrap; }
.legend-item { display:flex; align-items:center; gap:.4rem; font-size:.75rem; color:var(--text-tertiary); }
.legend-dot { width:10px; height:10px; border-radius:50%; }

/* ── Media upload ── */
.preview-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(110px,1fr)); gap:.75rem; margin-top:1rem; }
</style>

<div class="pg-container">
    <a href="{{ route('jobs.index') }}" class="back-link">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to My Jobs
    </a>

    <div class="page-title">Post a <span>Service Job</span></div>
    <p class="page-sub">Tell us what you need — nearby providers will compete to offer you the best price.</p>

    {{-- Assigned Provider Banner --}}
    @if($assignedProvider)
    <div style="background:linear-gradient(135deg,rgba(0,255,170,.1),rgba(0,212,255,.06));border:1px solid rgba(0,255,170,.3);border-radius:14px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div style="display:flex;align-items:center;gap:1rem;">
            <span style="font-size:2rem;">🎯</span>
            <div>
                <div style="font-family:'Orbitron',sans-serif;font-weight:700;font-size:.9rem;color:var(--accent-green);">Direct Job Assignment</div>
                <div style="font-size:.825rem;color:var(--text-secondary);">This job will be sent directly to <strong>{{ $assignedProvider->name }}</strong> ({{ ucfirst(str_replace('_',' ',$assignedProvider->type)) }})</div>
            </div>
        </div>
        <a href="{{ route('jobs.create') }}" style="font-size:.8rem;color:var(--text-tertiary);text-decoration:none;white-space:nowrap;">✕ Post to all providers instead</a>
    </div>
    @elseif($recentProviders->isNotEmpty())
    <div style="background:var(--card-bg);border:1px solid var(--border-color);border-radius:14px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;">
        <div style="font-family:'Orbitron',sans-serif;font-size:.85rem;font-weight:700;margin-bottom:1rem;color:var(--text-secondary);">🔄 Recent Providers — Post Again?</div>
        <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
            @foreach($recentProviders as $rp)
            <a href="{{ route('jobs.create', ['provider_id' => $rp->id]) }}"
               style="display:inline-flex;align-items:center;gap:.625rem;padding:.625rem 1rem;background:rgba(0,212,255,.06);border:1px solid rgba(0,212,255,.2);border-radius:10px;text-decoration:none;transition:all .3s;"
               onmouseover="this.style.borderColor='rgba(0,212,255,.5)'" onmouseout="this.style.borderColor='rgba(0,212,255,.2)'">
                <div>
                    <div style="font-size:.8rem;font-weight:700;color:var(--text-primary);">{{ $rp->name }}</div>
                    <div style="font-size:.72rem;color:var(--text-tertiary);">{{ ucfirst(str_replace('_',' ',$rp->type)) }}@if($rp->rating > 0) · ★{{ number_format($rp->rating,1) }}@endif</div>
                </div>
                <svg width="14" height="14" fill="none" stroke="var(--accent-cyan)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @endforeach
        </div>
    </div>
    @else
    <div class="info-box">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>Your job post will be visible to verified service providers within your selected radius for <strong>24 hours</strong>. Once you accept an offer, the rest are automatically declined.</div>
    </div>
    @endif

    <form method="POST" action="{{ route('jobs.store') }}" id="jobForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="latitude"  id="lat_input"  value="{{ old('latitude') }}">
        <input type="hidden" name="longitude" id="lng_input"  value="{{ old('longitude') }}">
        @if($assignedProvider)
        <input type="hidden" name="assigned_provider_id" value="{{ $assignedProvider->id }}">
        @endif

        {{-- Vehicle & Service --}}
        <div class="form-card">
            <div class="section-title">Vehicle & Service</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Your Vehicle <span class="req">*</span></label>
                    <select name="vehicle_id" class="form-select" required>
                        <option value="">Select vehicle...</option>
                        @foreach($vehicles as $v)
                        <option value="{{ $v->id }}" {{ old('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->full_name }}</option>
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

        {{-- Schedule --}}
        <div class="form-card">
            <div class="section-title">Schedule</div>
            <div class="form-grid">
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

        {{-- Location ── ENHANCED ── --}}
        <div class="form-card">
            <div class="section-title">📍 Service Location</div>
            <p style="font-size:.825rem;color:var(--text-secondary);margin-bottom:1.25rem;">
                Choose where the service should happen — your current location, a saved address, or anywhere you type.
            </p>

            {{-- Saved location quick-picks --}}
            <div id="locTabs" class="loc-tabs">
                {{-- Populated by JS from localStorage --}}
            </div>

            {{-- Address input + detect button --}}
            <div class="form-group">
                <label class="form-label">Address / Area <span class="req">*</span></label>
                <div class="loc-detect-row">
                    <input type="text" name="location_address" id="location_address" class="form-input"
                        placeholder="Type an address, or use a saved location..."
                        value="{{ old('location_address') }}"
                        oninput="onAddressType()">
                    <button type="button" class="btn-detect" id="detectBtn" onclick="detectCurrent()">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Use My Location
                    </button>
                </div>
                <div id="locStatus" class="loc-status"></div>
                @error('latitude')<p class="error-msg">Please set your location.</p>@enderror
            </div>

            {{-- Geocode typed address --}}
            <div id="geocodeHint" style="display:none;margin-top:-.5rem;margin-bottom:.875rem;">
                <button type="button" onclick="geocodeAddress()" style="font-size:.78rem;color:var(--accent-cyan);background:none;border:none;cursor:pointer;padding:0;">
                    🔍 Find coordinates for this address
                </button>
            </div>

            {{-- Save current location --}}
            <div class="save-loc-row" id="saveLocRow" style="display:none;">
                <input type="text" id="saveLocName" class="save-loc-input" placeholder='Name this location, e.g. "Home" or "Office"' maxlength="30">
                <button type="button" class="btn-save-loc" onclick="saveLocation()">💾 Save Location</button>
            </div>

            {{-- Radius --}}
            <div class="form-group" style="margin-top:1.25rem;margin-bottom:0;">
                <label class="form-label">Notification Radius</label>
                <select name="radius" id="radiusSelect" class="form-select">
                    @foreach([10, 15, 25, 40, 60] as $r)
                    <option value="{{ $r }}" {{ old('radius', 25) == $r ? 'selected' : '' }}>{{ $r }} miles</option>
                    @endforeach
                </select>
                <div class="radius-hint">Providers within this radius will be notified about your job.</div>
            </div>

            {{-- ── Location Picker Map ── --}}
            <div style="margin-top:1.25rem;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.625rem;">
                    <span style="font-size:.78rem;color:var(--text-tertiary);">📍 Click or drag the pin to set exact location</span>
                    <span id="pickerStatus" style="font-size:.78rem;color:var(--accent-green);display:none;">✓ Location pinned</span>
                </div>
                <div id="locationPickerMap" style="width:100%;height:340px;border-radius:14px;border:1px solid var(--border-color);overflow:hidden;position:relative;">
                    <div id="mapOverlay" style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;background:rgba(10,14,26,.92);z-index:2;border-radius:14px;gap:.75rem;pointer-events:none;">
                        <svg width="36" height="36" fill="none" stroke="rgba(0,212,255,.5)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span style="font-size:.8rem;color:rgba(255,255,255,.45);">Set your location above to pin it on the map</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Media Upload --}}
        <div class="form-card">
            <div class="section-title">📸 Photos & Videos <span style="font-size:.75rem;font-weight:400;color:var(--text-tertiary);text-transform:none;letter-spacing:0;">(optional)</span></div>
            <div class="form-group full">
                <label class="form-label">Upload Images or Videos</label>
                <div id="dropZone" style="border:2px dashed var(--border-color);border-radius:12px;padding:2rem;text-align:center;cursor:pointer;transition:all .3s;"
                     onclick="document.getElementById('mediaInput').click()"
                     ondragover="event.preventDefault();this.style.borderColor='var(--accent-cyan)'"
                     ondragleave="this.style.borderColor='var(--border-color)'"
                     ondrop="handleDrop(event)">
                    <div style="font-size:2rem;margin-bottom:.5rem;">📷</div>
                    <div style="font-size:.875rem;color:var(--text-secondary);margin-bottom:.25rem;">Click to upload or drag & drop</div>
                    <div style="font-size:.75rem;color:var(--text-tertiary);">Images: JPG, PNG, GIF, WEBP · Videos: MP4, MOV, AVI · Max 50MB each · Up to 10 files</div>
                    <input type="file" id="mediaInput" name="media[]" multiple accept="image/*,video/*" style="display:none" onchange="handleFiles(this.files)">
                </div>
                <div id="previewGrid" class="preview-grid"></div>
                @error('media.*')<p style="color:#ff8099;font-size:.78rem;margin-top:.5rem;">{{ $message }}</p>@enderror
            </div>
        </div>

        <button type="submit" class="btn-submit" id="submitBtn">🚀 Post My Job</button>
    </form>
</div>

<script>
// ── State ───────────────────────────────────────────────────────────────────
let map = null;
let userMarker = null;
let radiusCircle = null;
let providerMarkers = [];
let currentLat = null;
let currentLng = null;
const STORAGE_KEY = 'odovin_saved_locations';

// ── Saved Locations (localStorage) ─────────────────────────────────────────
function getSavedLocations() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); } catch { return []; }
}
function setSavedLocations(locs) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(locs));
}

function renderLocTabs() {
    const locs = getSavedLocations();
    const wrap = document.getElementById('locTabs');

    // Always show "Current Location" tab
    const currentHtml = `
        <div class="loc-tab" id="tab-current" onclick="detectCurrent()">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="3"/><path stroke-linecap="round" stroke-width="2" d="M12 2v3M12 19v3M2 12h3M19 12h3"/>
            </svg>
            Current Location
        </div>`;

    const savedHtml = locs.map((loc, i) => `
        <div class="loc-tab" id="tab-${i}" onclick="applyLocation(${loc.lat}, ${loc.lng}, '${escHtml(loc.name)}', ${i})">
            ${iconFor(loc.name)} ${escHtml(loc.name)}
            <span class="del-loc" onclick="event.stopPropagation();deleteLocation(${i})" title="Remove">✕</span>
        </div>`).join('');

    wrap.innerHTML = currentHtml + savedHtml;
}

function iconFor(name) {
    const n = name.toLowerCase();
    if (n.includes('home')) return '🏠';
    if (n.includes('office') || n.includes('work')) return '🏢';
    return '📍';
}

function escHtml(s) {
    return s.replace(/'/g, "\\'").replace(/</g,'&lt;');
}

function deleteLocation(index) {
    const locs = getSavedLocations();
    locs.splice(index, 1);
    setSavedLocations(locs);
    renderLocTabs();
}

function saveLocation() {
    const name = document.getElementById('saveLocName').value.trim();
    if (!name) { alert('Please enter a name for this location.'); return; }
    if (!currentLat) { alert('No location set yet.'); return; }
    const locs = getSavedLocations();
    // Replace if same name exists
    const existing = locs.findIndex(l => l.name.toLowerCase() === name.toLowerCase());
    const entry = { name, lat: currentLat, lng: currentLng, address: document.getElementById('location_address').value };
    if (existing >= 0) locs[existing] = entry; else locs.push(entry);
    setSavedLocations(locs);
    document.getElementById('saveLocName').value = '';
    renderLocTabs();
    setStatus('✓ Location saved as "' + name + '"', true);
}

// ── Apply a location to the form ────────────────────────────────────────────
function applyLocation(lat, lng, label, tabIndex) {
    currentLat = lat;
    currentLng = lng;
    document.getElementById('lat_input').value = lat;
    document.getElementById('lng_input').value = lng;

    const locs = getSavedLocations();
    if (tabIndex !== undefined && locs[tabIndex]) {
        document.getElementById('location_address').value = locs[tabIndex].address || label;
    }

    // Highlight active tab
    document.querySelectorAll('.loc-tab').forEach(t => t.classList.remove('active'));
    const tab = document.getElementById(tabIndex !== undefined ? `tab-${tabIndex}` : 'tab-current');
    if (tab) tab.classList.add('active');

    setStatus('✓ Using ' + label, true);
    document.getElementById('saveLocRow').style.display = 'none';
    loadMap(lat, lng);
}

// ── Detect current GPS location ─────────────────────────────────────────────
function detectCurrent() {
    const btn = document.getElementById('detectBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="animation:spin 1s linear infinite"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Detecting...';
    setStatus('Detecting your location...', null);

    if (!navigator.geolocation) {
        setStatus('Geolocation not supported.', false);
        resetBtn(); return;
    }

    navigator.geolocation.getCurrentPosition(
        pos => {
            const lat = parseFloat(pos.coords.latitude.toFixed(7));
            const lng = parseFloat(pos.coords.longitude.toFixed(7));

            document.getElementById('lat_input').value = lat;
            document.getElementById('lng_input').value = lng;
            currentLat = lat; currentLng = lng;

            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('location_address').value = data.display_name || `${lat}, ${lng}`;
                }).catch(() => {
                    document.getElementById('location_address').value = `${lat}, ${lng}`;
                });

            setStatus('✓ Current location detected', true);
            document.getElementById('saveLocRow').style.display = 'flex';
            document.querySelectorAll('.loc-tab').forEach(t => t.classList.remove('active'));
            document.getElementById('tab-current').classList.add('active');
            loadMap(lat, lng);
            resetBtn();
        },
        () => {
            setStatus('Could not detect location. Type your address instead.', false);
            resetBtn();
        }
    );
}

function resetBtn() {
    const btn = document.getElementById('detectBtn');
    btn.disabled = false;
    btn.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Use My Location';
}

// ── Geocode typed address ───────────────────────────────────────────────────
let geocodeTimer = null;
function onAddressType() {
    document.getElementById('geocodeHint').style.display = 'block';
    document.getElementById('saveLocRow').style.display = 'none';
    // Debounce auto-geocode
    clearTimeout(geocodeTimer);
    geocodeTimer = setTimeout(geocodeAddress, 1200);
}

function geocodeAddress() {
    const addr = document.getElementById('location_address').value.trim();
    if (!addr || addr.length < 5) return;
    setStatus('Looking up address...', null);

    fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(addr)}&format=json&limit=1`)
        .then(r => r.json())
        .then(data => {
            if (!data.length) { setStatus('Address not found. Try a different format.', false); return; }
            const lat = parseFloat(data[0].lat);
            const lng = parseFloat(data[0].lon);
            document.getElementById('lat_input').value = lat.toFixed(7);
            document.getElementById('lng_input').value = lng.toFixed(7);
            currentLat = lat; currentLng = lng;
            document.getElementById('geocodeHint').style.display = 'none';
            document.getElementById('saveLocRow').style.display = 'flex';
            setStatus('✓ Location set to ' + data[0].display_name.split(',').slice(0,2).join(','), true);
            loadMap(lat, lng);
        })
        .catch(() => setStatus('Could not geocode address.', false));
}

function setStatus(msg, ok) {
    const el = document.getElementById('locStatus');
    el.textContent = msg;
    el.className = 'loc-status' + (ok === true ? ' ok' : ok === false ? ' err' : '');
}

// ── Location Picker Map ────────────────────────────────────────────────────
let pickerMap = null;
let pickerMarker = null;

function initPickerMap(lat, lng) {
    if (!window.google) { setTimeout(() => initPickerMap(lat, lng), 400); return; }
    const mapEl = document.getElementById('locationPickerMap');
    if (!pickerMap) {
        pickerMap = new google.maps.Map(mapEl, {
            center: { lat: lat || 24.8607, lng: lng || 67.0011 }, // Karachi default
            zoom: lat ? 14 : 11,
            styles: darkMapStyles(),
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: false,
        });
        pickerMap.addListener('click', e => {
            placePinAt(e.latLng.lat(), e.latLng.lng());
        });
    }
    if (lat) {
        pickerMap.setCenter({ lat, lng });
        pickerMap.setZoom(14);
        placePinAt(lat, lng);
        // Hide overlay
        const ov = document.getElementById('mapOverlay');
        if (ov) ov.style.display = 'none';
    }
}

function loadMap(lat, lng) {
    initPickerMap(lat, lng);
}

function placePinAt(lat, lng) {
    if (pickerMarker) pickerMarker.setMap(null);
    pickerMarker = new google.maps.Marker({
        position: { lat, lng },
        map: pickerMap,
        draggable: true,
        title: 'Your service location — drag to adjust',
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            fillColor: '#00d4ff',
            fillOpacity: 1,
            strokeColor: '#ffffff',
            strokeWeight: 3,
            scale: 10,
        },
        animation: google.maps.Animation.DROP,
    });

    // Update coords when dragged
    pickerMarker.addListener('dragend', e => {
        const newLat = e.latLng.lat();
        const newLng = e.latLng.lng();
        document.getElementById('lat_input').value = newLat.toFixed(7);
        document.getElementById('lng_input').value = newLng.toFixed(7);
        currentLat = newLat; currentLng = newLng;
        // Reverse geocode the dragged position
        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${newLat}&lon=${newLng}&format=json`)
            .then(r => r.json())
            .then(d => {
                if (d.display_name) {
                    document.getElementById('location_address').value = d.display_name;
                    setStatus('✓ Location updated', true);
                }
            }).catch(() => {});
    });

    document.getElementById('lat_input').value = lat.toFixed(7);
    document.getElementById('lng_input').value = lng.toFixed(7);
    document.getElementById('pickerStatus').style.display = '';
}

function updateMap() {} // no-op for create page (no provider map)



function darkMapStyles() {
    return [
        { elementType:'geometry', stylers:[{color:'#1a2030'}] },
        { elementType:'labels.text.stroke', stylers:[{color:'#242f3e'}] },
        { elementType:'labels.text.fill', stylers:[{color:'#746855'}] },
        { featureType:'road', elementType:'geometry', stylers:[{color:'#2c3e50'}] },
        { featureType:'road', elementType:'labels.text.fill', stylers:[{color:'#9ca5b3'}] },
        { featureType:'water', elementType:'geometry', stylers:[{color:'#17263c'}] },
        { featureType:'poi', stylers:[{visibility:'off'}] },
        { featureType:'transit', stylers:[{visibility:'off'}] },
    ];
}

// ── Init ────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    renderLocTabs();

    const oldLat = parseFloat(document.getElementById('lat_input').value);
    const oldLng = parseFloat(document.getElementById('lng_input').value);
    if (oldLat && oldLng) {
        currentLat = oldLat; currentLng = oldLng;
    }
    // Always init map (shows default view or restored location)
    initPickerMap(oldLat || null, oldLng || null);
});
</script>

{{-- Google Maps API --}}
<script>
@if(config('services.google_maps.key'))
(function() {
    const s = document.createElement('script');
    s.src = "https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}";
    s.async = true;
    document.head.appendChild(s);
})();
@endif
</script>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>

{{-- Media upload preview ──────────────────────────────────────────────────────────── --}}
<script>
const dt = new DataTransfer();

function handleFiles(files) { Array.from(files).forEach(addFile); }

function handleDrop(e) {
    e.preventDefault();
    document.getElementById('dropZone').style.borderColor = 'var(--border-color)';
    handleFiles(e.dataTransfer.files);
}

function addFile(file) {
    if (dt.files.length >= 10) return alert('Maximum 10 files allowed.');
    if (file.size > 52428800) return alert(`${file.name} is too large. Max 50MB per file.`);
    dt.items.add(file);
    document.getElementById('mediaInput').files = dt.files;
    renderPreview(file, dt.files.length - 1);
}

function removeFile(index) {
    const newDt = new DataTransfer();
    Array.from(dt.files).forEach((f, i) => { if (i !== index) newDt.items.add(f); });
    dt.items.clear();
    Array.from(newDt.files).forEach(f => dt.items.add(f));
    document.getElementById('mediaInput').files = dt.files;
    renderAllPreviews();
}

function renderPreview(file, index) {
    const grid = document.getElementById('previewGrid');
    const isVideo = file.type.startsWith('video');
    const wrap = document.createElement('div');
    wrap.id = `preview-${index}`;
    wrap.style.cssText = 'position:relative;border-radius:10px;overflow:hidden;background:var(--border-color);aspect-ratio:1;';
    wrap.innerHTML = `
        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:2rem;">${isVideo ? '🎬' : '🖼️'}</div>
        <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,.7);padding:.25rem .5rem;font-size:.65rem;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${file.name}</div>
        <button type="button" onclick="removeFile(${index})" style="position:absolute;top:.35rem;right:.35rem;background:rgba(255,51,102,.8);border:none;border-radius:50%;width:22px;height:22px;color:#fff;font-size:.75rem;cursor:pointer;line-height:1;">✕</button>`;
    if (!isVideo) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;object-fit:cover;';
            wrap.insertBefore(img, wrap.firstChild);
        };
        reader.readAsDataURL(file);
    }
    grid.appendChild(wrap);
}

function renderAllPreviews() {
    document.getElementById('previewGrid').innerHTML = '';
    Array.from(dt.files).forEach((f, i) => renderPreview(f, i));
}
</script>

@endsection