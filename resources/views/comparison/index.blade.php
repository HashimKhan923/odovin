@extends('layouts.app')

@section('title', 'Compare Vehicles')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">
        Compare Vehicles
    </h1>

    @if ($vehicles->count() < 2)
        <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 p-4 rounded">
            You need at least two vehicles to use comparison.
        </div>
    @else
        <form method="POST" action="{{ route('comparison.compare') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($vehicles as $vehicle)
                    <label class="border rounded-lg p-4 cursor-pointer hover:border-blue-500">
                        <div class="flex items-start gap-3">
                            <input
                                type="checkbox"
                                name="vehicle_ids[]"
                                value="{{ $vehicle->id }}"
                                class="mt-1"
                            >

                            <div>
                                <p class="font-medium text-gray-900">
                                    {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
                                </p>

                                <p class="text-sm text-gray-500">
                                    Mileage: {{ number_format($vehicle->current_mileage) }} km
                                </p>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>

            @error('vehicle_ids')
                <p class="text-red-600 text-sm mt-3">{{ $message }}</p>
            @enderror

            <div class="mt-6">
                <button
                    type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700"
                >
                    Compare Selected Vehicles
                </button>
            </div>
        </form>
    @endif
</div>
@endsection
