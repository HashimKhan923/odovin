@extends('layouts.app')

@section('title', 'New Maintenance')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Add Maintenance</h1>
        <p class="mt-1 text-sm text-gray-600">Schedule or record vehicle maintenance</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ route('maintenance.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                {{-- Vehicle --}}
                <div class="md:col-span-2">
                    <label for="vehicle_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Vehicle <span class="text-red-500">*</span>
                    </label>
                    <select id="vehicle_id" name="vehicle_id" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('vehicle_id') border-red-500 @enderror">
                        <option value="">Select a vehicle</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->full_name ?? ($vehicle->make.' '.$vehicle->model) }}
                            </option>
                        @endforeach
                    </select>
                    @error('vehicle_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Service Type --}}
                <div class="md:col-span-2">
                    <label for="service_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Service Type <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="service_type" name="service_type" required
                           value="{{ old('service_type') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('service_type') border-red-500 @enderror">
                    @error('service_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>

                {{-- Due Mileage --}}
                <div>
                    <label for="due_mileage" class="block text-sm font-medium text-gray-700 mb-2">
                        Due Mileage
                    </label>
                    <input type="number" id="due_mileage" name="due_mileage"
                           value="{{ old('due_mileage') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                {{-- Due Date --}}
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Due Date
                    </label>
                    <input type="date" id="due_date" name="due_date"
                           value="{{ old('due_date') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                {{-- Priority --}}
                <div class="md:col-span-2">
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                        Priority <span class="text-red-500">*</span>
                    </label>
                    <select id="priority" name="priority" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach(['low','medium','high','critical'] as $level)
                            <option value="{{ $level }}" {{ old('priority') === $level ? 'selected' : '' }}>
                                {{ ucfirst($level) }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('maintenance.index') }}"
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Save Maintenance
                </button>
            </div>
        </form>
    </div>
</div>
@endsection