@extends('layouts.app')

@section('title', 'My Vehicles')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">My Vehicles</h1>
            <p class="mt-1 text-sm text-gray-600">Manage all your vehicles in one place</p>
        </div>
        <a href="{{ route('vehicles.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Vehicle
        </a>
    </div>

    @if($vehicles->isEmpty())
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-lg p-12 text-center">
            <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No vehicles yet</h3>
            <p class="text-gray-600 mb-6">Add your first vehicle to start managing its maintenance and expenses</p>
            <a href="{{ route('vehicles.create') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                Add Your First Vehicle
            </a>
        </div>
    @else
        <!-- Vehicles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($vehicles as $vehicle)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition {{ $vehicle->is_primary ? 'ring-2 ring-blue-500' : '' }}">
                <div class="p-6">
                    <!-- Vehicle Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="text-lg font-bold text-gray-900">{{ $vehicle->full_name }}</h3>
                                @if($vehicle->is_primary)
                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Primary</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600">{{ $vehicle->vin }}</p>
                        </div>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak
                                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                <a href="{{ route('vehicles.show', $vehicle) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">View Details</a>
                                <a href="{{ route('vehicles.edit', $vehicle) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit</a>
                                @unless($vehicle->is_primary)
                                <form action="{{ route('vehicles.set-primary', $vehicle) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Set as Primary
                                    </button>
                                </form>
                                @endunless
                                <div class="border-t border-gray-200"></div>
                                <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this vehicle?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Details -->
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Mileage</span>
                            <span class="font-medium text-gray-900">{{ number_format($vehicle->current_mileage) }} mi</span>
                        </div>
                        @if($vehicle->license_plate)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">License Plate</span>
                            <span class="font-medium text-gray-900">{{ $vehicle->license_plate }}</span>
                        </div>
                        @endif
                        @if($vehicle->color)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Color</span>
                            <span class="font-medium text-gray-900">{{ $vehicle->color }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-2 pt-4 border-t border-gray-200">
                        <div class="text-center">
                            <p class="text-lg font-bold text-gray-900">{{ $vehicle->maintenance_schedules_count }}</p>
                            <p class="text-xs text-gray-600">Maintenance</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold text-gray-900">{{ $vehicle->service_records_count }}</p>
                            <p class="text-xs text-gray-600">Services</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold text-gray-900">{{ $vehicle->expenses_count }}</p>
                            <p class="text-xs text-gray-600">Expenses</p>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="mt-4">
                        <a href="{{ route('vehicles.show', $vehicle) }}" 
                           class="block w-full text-center bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection