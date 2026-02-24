@extends('provider.layouts.app')
@section('title', 'Working Hours')

@push('styles')
<style>
.hours-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:2rem; max-width:680px; }
.col-headers { display:grid; grid-template-columns:140px 1fr 1fr 70px; gap:1rem; padding:.5rem 0; margin-bottom:.5rem; }
.col-hdr { font-size:.7rem; text-transform:uppercase; letter-spacing:.08em; color:var(--text-tertiary); font-weight:700; }
.day-row { display:grid; grid-template-columns:140px 1fr 1fr 70px; gap:1rem; align-items:center; padding:.875rem 0; border-bottom:1px solid rgba(0,212,255,.06); }
.day-row:last-child { border-bottom:none; }
.day-name { font-weight:600; font-size:.875rem; }
.form-input { width:100%; padding:.625rem .875rem; background:var(--input-bg); border:1px solid var(--border-color); border-radius:8px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.875rem; }
.form-input:focus { outline:none; border-color:var(--accent-cyan); }
.form-input:disabled { opacity:.35; cursor:not-allowed; }
.toggle-wrap { display:flex; flex-direction:column; align-items:center; gap:.25rem; }
.toggle { position:relative; display:inline-block; width:44px; height:24px; }
.toggle input { opacity:0; width:0; height:0; }
.slider { position:absolute; cursor:pointer; inset:0; background:rgba(255,255,255,.1); border-radius:12px; transition:.3s; }
.slider::before { content:''; position:absolute; width:18px; height:18px; left:3px; bottom:3px; background:#fff; border-radius:50%; transition:.3s; }
.toggle input:checked + .slider { background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); }
.toggle input:checked + .slider::before { transform:translateX(20px); }
.closed-txt { font-size:.65rem; color:var(--text-tertiary); height:14px; }
.btn-save { padding:.875rem 2rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:12px; color:#000; font-family:'Orbitron',sans-serif; font-weight:800; font-size:.875rem; cursor:pointer; transition:all .3s; }
.btn-save:hover { transform:translateY(-2px); box-shadow:0 6px 25px rgba(0,212,255,.4); }
@media(max-width:580px) { .col-headers,.day-row { grid-template-columns:1fr 1fr; } .day-row .day-name,.col-headers .col-hdr:first-child { grid-column:span 2; } }
</style>
@endpush

@section('content')
<div class="hours-card">
    <div style="margin-bottom:2rem;">
        <h2 style="font-family:'Orbitron',sans-serif;font-size:1.25rem;font-weight:700;margin-bottom:.375rem;">Working Hours</h2>
        <p style="font-size:.875rem;color:var(--text-tertiary);">Set your availability for each day of the week</p>
    </div>

    <form action="{{ route('provider.hours.update') }}" method="POST">
        @csrf @method('PUT')

        @php
        $hours = $provider->working_hours ?? [];
        $days  = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        $defaultOpen = ['monday','tuesday','wednesday','thursday','friday'];
        @endphp

        <div class="col-headers">
            <div class="col-hdr">Day</div>
            <div class="col-hdr">Open</div>
            <div class="col-hdr">Close</div>
            <div class="col-hdr">Active</div>
        </div>

        @foreach($days as $day)
        @php
        $key     = strtolower($day);
        $dayData = $hours[$key] ?? [
            'open'    => '08:00',
            'close'   => '17:00',
            'is_open' => in_array($key, $defaultOpen),
        ];
        $isOpen  = (bool) ($dayData['is_open'] ?? false);
        @endphp
        <div class="day-row" id="row_{{ $key }}">
            <div class="day-name">{{ $day }}</div>
            <div>
                <input type="time" name="working_hours[{{ $key }}][open]"
                       class="form-input" value="{{ $dayData['open'] ?? '08:00' }}"
                       {{ !$isOpen ? 'disabled' : '' }} id="open_{{ $key }}">
            </div>
            <div>
                <input type="time" name="working_hours[{{ $key }}][close]"
                       class="form-input" value="{{ $dayData['close'] ?? '17:00' }}"
                       {{ !$isOpen ? 'disabled' : '' }} id="close_{{ $key }}">
            </div>
            <div class="toggle-wrap">
                <label class="toggle">
                    <input type="checkbox" name="working_hours[{{ $key }}][is_open]"
                           value="1" {{ $isOpen ? 'checked' : '' }}
                           onchange="toggleDay('{{ $key }}', this.checked)">
                    <span class="slider"></span>
                </label>
                <div class="closed-txt" id="lbl_{{ $key }}">{{ !$isOpen ? 'Closed' : '' }}</div>
            </div>
        </div>
        @endforeach

        <div style="display:flex;justify-content:flex-end;margin-top:1.5rem;">
            <button type="submit" class="btn-save">Save Hours</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleDay(key, open) {
    document.getElementById('open_'  + key).disabled = !open;
    document.getElementById('close_' + key).disabled = !open;
    document.getElementById('lbl_'   + key).textContent = open ? '' : 'Closed';
}
</script>
@endpush

@endsection