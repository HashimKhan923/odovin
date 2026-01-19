@extends('layouts.app')

@section('title', 'Edit Vehicle')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Vehicle</h1>
        <p class="mt-1 text-sm text-gray-600">Update your vehicle information</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ route('vehicles.update', $vehicle) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- VIN (Read-only) -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">VIN Number</label>
                <input type="text" 
                       value="{{ $vehicle->vin }}" 
                       disabled
                       class="w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm">
                <p class="mt-1 text-xs text-gray-500">VIN cannot be changed</p>
            </div>

            <!-- Vehicle Info (Read-only) -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <p class="text-lg font-semibold text-gray-900 mb-2">{{ $vehicle->full_name }}</p>
                <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                    @if($vehicle->trim)
                        <div>Trim: {{ $vehicle->trim }}</div>
                    @endif
                    @if($vehicle->engine)
                        <div>Engine: {{ $vehicle->engine }}</div>
                    @endif
                </div>
            </div>

            <!-- Editable Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="license_plate" class="block text-sm font-medium text-gray-700 mb-2">
                        License Plate
                    </label>
                    <input type="text" 
                           id="license_plate" 
                           name="license_plate" 
                           value="{{ old('license_plate', $vehicle->license_plate) }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                        Color
                    </label>
                    <input type="text" 
                           id="color" 
                           name="color" 
                           value="{{ old('color', $vehicle->color) }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="current_mileage" class="block text-sm font-medium text-gray-700 mb-2">
                        Current Mileage
                    </label>
                    <input type="number" 
                           id="current_mileage" 
                           name="current_mileage" 
                           min="{{ $vehicle->current_mileage }}"
                           value="{{ old('current_mileage', $vehicle->current_mileage) }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Cannot be less than {{ number_format($vehicle->current_mileage) }} mi</p>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status
                    </label>
                    <select id="status" 
                            name="status" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="active" {{ $vehicle->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $vehicle->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="sold" {{ $vehicle->status === 'sold' ? 'selected' : '' }}>Sold</option>
                    </select>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('vehicles.show', $vehicle) }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Update Vehicle
                </button>
            </div>
        </form>
    </div>
</div>
@endsection