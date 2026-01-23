@extends('layouts.app')

@section('title', 'Import Fuel Logs')

@section('content')
<div class="max-w-xl mx-auto px-4">
    <h1 class="text-3xl font-bold mb-6">Import Fuel Logs (CSV)</h1>

    <div class="bg-white p-6 rounded-lg shadow">
        <form method="POST"
              action="{{ route('fuel.import') }}"
              enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">CSV File *</label>
                <input type="file"
                       name="file"
                       accept=".csv"
                       required
                       class="block w-full">
            </div>

            <p class="text-xs text-gray-500 mb-4">
                Required columns: vehicle_id, fill_date, odometer, gallons, price_per_gallon, total_cost
            </p>

            <div class="flex justify-end gap-3">
                <a href="{{ route('fuel.index') }}"
                   class="px-4 py-2 border rounded-lg">
                    Cancel
                </a>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                    Import
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
