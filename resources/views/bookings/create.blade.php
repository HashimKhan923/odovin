@extends('layouts.app')

@section('title', 'New Booking')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Book a Service</h1>
        <p class="mt-1 text-sm text-gray-600">Schedule your vehicle service appointment</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ route('bookings.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="md:col-span-2">
                    <label for="vehicle_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Vehicle <span class="text-red-500">*</span>
                    </label>
                    <select id="vehicle_id" name="vehicle_id" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('vehicle_id') border-red-500 @enderror">
                        <option value="">Select a vehicle</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $vehicleId) == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->full_name }} ({{ $vehicle->license_plate ?? 'No Plate' }})
                            </option>
                        @endforeach
                    </select>
                    @error('vehicle_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="service_provider_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Service Provider <span class="text-red-500">*</span>
                    </label>
                    <select id="service_provider_id" name="service_provider_id" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('service_provider_id') border-red-500 @enderror">
                        <option value="">Select a provider</option>
                        @foreach($providers as $provider)
                            <option value="{{ $provider->id }}" {{ old('service_provider_id') == $provider->id ? 'selected' : '' }}>
                                {{ $provider->name }} - {{ $provider->city }} 
                                @if($provider->rating > 0)(⭐ {{ number_format($provider->rating, 1) }})@endif
                            </option>
                        @endforeach
                    </select>
                    @error('service_provider_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        <a href="{{ route('providers.index') }}" class="text-blue-600 hover:text-blue-800">Browse all providers</a>
                    </p>
                </div>

                <div class="md:col-span-2">
                    <label for="service_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Service Type <span class="text-red-500">*</span>
                    </label>
                    <select id="service_type" name="service_type" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select service type</option>
                        @foreach($serviceTypes as $type)
                            <option value="{{ $type }}" {{ old('service_type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="3" required
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Describe the service needed...">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="scheduled_date" name="scheduled_date" required
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           value="{{ old('scheduled_date') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="scheduled_time" class="block text-sm font-medium text-gray-700 mb-2">
                        Time <span class="text-red-500">*</span>
                    </label>
                    <input type="time" id="scheduled_time" name="scheduled_time" required
                           value="{{ old('scheduled_time', '09:00') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="estimated_cost" class="block text-sm font-medium text-gray-700 mb-2">
                        Estimated Cost
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                        <input type="number" id="estimated_cost" name="estimated_cost" step="0.01"
                               value="{{ old('estimated_cost') }}"
                               class="w-full pl-7 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label for="customer_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Additional Notes
                    </label>
                    <textarea id="customer_notes" name="customer_notes" rows="2"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Any special instructions or preferences...">{{ old('customer_notes') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('bookings.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Create Booking
                </button>
            </div>
        </form>
    </div>
</div>
@endsection