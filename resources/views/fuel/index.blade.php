@extends('layouts.app')

@section('title', 'Fuel Logs')

@section('content')
<div class="max-w-6xl mx-auto px-4">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Fuel Logs</h1>
        <div class="flex items-center justify-start gap-x-2">

            <a href="{{ route('fuel.create') }}"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                Add Fuel Log
            </a>
            <a href="{{ route('fuel.import.form') }}"
            class="px-4 py-2 bg-gray-600 text-white rounded-lg">
                Import CSV
            </a>
            <a href="{{ route('fuel.export.csv', $selectedVehicleId) }}"
               class="px-4 py-2 bg-gray-600 text-white rounded-lg">
                Export CSV
            </a>
            <a href="{{ route('fuel.export.pdf', ['vehicle_id' => request('vehicle_id')]) }}"
                class="px-4 py-2 bg-red-600 text-white rounded-lg">
                Export PDF
                
            </a>
    </div>
     </div>


    <form method="GET" class="mb-6">
    <div class="flex items-center gap-3">
        <select name="vehicle_id"
                class="rounded-lg border-gray-300"
                onchange="this.form.submit()">
            <option value="">All Vehicles</option>
            @foreach($vehicles as $vehicle)
                <option value="{{ $vehicle->id }}"
                    {{ $selectedVehicleId == $vehicle->id ? 'selected' : '' }}>
                    {{ $vehicle->full_name }}
                </option>
            @endforeach
        </select>
    </div>
</form>


    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">Total Gallons</p>
            <p class="text-xl font-bold">{{ number_format($stats['total_gallons'], 2) }}</p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">Total Cost</p>
            <p class="text-xl font-bold">${{ number_format($stats['total_cost'], 2) }}</p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">Avg MPG</p>
            <p class="text-xl font-bold">{{ number_format($stats['average_mpg'], 1) }}</p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">Avg Price/Gal</p>
            <p class="text-xl font-bold">${{ number_format($stats['average_price_per_gallon'], 2) }}</p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">Fuel Cost / Mile</p>
                
            @if($fuelCostPerMile)
                <p class="text-xl font-bold">
                    ${{ number_format($fuelCostPerMile, 3) }}
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    Based on {{ number_format($totalMiles) }} miles
                </p>
            @else
                <p class="text-sm text-gray-400">Not enough data</p>
            @endif
        </div>
    </div>

    @if($mpgChartData->count() > 1)
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-semibold mb-4">MPG Trend</h2>

            <canvas id="mpgChart" height="120"></canvas>
    </div>
    @endif

    {{-- Logs --}}
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">Date</th>
                    <th class="px-4 py-2 text-left">Vehicle</th>
                    <th class="px-4 py-2 text-right">Gallons</th>
                    <th class="px-4 py-2 text-right">Cost</th>
                    <th class="px-4 py-2 text-right">MPG</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $log->fill_date->format('M d, Y') }}</td>
                        <td class="px-4 py-2">{{ $log->vehicle->full_name }}</td>
                        <td class="px-4 py-2 text-right">{{ $log->gallons }}</td>
                        <td class="px-4 py-2 text-right">${{ $log->total_cost }}</td>
                        <td class="px-4 py-2 text-right">

                     @if($log->mpg)
                        <span class="font-medium">
                            {{ number_format($log->mpg, 1) }}
                        </span>

                        @if($log->mpg_anomaly === 'impossible')
                            <span class="ml-1 text-xs text-red-600 font-semibold">
                                ⚠ Impossible
                            </span>
                            <span title="This MPG is far from your vehicle’s average. Check odometer or gallons."
                                class="cursor-help">
                                ⚠
                            </span>
                        @elseif($log->mpg_anomaly === 'unrealistic')
                            <span class="ml-1 text-xs text-orange-600 font-semibold">
                                ⚠ Unrealistic
                            </span>
                            <span title="This MPG is far from your vehicle’s average. Check odometer or gallons."
                                class="cursor-help">
                                ⚠
                            </span>
                        @elseif($log->mpg_anomaly === 'suspicious')
                            <span class="ml-1 text-xs text-yellow-600 font-semibold">
                                ⚠ Suspicious
                            </span>
                            <span title="This MPG is far from your vehicle’s average. Check odometer or gallons."
                                class="cursor-help">
                                ⚠
                            </span>
                        @endif
                    @else
                        —
                    @endif
                            
                        </td>
                        <td class="px-4 py-2 text-right space-x-2">
                            <a href="{{ route('fuel.edit', $log) }}"
                            class="text-blue-600 hover:underline">
                                Edit
                            </a>

                            <form method="POST"
                                action="{{ route('fuel.destroy', $log) }}"
                                class="inline"
                                onsubmit="return confirm('Delete this fuel log?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>

</div>



@endsection

@push('scripts')
@if($mpgChartData->count() > 1)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const mpgData = @json($mpgChartData);
        const canvas = document.getElementById('mpgChart');

        if (!canvas) return;

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: mpgData.map(d => d.date),
                datasets: [{
                    label: 'MPG',
                    data: mpgData.map(d => d.mpg),

                    borderColor: '#10b981',      // emerald
                    backgroundColor: 'rgba(16,185,129,0.15)',

                    fill: true,
                    tension: 0.35,

                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#10b981',
                    pointBorderWidth: 0,
                }]
            },
            options: {
                responsive: true,

                animation: {
                    duration: 900,
                    easing: 'easeOutCubic'
                },

                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#111827',
                        padding: 10,
                        callbacks: {
                            label: ctx => `${ctx.parsed.y} MPG`
                        }
                    }
                },

                scales: {
                    x: {
                        ticks: {
                            color: '#6b7280'
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        ticks: {
                            color: '#6b7280'
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        },
                        title: {
                            display: true,
                            text: 'Miles per Gallon'
                        }
                    }
                }
            }
        });

    });
</script>
@endif
@endpush