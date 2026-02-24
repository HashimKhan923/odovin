@extends('admin.layouts.app')
@section('title', 'Vehicle Details')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.vehicles.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Vehicles
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm">Total Fuel Logs</h3>
            <p class="text-3xl font-bold">{{ $stats['total_fuel_logs'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm">Total Fuel Cost</h3>
            <p class="text-3xl font-bold">${{ number_format($stats['total_fuel_cost'], 2) }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm">Service Records</h3>
            <p class="text-3xl font-bold">{{ $stats['total_service_records'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm">Total Expenses</h3>
            <p class="text-3xl font-bold">${{ number_format($stats['total_expenses'], 2) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-6">{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}</h2>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div><dt class="text-sm font-medium text-gray-500">Owner</dt><dd class="mt-1 text-sm text-gray-900">{{ $vehicle->user->name }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">VIN</dt><dd class="mt-1 text-sm text-gray-900 font-mono">{{ $vehicle->vin }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Type</dt><dd class="mt-1 text-sm text-gray-900">{{ ucfirst($vehicle->type) }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Fuel Type</dt><dd class="mt-1 text-sm text-gray-900">{{ ucfirst($vehicle->fuel_type) }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Mileage</dt><dd class="mt-1 text-sm text-gray-900">{{ number_format($vehicle->current_mileage) }} miles</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Added</dt><dd class="mt-1 text-sm text-gray-900">{{ $vehicle->created_at->format('F d, Y') }}</dd></div>
        </dl>
    </div>
</div>
@endsection
