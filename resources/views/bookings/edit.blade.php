@extends('layouts.app')

@section('title', 'Edit Booking')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Booking</h1>
        <p class="mt-1 text-sm text-gray-600">Update your service booking</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ route('bookings.update', $booking) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-600 mb-2"><strong>Service Type:</strong> {{ $booking->service_type }}</p>
                    <p class="text-sm text-gray-600 mb-2"><strong>Provider:</strong> {{ $booking->serviceProvider->name }}</p>
                    <p class="text-sm text-gray-600"><strong>Vehicle:</strong> {{ $booking->vehicle->full_name }}</p>
                </div>

                <div>
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="scheduled_date" name="scheduled_date" required
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           value="{{ old('scheduled_date', $booking->scheduled_date->format('Y-m-d')) }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="scheduled_time" class="block text-sm font-medium text-gray-700 mb-2">
                        Time <span class="text-red-500">*</span>
                    </label>
                    <input type="time" id="scheduled_time" name="scheduled_time" required
                           value="{{ old('scheduled_time', $booking->scheduled_date->format('H:i')) }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="estimated_cost" class="block text-sm font-medium text-gray-700 mb-2">
                        Estimated Cost
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                        <input type="number" id="estimated_cost" name="estimated_cost" step="0.01"
                               value="{{ old('estimated_cost', $booking->estimated_cost) }}"
                               class="w-full pl-7 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label for="customer_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Additional Notes
                    </label>
                    <textarea id="customer_notes" name="customer_notes" rows="3"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Any special instructions...">{{ old('customer_notes', $booking->customer_notes) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('bookings.show', $booking) }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Update Booking
                </button>
            </div>
        </form>
    </div>
</div>
@endsection