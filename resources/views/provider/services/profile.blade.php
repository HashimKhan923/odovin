@extends('provider.layouts.app')
@section('title', 'Business Profile')

@push('styles')
<style>
.form-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:2rem; max-width:800px; }
.form-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; }
.form-group { margin-bottom:1.25rem; }
.form-label { display:block; font-size:.8rem; font-weight:600; color:var(--text-secondary); margin-bottom:.5rem; }
.form-input, .form-select, .form-textarea { width:100%; padding:.75rem 1rem; background:var(--input-bg); border:1px solid var(--border-color); border-radius:10px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.875rem; transition:all .3s; }
.form-input:focus, .form-select:focus, .form-textarea:focus { outline:none; border-color:var(--accent-cyan); box-shadow:0 0 0 3px rgba(0,212,255,.1); }
.form-select option { background:rgba(18,24,39,1); }
.form-textarea { resize:vertical; min-height:100px; }
.section-title { font-family:'Orbitron',sans-serif; font-size:.875rem; font-weight:700; color:var(--accent-cyan); margin:1.5rem 0 1rem; padding-bottom:.5rem; border-bottom:1px solid var(--border-color); }
.services-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:.625rem; }
.svc-check { display:flex; align-items:center; gap:.625rem; padding:.625rem .875rem; background:rgba(0,212,255,.03); border:1px solid var(--border-color); border-radius:8px; cursor:pointer; transition:all .25s; }
.svc-check:hover { border-color:rgba(0,212,255,.3); }
.svc-check input { accent-color:var(--accent-cyan); width:16px; height:16px; cursor:pointer; }
.svc-check label { font-size:.8rem; cursor:pointer; }
.btn-save { padding:.875rem 2rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:12px; color:#000; font-family:'Orbitron',sans-serif; font-weight:800; font-size:.875rem; cursor:pointer; transition:all .3s; }
.btn-save:hover { transform:translateY(-2px); box-shadow:0 6px 25px rgba(0,212,255,.4); }
@media(max-width:640px) { .form-grid-2 { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
<div class="form-card">
    <div style="margin-bottom:2rem;">
        <h2 style="font-family:'Orbitron',sans-serif;font-size:1.25rem;font-weight:700;margin-bottom:.375rem;">Business Profile</h2>
        <p style="font-size:.875rem;color:var(--text-tertiary);">Update your business information visible to customers</p>
    </div>

    <form action="{{ route('provider.profile.update') }}" method="POST">
        @csrf @method('PUT')

        <div class="section-title">Business Information</div>
        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Business Name *</label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $provider->name) }}" required>
                @error('name')<div style="color:#ff3366;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Business Type *</label>
                <select name="type" class="form-select" required>
                    @foreach($types as $key => $label)
                    <option value="{{ $key }}" {{ old('type', $provider->type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Phone Number *</label>
                <input type="tel" name="phone" class="form-input" value="{{ old('phone', $provider->phone) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" class="form-input" value="{{ old('email', $provider->email) }}" required>
            </div>
        </div>

        <div class="section-title">Location</div>
        <div class="form-group">
            <label class="form-label">Street Address *</label>
            <input type="text" name="address" class="form-input" value="{{ old('address', $provider->address) }}" required>
        </div>
        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">City *</label>
                <input type="text" name="city" class="form-input" value="{{ old('city', $provider->city) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">State *</label>
                <input type="text" name="state" class="form-input" value="{{ old('state', $provider->state) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">ZIP Code *</label>
                <input type="text" name="zip_code" class="form-input" value="{{ old('zip_code', $provider->zip_code) }}" required>
            </div>
        </div>

        <div class="section-title">Services Offered</div>
        @php
        $allServices = ['Oil Change','Tire Rotation','Brake Service','Battery Replacement','Air Filter Replacement','Transmission Service','Engine Diagnostics','Wheel Alignment','Detailing','Full Inspection','AC Service','Coolant Flush','Spark Plug Replacement','EV Battery Check','Windshield Repair','Towing','Paint Correction','Other'];
        $current = is_array($provider->services_offered) ? $provider->services_offered : [];
        @endphp
        <div class="services-grid">
            @foreach($allServices as $svc)
            <div class="svc-check">
                <input type="checkbox" name="services_offered[]" id="svc_{{ Str::slug($svc) }}" value="{{ $svc }}" {{ in_array($svc, $current) ? 'checked' : '' }}>
                <label for="svc_{{ Str::slug($svc) }}">{{ $svc }}</label>
            </div>
            @endforeach
        </div>

        <div class="section-title">About Your Business</div>
        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-textarea" placeholder="Tell customers about your expertise, certifications, years of experience...">{{ old('description', $provider->description ?? '') }}</textarea>
        </div>

        <div style="display:flex;justify-content:flex-end;margin-top:1.5rem;">
            <button type="submit" class="btn-save">Save Profile</button>
        </div>
    </form>
</div>
@endsection