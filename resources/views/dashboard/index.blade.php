@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-full">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-1 text-sm text-gray-600">Welcome back! Here's your vehicle overview.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-600">Total Vehicles</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_vehicles'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-600">Active Bookings</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_bookings'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-600">Pending Maintenance</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_maintenance'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-600">This Month</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format($stats['month_expenses'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <!-- Icon -->
                <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                    <!-- Fuel / Gas Pump Icon -->
                    <svg class="h-6 w-6 text-yellow-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 3h8v18H3V3zm8 0h4l3 3v6a2 2 0 002 2v5a2 2 0 01-2 2h-1m-6-9h4" />
                    </svg>
                </div>

                <!-- Content -->
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-600">Fuel This Month</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        ${{ number_format($stats['fuel_cost_month'], 2) }}
                    </p>

                    <a href="{{ route('fuel.index') }}"
                    class="text-sm text-blue-600 hover:underline mt-1 inline-block">
                        View fuel logs
                    </a>
                </div>
            </div>
        </div>



    {{-- Avg MPG --}}
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-500">Avg MPG (30 days)</p>
        <p class="text-2xl font-semibold text-gray-900">
            {{ $fuelInsights['avg_mpg_30'] ?? '—' }}
        </p>
    </div>

    {{-- Fuel Cost Per Mile --}}
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-500">Fuel Cost / Mile</p>

        @if($fuelInsights['fuel_cost_mile'])
            <p class="text-2xl font-semibold text-gray-900">
                ${{ number_format($fuelInsights['fuel_cost_mile'], 3) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">
                Based on {{ number_format($fuelInsights['total_miles']) }} miles
            </p>
        @else
            <p class="text-sm text-gray-400">Not enough data</p>
        @endif
    </div>

    {{-- MPG Trend --}}
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-500">MPG Trend</p>

        @if($fuelInsights['mpg_trend'] === 'up')
            <p class="text-2xl font-semibold text-green-600">
                ▲ Improving
            </p>
            <p class="text-xs text-gray-400 mt-1">
                Fuel efficiency is improving
            </p>

        @elseif($fuelInsights['mpg_trend'] === 'down')
            <p class="text-2xl font-semibold text-red-600">
                ▼ Declining
            </p>
            <p class="text-xs text-gray-400 mt-1">
                Consider checking driving or maintenance
            </p>

        @elseif($fuelInsights['mpg_trend'] === 'stable')
            <p class="text-2xl font-semibold text-yellow-600">
                ● Stable
            </p>
            <p class="text-xs text-gray-400 mt-1">
                No major efficiency change
            </p>

        @else
            <p class="text-sm text-gray-400">
                Not enough data
            </p>
        @endif
    </div>
    </div>

    

    

        @if($stats['open_recalls'] > 0)
<div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
    <p class="font-medium text-red-800">
        ⚠ {{ $stats['open_recalls'] }} open safety recall(s) detected.
    </p>
    <a href="{{ route('recalls.index') }}" class="text-sm text-red-700 underline">
        Review recalls
    </a>
</div>
@endif

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Primary Vehicle Card -->
        <div class="lg:col-span-1">
            @if($primaryVehicle)
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Primary Vehicle</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $primaryVehicle->full_name }}</p>
                            <p class="text-sm text-gray-600">{{ $primaryVehicle->vin }}</p>
                        </div>
                        <div class="pt-3 border-t border-gray-200">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm text-gray-600">Mileage</span>
                                <span class="text-sm font-medium text-gray-900">{{ number_format($primaryVehicle->current_mileage) }} mi</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm text-gray-600">License Plate</span>
                                <span class="text-sm font-medium text-gray-900">{{ $primaryVehicle->license_plate ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Color</span>
                                <span class="text-sm font-medium text-gray-900">{{ $primaryVehicle->color ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="pt-3">
                            <a href="{{ route('vehicles.show', $primaryVehicle) }}" class="block w-full text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600 mb-4">No vehicles added yet</p>
                <a href="{{ route('vehicles.create') }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Add Your First Vehicle
                </a>
            </div>
            @endif
        </div>

        <!-- Upcoming Maintenance -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Upcoming Maintenance</h2>
                        <a href="{{ route('maintenance.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                    </div>
                    @forelse($upcomingMaintenance as $maintenance)
                    <div class="border-b border-gray-200 py-3 last:border-b-0">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $maintenance->service_type }}</p>
                                <p class="text-sm text-gray-600">{{ $maintenance->vehicle->full_name }}</p>
                                <p class="text-xs text-gray-500 mt-1">
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
                    </div>
                    @empty
                    <p class="text-center text-gray-500 py-4">No upcoming maintenance</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>



    <!-- Recent Activity & Expenses -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Bookings -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Bookings</h2>
                    <a href="{{ route('bookings.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                </div>
                @forelse($recentBookings as $booking)
                <div class="border-b border-gray-200 py-3 last:border-b-0">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $booking->service_type }}</p>
                            <p class="text-sm text-gray-600">{{ $booking->serviceProvider->name }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $booking->scheduled_date->format('M d, Y g:i A') }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            {{ $booking->status === 'completed' ? 'bg-green-100 text-green-800' : 
                               ($booking->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-4">No bookings yet</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Expenses -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Expenses</h2>
                    <a href="{{ route('expenses.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                </div>
                @forelse($recentExpenses as $expense)
                <div class="border-b border-gray-200 py-3 last:border-b-0">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $expense->description }}</p>
                            <p class="text-sm text-gray-600">{{ ucfirst($expense->category) }} - {{ $expense->vehicle->full_name }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $expense->expense_date->format('M d, Y') }}</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">${{ number_format($expense->amount, 2) }}</span>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-4">No expenses recorded</p>
                @endforelse
            </div>
        </div>
    </div>

 

