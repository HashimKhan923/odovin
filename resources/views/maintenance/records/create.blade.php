@extends('layouts.app')

@section('title', 'Add Service Record')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Add Service Record</h1>
        <p class="mt-1 text-sm text-gray-600">Document a completed service</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ route('maintenance.records.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="md:col-span-2">
                    <label for="vehicle_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Vehicle <span class="text-red-500">*</span>
                    </label>
                    <select id="vehicle_id" name="vehicle_id" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select a vehicle</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="service_provider_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Service Provider
                    </label>
                    <select id="service_provider_id" name="service_provider_id"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select provider</option>
                        @foreach($providers as $provider)
                            <option value="{{ $provider->id }}" {{ old('service_provider_id') == $provider->id ? 'selected' : '' }}>
                                {{ $provider->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="service_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Service Type <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="service_type" name="service_type" required
                           value="{{ old('service_type') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="e.g., Oil Change">
                </div>

                <div>
                    <label for="service_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Service Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="service_date" name="service_date" required
                           value="{{ old('service_date', date('Y-m-d')) }}"
                           max="{{ date('Y-m-d') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="2" required
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="What was done...">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="mileage_at_service" class="block text-sm font-medium text-gray-700 mb-2">
                        Mileage at Service <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="mileage_at_service" name="mileage_at_service" required
                           value="{{ old('mileage_at_service') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">
                        Cost <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                        <input type="number" id="cost" name="cost" step="0.01" required
                               value="{{ old('cost') }}"
                               class="w-full pl-7 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label for="invoice_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Invoice Number
                    </label>
                    <input type="text" id="invoice_number" name="invoice_number"
                           value="{{ old('invoice_number') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="invoice_file" class="block text-sm font-medium text-gray-700 mb-2">
                        Invoice File
                    </label>
                    <input type="file" id="invoice_file" name="invoice_file" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notes
                    </label>
                    <textarea id="notes" name="notes" rows="2"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('maintenance.records.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Add Service Record
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
