@extends('admin.layouts.app')
@section('title', 'Edit Provider')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6"><a href="{{ route('admin.providers.index') }}" class="text-blue-600">← Back</a></div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-6">Edit: {{ $provider->name }}</h1>
        <form method="POST" action="{{ route('admin.providers.update', $provider) }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div><label class="block text-sm font-medium mb-2">Name *</label><input type="text" name="name" value="{{ $provider->name }}" class="w-full border rounded-lg px-4 py-2" required></div>
                <div><label class="block text-sm font-medium mb-2">Email *</label><input type="email" name="email" value="{{ $provider->email }}" class="w-full border rounded-lg px-4 py-2" required></div>
                <div><label class="block text-sm font-medium mb-2">Phone *</label><input type="text" name="phone" value="{{ $provider->phone }}" class="w-full border rounded-lg px-4 py-2" required></div>
                <div><label class="block text-sm font-medium mb-2">Address *</label><textarea name="address" class="w-full border rounded-lg px-4 py-2" rows="3" required>{{ $provider->address }}</textarea></div>
                <div><label class="block text-sm font-medium mb-2">City *</label><input type="text" name="city" value="{{ $provider->city }}" class="w-full border rounded-lg px-4 py-2" required></div>
                <div><label class="block text-sm font-medium mb-2">Services Offered *</label>
                    <select name="services_offered[]" multiple class="w-full border rounded-lg px-4 py-2" required>
                        <option value="oil_change" {{ in_array('oil_change', $provider->services_offered ?? []) ? 'selected' : '' }}>Oil Change</option>
                        <option value="tire_rotation" {{ in_array('tire_rotation', $provider->services_offered ?? []) ? 'selected' : '' }}>Tire Rotation</option>
                        <option value="brake_service" {{ in_array('brake_service', $provider->services_offered ?? []) ? 'selected' : '' }}>Brake Service</option>
                        <option value="general_maintenance" {{ in_array('general_maintenance', $provider->services_offered ?? []) ? 'selected' : '' }}>General Maintenance</option>
                    </select>
                </div>
                <div><label class="block text-sm font-medium mb-2">Latitude</label><input type="text" name="latitude" value="{{ $provider->latitude }}" class="w-full border rounded-lg px-4 py-2"></div>
                <div><label class="block text-sm font-medium mb-2">Longitude</label><input type="text" name="longitude" value="{{ $provider->longitude }}" class="w-full border rounded-lg px-4 py-2"></div>
                <div><label class="block text-sm font-medium mb-2">Status *</label>
                    <select name="status" class="w-full border rounded-lg px-4 py-2" required>
                        <option value="active" {{ $provider->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $provider->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ $provider->status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
            </div>
            <div class="mt-6 flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">Update</button>
                <a href="{{ route('admin.providers.index') }}" class="bg-gray-200 px-6 py-2 rounded-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
