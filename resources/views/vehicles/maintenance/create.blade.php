@extends('layouts.app')

@section('title', 'Add Maintenance Schedule')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Add Maintenance Schedule</h1>
        <p class="mt-1 text-sm text-gray-600">Create a new maintenance schedule</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ route('maintenance.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <div>
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
                    @error('vehicle_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="service_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Service Type <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="service_type" name="service_type" required
                           value="{{ old('service_type') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="e.g., Oil Change, Tire Rotation">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Additional details...">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Due Date
                        </label>
                        <input type="date" id="due_date" name="due_date"
                               value="{{ old('due_date') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="due_mileage" class="block text-sm font-medium text-gray-700 mb-2">
                            Due Mileage
                        </label>
                        <input type="number" id="due_mileage" name="due_mileage"
                               value="{{ old('due_mileage') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="e.g., 30000">
                    </div>
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                        Priority <span class="text-red-500">*</span>
                    </label>
                    <select id="priority" name="priority" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_recurring" value="1" 
                               {{ old('is_recurring') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Make this a recurring maintenance</span>
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-4" x-data="{ recurring: {{ old('is_recurring', 0) }} }">
                    <div>
                        <label for="recurrence_mileage" class="block text-sm font-medium text-gray-700 mb-2">
                            Recurrence Mileage
                        </label>
                        <input type="number" id="recurrence_mileage" name="recurrence_mileage"
                               value="{{ old('recurrence_mileage') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Every X miles">
                        <p class="mt-1 text-xs text-gray-500">e.g., 5000 for every 5,000 miles</p>
                    </div>

                    <div>
                        <label for="recurrence_months" class="block text-sm font-medium text-gray-700 mb-2">
                            Recurrence Months
                        </label>
                        <input type="number" id="recurrence_months" name="recurrence_months"
                               value="{{ old('recurrence_months') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Every X months">
                        <p class="mt-1 text-xs text-gray-500">e.g., 6 for every 6 months</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('maintenance.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Create Schedule
                </button>
            </div>
        </form>
    </div>
</div>
@endsection