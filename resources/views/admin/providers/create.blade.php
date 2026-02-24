@extends('admin.layouts.app')
@section('title', 'Create Provider')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6"><a href="{{ route('admin.providers.index') }}" class="text-blue-600">← Back</a></div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-6">Create Service Provider</h1>
<form method="POST" action="{{ route('admin.providers.store') }}">
    @csrf

    <div class="space-y-4">

        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium mb-2">Name *</label>
            <input type="text" name="name" class="w-full border rounded-lg px-4 py-2" value="{{ old('name') }}" required>
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium mb-2">Email *</label>
            <input type="email" name="email" class="w-full border rounded-lg px-4 py-2" value="{{ old('email') }}" required>
        </div>

        {{-- Phone --}}
        <div>
            <label class="block text-sm font-medium mb-2">Phone *</label>
            <input type="text" name="phone" class="w-full border rounded-lg px-4 py-2" value="{{ old('phone') }}" required>
        </div>

        {{-- Address --}}
        <div>
            <label class="block text-sm font-medium mb-2">Address *</label>
            <textarea name="address" rows="3" class="w-full border rounded-lg px-4 py-2" required>{{ old('address') }}</textarea>
        </div>

        {{-- City --}}
        <div>
            <label class="block text-sm font-medium mb-2">City *</label>
            <input type="text" name="city" class="w-full border rounded-lg px-4 py-2" value="{{ old('city') }}" required>
        </div>

        {{-- State --}}
        <div>
            <label class="block text-sm font-medium mb-2">State</label>
            <input type="text" name="state" class="w-full border rounded-lg px-4 py-2" value="{{ old('state') }}">
        </div>

        {{-- Zip Code --}}
        <div>
            <label class="block text-sm font-medium mb-2">Zip Code</label>
            <input type="text" name="zip_code" class="w-full border rounded-lg px-4 py-2" value="{{ old('zip_code') }}">
        </div>

        {{-- Services Offered --}}
        <div>
            <label class="block text-sm font-medium mb-2">Services Offered *</label>
            <select name="services_offered[]" multiple class="w-full border rounded-lg px-4 py-2" required>
                <option value="oil_change" {{ in_array('oil_change', old('services_offered', [])) ? 'selected' : '' }}>Oil Change</option>
                <option value="tire_rotation" {{ in_array('tire_rotation', old('services_offered', [])) ? 'selected' : '' }}>Tire Rotation</option>
                <option value="brake_service" {{ in_array('brake_service', old('services_offered', [])) ? 'selected' : '' }}>Brake Service</option>
                <option value="general_maintenance" {{ in_array('general_maintenance', old('services_offered', [])) ? 'selected' : '' }}>General Maintenance</option>
            </select>
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-medium mb-2">Description</label>
            <textarea name="description" rows="3" class="w-full border rounded-lg px-4 py-2">{{ old('description') }}</textarea>
        </div>

        {{-- Website --}}
        <div>
            <label class="block text-sm font-medium mb-2">Website</label>
            <input type="url" name="website" class="w-full border rounded-lg px-4 py-2" value="{{ old('website') }}">
        </div>

        {{-- Latitude --}}
        <div>
            <label class="block text-sm font-medium mb-2">Latitude</label>
            <input type="text" name="latitude" class="w-full border rounded-lg px-4 py-2" value="{{ old('latitude') }}">
        </div>

        {{-- Longitude --}}
        <div>
            <label class="block text-sm font-medium mb-2">Longitude</label>
            <input type="text" name="longitude" class="w-full border rounded-lg px-4 py-2" value="{{ old('longitude') }}">
        </div>

        {{-- Status --}}
        <div>
            <label class="block text-sm font-medium mb-2">Status *</label>
            <select name="status" class="w-full border rounded-lg px-4 py-2" required>
                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>
        </div>

    </div>

    <div class="mt-6 flex gap-4">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
            Create
        </button>

        <a href="{{ route('admin.providers.index') }}" class="bg-gray-200 px-6 py-2 rounded-lg">
            Cancel
        </a>
    </div>
</form>

    </div>
</div>
@endsection
