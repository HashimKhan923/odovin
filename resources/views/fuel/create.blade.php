@extends('layouts.app')

@section('title', 'Add Fuel Log')

@section('content')
<div class="max-w-3xl mx-auto px-4">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Add Fuel Log</h1>
        <p class="text-sm text-gray-600">Track your fuel usage and cost</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('fuel.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Vehicle *</label>
                    <select name="vehicle_id" required class="w-full rounded-lg border-gray-300">
                        <option value="">Select vehicle</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">
                                {{ $vehicle->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Fill Date *</label>
                    <input type="date" name="fill_date" required
                           class="w-full rounded-lg border-gray-300"
                           value="{{ now()->toDateString() }}">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Odometer *</label>
                    <input type="number" name="odometer" required
                           class="w-full rounded-lg border-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Gallons *</label>
                    <input type="number" step="0.01" name="gallons" required
                           class="w-full rounded-lg border-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Price / Gallon *</label>
                    <input type="number" step="0.01" name="price_per_gallon" required
                           class="w-full rounded-lg border-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Total Cost *</label>
                    <input type="number" step="0.01" name="total_cost" required
                           class="w-full rounded-lg border-gray-300">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_full_tank" value="1" checked>
                    <span class="text-sm">Full tank fill-up</span>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Gas Station</label>
                    <input type="text" name="gas_station"
                           class="w-full rounded-lg border-gray-300">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Notes</label>
                    <textarea name="notes" rows="3"
                              class="w-full rounded-lg border-gray-300"></textarea>
                </div>
            </div>

            <div class="flex justify-end mt-6 gap-3">
                <a href="{{ route('fuel.index') }}"
                   class="px-4 py-2 border rounded-lg text-gray-700">
                    Cancel
                </a>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                    Save Fuel Log
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
