@extends('layouts.app')

@section('title', 'Recall Details')

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">
            Recalls — {{ $vehicle->full_name }}
        </h1>

        <form method="POST" action="{{ route('recalls.sync', $vehicle) }}">
            @csrf
            <button class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Refresh Recalls
            </button>
        </form>
    </div>

    @forelse($recalls as $recall)
        <div class="bg-white rounded-lg shadow p-5 mb-4 border-l-4
            {{ $recall->is_open ? 'border-red-500' : 'border-gray-300' }}">
            
            <div class="flex justify-between">
                <h3 class="font-semibold text-gray-900">
                    {{ $recall->component }}
                </h3>
                <span class="text-xs px-2 py-1 rounded-full
                    {{ $recall->is_open ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600' }}">
                    {{ $recall->is_open ? 'Open' : 'Closed' }}
                </span>
            </div>

            <p class="text-sm text-gray-700 mt-2">{{ $recall->summary }}</p>

            @if($recall->remedy)
                <p class="text-sm text-gray-600 mt-2">
                    <strong>Remedy:</strong> {{ $recall->remedy }}
                </p>
            @endif

            <p class="text-xs text-gray-500 mt-2">
                Reported: {{ optional($recall->report_received_date)->format('M d, Y') }}
            </p>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow p-6 text-center text-gray-500">
            No recalls found for this vehicle.
        </div>
    @endforelse
</div>
@endsection
