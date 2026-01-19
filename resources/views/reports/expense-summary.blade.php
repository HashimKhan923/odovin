@extends('layouts.app')

@section('title', 'Expense Summary')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('reports.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center mb-2">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Reports
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Expense Summary</h1>
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
            
            <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" 
                   class="rounded-lg border-gray-300">
            
            <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" 
                   class="rounded-lg border-gray-300">
            
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Filter
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600 mb-1">Total Expenses</p>
            <p class="text-3xl font-bold text-gray-900">${{ number_format($totalExpenses, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600 mb-1">Total Transactions</p>
            <p class="text-3xl font-bold text-gray-900">{{ $expenses->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600 mb-1">Average Expense</p>
            <p class="text-3xl font-bold text-gray-900">${{ $expenses->count() > 0 ? number_format($totalExpenses / $expenses->count(), 2) : '0.00' }}</p>
        </div>
    </div>

    <!-- By Category -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Expenses by Category</h2>
        <div class="space-y-3">
            @foreach($byCategory as $category => $total)
            <div class="flex justify-between items-center">
                <span class="text-gray-700">{{ ucfirst($category) }}</span>
                <span class="font-bold text-gray-900">${{ number_format($total, 2) }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Monthly Breakdown -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Monthly Breakdown</h2>
        <div class="space-y-3">
            @foreach($byMonth as $month => $total)
            <div class="flex justify-between items-center">
                <span class="text-gray-700">{{ date('F Y', strtotime($month . '-01')) }}</span>
                <span class="font-bold text-gray-900">${{ number_format($total, 2) }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection