@extends('layouts.app')

@section('title', 'Service Bookings')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Service Bookings</h1>
            <p class="mt-1 text-sm text-gray-600">Manage your service appointments</p>
        </div>
        <a href="{{ route('bookings.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Booking
        </a>
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
            
            <select name="status" class="rounded-lg border-gray-300">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Filter
            </button>
            <a href="{{ route('bookings.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                Clear
            </a>
        </form>
    </div>

    @if($bookings->isEmpty())
        <div class="bg-white rounded-lg shadow-lg p-12 text-center">
            <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No bookings yet</h3>
            <p class="text-gray-600 mb-6">Book your first service appointment</p>
            <a href="{{ route('bookings.create') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                Create Booking
            </a>
        </div>
    @else
        <div class="grid gap-4">
            @foreach($bookings as $booking)
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-bold text-gray-900">{{ $booking->service_type }}</h3>
                            <span class="px-3 py-1 text-xs font-medium rounded-full
                                {{ $booking->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($booking->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : 
                                   ($booking->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' :
                                   ($booking->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">{{ $booking->vehicle->full_name }}</p>
                        <p class="text-sm text-gray-600 mb-2">
                            <strong>Provider:</strong> {{ $booking->serviceProvider->name }}
                        </p>
                        <p class="text-sm text-gray-600">
                            <strong>Scheduled:</strong> {{ $booking->scheduled_date->format('M d, Y g:i A') }}
                        </p>
                        @if($booking->estimated_cost)
                            <p class="text-sm text-gray-600">
                                <strong>Estimated Cost:</strong> ${{ number_format($booking->estimated_cost, 2) }}
                            </p>
                        @endif
                    </div>
                    
                    <div class="flex gap-2">
                        <a href="{{ route('bookings.show', $booking) }}" class="text-blue-600 hover:text-blue-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                        @if(in_array($booking->status, ['pending', 'confirmed']))
                            <a href="{{ route('bookings.edit', $booking) }}" class="text-gray-600 hover:text-gray-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
    @endif
</div>
@endsection