@if($upcomingReminders->count())
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Upcoming Reminders</h2>
        <a href="{{ route('reminders.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
    </div>

    <div class="space-y-3">
        @foreach($upcomingReminders as $reminder)
            <div class="flex justify-between items-center border-b pb-2 last:border-b-0">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $reminder->title }}</p>
                    <p class="text-xs text-gray-600">{{ $reminder->vehicle->full_name }}</p>
                </div>
                <span class="text-sm text-gray-500">
                    {{ $reminder->due_date->format('M d, Y') }}
                </span>
            </div>
        @endforeach
    </div>
</div>
@endif

@if($expiringDocuments->count())
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Expiring Documents</h2>

    <div class="space-y-3">
        @foreach($expiringDocuments as $doc)
            <div class="flex justify-between items-center border-b pb-2 last:border-b-0">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $doc->name }}</p>
                    <p class="text-xs text-gray-600">{{ $doc->vehicle->full_name }}</p>
                </div>
                <span class="text-sm text-red-600 font-medium">
                    {{ $doc->expiry_date->format('M d, Y') }}
                </span>
            </div>
        @endforeach
    </div>
</div>
@endif




    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('bookings.create') }}" class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition">
                <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-sm font-medium text-gray-900">Book Service</span>
            </a>
            
            <a href="{{ route('expenses.create') }}" class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition">
                <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span class="text-sm font-medium text-gray-900">Add Expense</span>
            </a>
            
            <a href="{{ route('vehicles.create') }}" class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition">
                <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span class="text-sm font-medium text-gray-900">Add Vehicle</span>
            </a>
            
            <a href="{{ route('providers.index') }}" class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition">
                <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span class="text-sm font-medium text-gray-900">Find Services</span>
            </a>

            <!-- <a href="{{ route('fuel.index') }}"
            class="flex flex-col items-center justify-center p-4
                    border border-gray-200 rounded-lg
                    hover:bg-blue-50 hover:border-blue-300 transition">

                <svg class="w-8 h-8 text-blue-600 mb-2"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                </svg>

                <span class="text-sm font-medium text-gray-900">Fuel Logs</span>
            </a> -->

        </div>
    </div>
</div>
@endsection