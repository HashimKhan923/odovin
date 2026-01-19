@extends('layouts.app')

@section('title', $vehicle->full_name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('vehicles.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center mb-2">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Vehicles
        </a>
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $vehicle->full_name }}</h1>
                <p class="mt-1 text-sm text-gray-600">VIN: {{ $vehicle->vin }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('vehicles.edit', $vehicle) }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                    Edit
                </a>
                @unless($vehicle->is_primary)
                <form action="{{ route('vehicles.set-primary', $vehicle) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        Set as Primary
                    </button>
                </form>
                @endunless
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600 mb-1">Total Expenses</p>
            <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['total_expenses'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600 mb-1">This Month</p>
            <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['this_month_expenses'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600 mb-1">Services</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_services'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600 mb-1">Pending Maintenance</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_maintenance'] }}</p>
        </div>
    </div>

    <!-- Vehicle Details and Actions -->
    <div class="grid lg:grid-cols-3 gap-6 mb-8">
        <!-- Vehicle Info -->
        <div class="lg:col-span-1 bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Vehicle Information</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm text-gray-600">Make & Model</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $vehicle->make }} {{ $vehicle->model }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Year</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $vehicle->year }}</dd>
                </div>
                @if($vehicle->trim)
                <div>
                    <dt class="text-sm text-gray-600">Trim</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $vehicle->trim }}</dd>
                </div>
                @endif
                @if($vehicle->engine)
                <div>
                    <dt class="text-sm text-gray-600">Engine</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $vehicle->engine }}</dd>
                </div>
                @endif
                @if($vehicle->transmission)
                <div>
                    <dt class="text-sm text-gray-600">Transmission</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $vehicle->transmission }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-sm text-gray-600">Current Mileage</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ number_format($vehicle->current_mileage) }} mi</dd>
                </div>
                @if($vehicle->license_plate)
                <div>
                    <dt class="text-sm text-gray-600">License Plate</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $vehicle->license_plate }}</dd>
                </div>
                @endif
                @if($vehicle->color)
                <div>
                    <dt class="text-sm text-gray-600">Color</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $vehicle->color }}</dd>
                </div>
                @endif
            </dl>

            <!-- Update Mileage Form -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <form action="{{ route('vehicles.update-mileage', $vehicle) }}" method="POST">
                    @csrf
                    <label class="block text-sm font-medium text-gray-700 mb-2">Update Mileage</label>
                    <div class="flex gap-2">
                        <input type="number" name="mileage" min="{{ $vehicle->current_mileage }}" 
                               value="{{ $vehicle->current_mileage }}"
                               class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Documents -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Documents</h3>
                    <a href="{{ route('vehicles.documents.create', $vehicle) }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        + Add Document
                    </a>
                </div>
                @if($vehicle->documents->isEmpty())
                    <p class="text-gray-600 text-sm">No documents uploaded yet</p>
                @else
                    <div class="space-y-2">
                        @foreach($vehicle->documents->take(5) as $document)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $document->title }}</p>
                                    <p class="text-xs text-gray-600">
                                        {{ ucfirst($document->type) }}
                                        @if($document->expiry_date)
                                            - Expires {{ $document->expiry_date->format('M d, Y') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('vehicles.documents.download', [$vehicle, $document]) }}" 
                               class="text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    <a href="{{ route('vehicles.documents.index', $vehicle) }}" 
                       class="block mt-4 text-center text-blue-600 hover:text-blue-800 text-sm">
                        View All Documents
                    </a>
                @endif
            </div>

            <!-- Upcoming Maintenance -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Upcoming Maintenance</h3>
                    <a href="{{ route('maintenance.index', ['vehicle_id' => $vehicle->id]) }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View All
                    </a>
                </div>
                @if($vehicle->maintenanceSchedules->isEmpty())
                    <p class="text-gray-600 text-sm">No scheduled maintenance</p>
                @else
                    <div class="space-y-3">
                        @foreach($vehicle->maintenanceSchedules as $maintenance)
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium text-gray-900">{{ $maintenance->service_type }}</p>
                                <p class="text-sm text-gray-600">
                                    Due: {{ $maintenance->due_date?->format('M d, Y') ?? 'N/A' }}
                                    @if($maintenance->due_mileage)
                                        | {{ number_format($maintenance->due_mileage) }} mi
                                    @endif
                                </p>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $maintenance->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($maintenance->status) }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Recent Services -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Services</h3>
            @if($vehicle->serviceRecords->isEmpty())
                <p class="text-gray-600 text-sm">No service history</p>
            @else
                <div class="space-y-3">
                    @foreach($vehicle->serviceRecords as $record)
                    <div class="border-b border-gray-100 pb-3 last:border-0">
                        <p class="font-medium text-gray-900">{{ $record->service_type }}</p>
                        <p class="text-sm text-gray-600">{{ $record->service_date->format('M d, Y') }} - ${{ number_format($record->cost, 2) }}</p>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Recent Expenses -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Expenses</h3>
            @if($vehicle->expenses->isEmpty())
                <p class="text-gray-600 text-sm">No expenses recorded</p>
            @else
                <div class="space-y-3">
                    @foreach($vehicle->expenses as $expense)
                    <div class="flex justify-between items-start border-b border-gray-100 pb-3 last:border-0">
                        <div>
                            <p class="font-medium text-gray-900">{{ $expense->description }}</p>
                            <p class="text-sm text-gray-600">{{ $expense->expense_date->format('M d, Y') }}</p>
                        </div>
                        <span class="font-semibold text-gray-900">${{ number_format($expense->amount, 2) }}</span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
