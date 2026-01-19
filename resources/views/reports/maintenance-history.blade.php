@extends('layouts.app')

@section('title', 'Maintenance History')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('reports.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center mb-2">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Reports
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Maintenance History</h1>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <select name="vehicle_id" class="rounded-lg border-gray-300">
                <option value="">All Vehicles</option>
                @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                        {{ $vehicle->full_name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Filter
            </button>
        </form>
    </div>

    @if($records->isEmpty())
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <p class="text-gray-600">No service records found</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vehicle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Provider</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mileage</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cost</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($records as $record)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $record->service_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $record->vehicle->full_name }}</td>
                        <td class="px-6 py-4 text-sm">{{ $record->service_type }}</td>
                        <td class="px-6 py-4 text-sm">{{ $record->serviceProvider->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($record->mileage_at_service) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">${{ number_format($record->cost, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection