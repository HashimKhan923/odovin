@extends('admin.layouts.app')
@section('title', 'Edit — ' . $provider->name)
@section('content')

<a href="{{ route('admin.providers.show', $provider) }}" class="back-link">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    Back to Provider
</a>

<div style="max-width:760px;">
    <div class="page-header">
        <div>
            <div class="page-title">Edit Provider</div>
            <div class="page-sub">{{ $provider->name }}</div>
        </div>
        <div style="display:flex;gap:.75rem;">
            <form method="POST" action="{{ route('admin.providers.toggle-status', $provider) }}">
                @csrf
                <button type="submit" class="btn btn-sm {{ $provider->status === 'active' ? 'btn-danger' : 'btn-success' }}">
                    {{ $provider->status === 'active' ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.providers.update', $provider) }}">
        @csrf @method('PUT')

        {{-- Basic Info --}}
        <div class="card" style="margin-bottom:1.5rem;">
            <div class="card-title">Basic Information</div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Name <span style="color:var(--accent-danger)">*</span></label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', $provider->name) }}" required>
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Email <span style="color:var(--accent-danger)">*</span></label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $provider->email) }}" required>
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Phone <span style="color:var(--accent-danger)">*</span></label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone', $provider->phone) }}" required>
                    @error('phone')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Website</label>
                    <input type="url" name="website" class="form-input" value="{{ old('website', $provider->website) }}" placeholder="https://">
                    @error('website')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group" style="grid-column:span 2;">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea" rows="3">{{ old('description', $provider->description) }}</textarea>
                    @error('description')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Location --}}
        <div class="card" style="margin-bottom:1.5rem;">
            <div class="card-title">Location</div>
            <div class="grid-2">
                <div class="form-group" style="grid-column:span 2;">
                    <label class="form-label">Street Address <span style="color:var(--accent-danger)">*</span></label>
                    <textarea name="address" class="form-textarea" rows="2" required>{{ old('address', $provider->address) }}</textarea>
                    @error('address')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">City <span style="color:var(--accent-danger)">*</span></label>
                    <input type="text" name="city" class="form-input" value="{{ old('city', $provider->city) }}" required>
                    @error('city')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">State / Province</label>
                    <input type="text" name="state" class="form-input" value="{{ old('state', $provider->state) }}">
                    @error('state')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Zip / Postal Code</label>
                    <input type="text" name="zip_code" class="form-input" value="{{ old('zip_code', $provider->zip_code) }}">
                    @error('zip_code')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group" style="grid-column:span 2;display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label class="form-label">Latitude</label>
                        <input type="text" name="latitude" class="form-input" value="{{ old('latitude', $provider->latitude) }}" placeholder="e.g. 37.7749">
                        @error('latitude')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label">Longitude</label>
                        <input type="text" name="longitude" class="form-input" value="{{ old('longitude', $provider->longitude) }}" placeholder="e.g. -122.4194">
                        @error('longitude')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Services & Status --}}
        <div class="card" style="margin-bottom:1.5rem;">
            <div class="card-title">Services & Status</div>
            <div class="form-group">
                <label class="form-label">Services Offered <span style="color:var(--accent-danger)">*</span></label>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:.625rem;margin-top:.25rem;">
                    @php
                        $allServices = [
                            'oil_change' => 'Oil Change',
                            'tire_rotation' => 'Tire Rotation',
                            'brake_service' => 'Brake Service',
                            'general_maintenance' => 'General Maintenance',
                            'engine_repair' => 'Engine Repair',
                            'transmission' => 'Transmission',
                            'ac_service' => 'A/C Service',
                            'electrical' => 'Electrical',
                            'body_work' => 'Body Work',
                            'detailing' => 'Detailing',
                            'inspection' => 'Inspection',
                            'towing' => 'Towing',
                        ];
                        $currentServices = old('services_offered', $provider->services_offered ?? []);
                    @endphp
                    @foreach($allServices as $val => $label)
                    <label style="display:flex;align-items:center;gap:.625rem;padding:.625rem .875rem;background:rgba(255,255,255,.03);border:1px solid {{ in_array($val, (array)$currentServices) ? 'rgba(0,212,255,.35)' : 'var(--border-color)' }};border-radius:8px;cursor:pointer;transition:all .2s;"
                        onmouseover="this.style.borderColor='rgba(0,212,255,.3)'"
                        onmouseout="this.style.borderColor='{{ in_array($val, (array)$currentServices) ? 'rgba(0,212,255,.35)' : 'var(--border-color)' }}'">
                        <input type="checkbox" name="services_offered[]" value="{{ $val }}"
                            {{ in_array($val, (array)$currentServices) ? 'checked' : '' }}
                            style="accent-color:var(--accent-cyan);width:15px;height:15px;">
                        <span style="font-size:.82rem;">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
                @error('services_offered')<div class="form-error" style="margin-top:.5rem;">{{ $message }}</div>@enderror
            </div>
            <div class="form-group" style="max-width:200px;margin-top:1.25rem;">
                <label class="form-label">Status <span style="color:var(--accent-danger)">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="active" {{ old('status', $provider->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $provider->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ old('status', $provider->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
                @error('status')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div style="display:flex;gap:.875rem;">
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save Changes
            </button>
            <a href="{{ route('admin.providers.show', $provider) }}" class="btn btn-secondary">Cancel</a>
            <form method="POST" action="{{ route('admin.providers.destroy', $provider) }}" style="margin-left:auto;" onsubmit="return confirm('Permanently delete {{ $provider->name }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete Provider</button>
            </form>
        </div>
    </form>
</div>

@endsection