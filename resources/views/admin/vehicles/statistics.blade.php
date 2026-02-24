@extends('admin.layouts.app')
@section('title', 'Vehicle Statistics')

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Vehicle Statistics</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm">Total Vehicles</h3>
            <p class="text-3xl font-bold">{{ $stats['total_vehicles'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm">Total Mileage</h3>
            <p class="text-3xl font-bold">{{ number_format($stats['total_mileage']) }} mi</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm">Average Year</h3>
            <p class="text-3xl font-bold">{{ number_format($stats['average_year']) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">By Type</h3>
            @foreach($stats['by_type'] as $type => $count)
            <div class="flex justify-between py-2 border-b">
                <span>{{ ucfirst($type) }}</span>
                <span class="font-semibold">{{ $count }}</span>
            </div>
            @endforeach
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">By Fuel Type</h3>
            @foreach($stats['by_fuel_type'] as $fuel => $count)
            <div class="flex justify-between py-2 border-b">
                <span>{{ ucfirst($fuel) }}</span>
                <span class="font-semibold">{{ $count }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
