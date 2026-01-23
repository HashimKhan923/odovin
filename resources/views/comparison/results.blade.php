@extends('layouts.app')

@section('title', 'Comparison Results')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">
        Vehicle Comparison Results
    </h1>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 rounded-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Metric</th>
                    @foreach ($comparison as $item)
                        <th class="p-3 text-left">
                            {{ $item['vehicle']->year }}
                            {{ $item['vehicle']->make }}
                            {{ $item['vehicle']->model }}
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody class="divide-y">
                <tr>
                    <td class="p-3 font-medium">Total Expenses</td>
                    @foreach ($comparison as $item)
                        <td class="p-3">
                            ${{ number_format($item['total_expenses'], 2) }}
                        </td>
                    @endforeach
                </tr>

                <tr>
                    <td class="p-3 font-medium">Monthly Average</td>
                    @foreach ($comparison as $item)
                        <td class="p-3">
                            ${{ number_format($item['monthly_average'], 2) }}
                        </td>
                    @endforeach
                </tr>

                <tr>
                    <td class="p-3 font-medium">Cost Per Mile</td>
                    @foreach ($comparison as $item)
                        <td class="p-3">
                            ${{ number_format($item['cost_per_mile'], 2) }}
                        </td>
                    @endforeach
                </tr>

                <tr>
                    <td class="p-3 font-medium">Fuel Cost</td>
                    @foreach ($comparison as $item)
                        <td class="p-3">
                            ${{ number_format($item['fuel_cost'], 2) }}
                        </td>
                    @endforeach
                </tr>

                <tr>
                    <td class="p-3 font-medium">Maintenance Cost</td>
                    @foreach ($comparison as $item)
                        <td class="p-3">
                            ${{ number_format($item['maintenance_cost'], 2) }}
                        </td>
                    @endforeach
                </tr>

                <tr>
                    <td class="p-3 font-medium">Service Count</td>
                    @foreach ($comparison as $item)
                        <td class="p-3">
                            {{ $item['service_count'] }}
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        <a
            href="{{ route('comparison.index') }}"
            class="text-blue-600 hover:underline"
        >
            ← Compare other vehicles
        </a>
    </div>
</div>
@endsection
