@extends('layouts.app')

@section('title', 'Maintenance Schedules')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Maintenance Schedules</h1>
            <p class="mt-1 text-sm text-gray-600">Keep your vehicles in top condition</p>
        </div>
        <a href="{{ route('maintenance.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Schedule
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
                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Filter
            </button>
            <a href="{{ route('maintenance.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                Clear
            </a>
        </form>
    </div>

    @if($schedules->isEmpty())
        <div class="bg-white rounded-lg shadow-lg p-12 text-center">
            <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No maintenance schedules</h3>
            <p class="text-gray-600 mb-6">Create your first maintenance schedule</p>
            <a href="{{ route('maintenance.create') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                Add Schedule
            </a>
        </div>
    @else
        <div class="grid gap-4">
            @foreach($schedules as $schedule)
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-bold text-gray-900">{{ $schedule->service_type }}</h3>
                            <span class="px-3 py-1 text-xs font-medium rounded-full
                                {{ $schedule->status === 'overdue' ? 'bg-red-100 text-red-800' : 
                                   ($schedule->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($schedule->status) }}
                            </span>
                            <span class="px-3 py-1 text-xs font-medium rounded-full
                                {{ $schedule->priority === 'critical' ? 'bg-red-100 text-red-800' : 
                                   ($schedule->priority === 'high' ? 'bg-orange-100 text-orange-800' : 
                                   ($schedule->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($schedule->priority) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">{{ $schedule->vehicle->full_name }}</p>
                        @if($schedule->description)
                            <p class="text-sm text-gray-600 mb-2">{{ $schedule->description }}</p>
                        @endif
                        <div class="flex gap-4 text-sm text-gray-600">
                            @if($schedule->due_date)
                                <span>📅 Due: {{ $schedule->due_date->format('M d, Y') }}</span>
                            @endif
                            @if($schedule->due_mileage)
                                <span>🚗 {{ number_format($schedule->due_mileage) }} mi</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        @if($schedule->status !== 'completed')
                            <form action="{{ route('maintenance.complete', $schedule) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-800">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('maintenance.destroy', $schedule) }}" method="POST" 
                              onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $schedules->links() }}
        </div>
    @endif
</div>
@endsection