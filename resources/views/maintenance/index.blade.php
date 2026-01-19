


@extends('layouts.app')

@section('title', 'Maintenance')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Maintenance</h1>
            <p class="mt-1 text-sm text-gray-600">
                Track upcoming and completed maintenance tasks
            </p>
        </div>

        <a href="{{ route('maintenance.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            Add Maintenance
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">

            <!-- Vehicle Filter -->
            <div>
                <label class="block text-sm text-gray-600 mb-1">Vehicle</label>
                <select name="vehicle_id" class="rounded-lg border-gray-300">
                    <option value="">All Vehicles</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}"
                            {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                            {{ $vehicle->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm text-gray-600 mb-1">Status</label>
                <select name="status" class="rounded-lg border-gray-300">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                        Pending
                    </option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>
                        Completed
                    </option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex gap-2">
                <button type="submit"
                        class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    Filter
                </button>

                <a href="{{ route('maintenance.index') }}"
                   class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Empty State -->
    @if($schedules->isEmpty())
        <div class="bg-white rounded-lg shadow-lg p-12 text-center">
            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                No maintenance records found
            </h3>
            <p class="text-gray-600 mb-6">
                Add your first maintenance task to start tracking
            </p>

            <a href="{{ route('maintenance.create') }}"
               class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                Add Maintenance
            </a>
        </div>
    @else

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">

            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Service
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Vehicle
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Due Date
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Due Mileage
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Status
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                        Actions
                    </th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($schedules as $schedule)
                    <tr class="hover:bg-gray-50">

                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            {{ $schedule->service_type }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $schedule->vehicle->full_name }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $schedule->due_date
                                ? $schedule->due_date->format('M d, Y')
                                : 'N/A' }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $schedule->due_mileage
                                ? number_format($schedule->due_mileage).' mi'
                                : 'N/A' }}
                        </td>

                        <td class="px-6 py-4">
                            @if($schedule->status === 'completed')
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    bg-green-100 text-green-800">
                                    Completed
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @endif
                        </td>
<td class="px-6 py-4 text-right text-sm">
    <div class="inline-flex items-center gap-2">

        @if($schedule->status !== 'completed')
            <form action="{{ route('maintenance.complete', $schedule) }}"
                  method="POST">
                @csrf
                <button type="submit"
                        onclick="return confirm('Mark this maintenance as completed?')"
                        class="inline-flex items-center px-3 py-1.5 rounded-full
                               text-xs font-medium
                               bg-green-50 text-green-700
                               hover:bg-green-100 transition">
                    ✓ Complete
                </button>
            </form>
        @endif

        <a href="{{ route('maintenance.edit', $schedule) }}"
           class="inline-flex items-center px-3 py-1.5 rounded-full
                  text-xs font-medium
                  bg-blue-50 text-blue-700
                  hover:bg-blue-100 transition">
            ✎ Edit
        </a>

        @if(!$schedule->is_system_generated)
            <form action="{{ route('maintenance.destroy', $schedule) }}"
                  method="POST"
                  onsubmit="return confirm('Delete this maintenance task?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center px-3 py-1.5 rounded-full
                               text-xs font-medium
                               bg-red-50 text-red-700
                               hover:bg-red-100 transition">
                    🗑 Delete
                </button>
            </form>
        @endif

    </div>
</td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $schedules->links() }}
    </div>

    @endif
</div>
@endsection
