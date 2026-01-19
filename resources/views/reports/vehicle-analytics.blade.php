@extends('layouts.app')

@section('title', 'Vehicle Analytics')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('reports.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center mb-2">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Reports
        </a>
        <h1 class="text-3xl font-bold text-gray-900">{{ $vehicle->full_name }}</h1>
        <p class="mt-1 text-sm text-gray-600">Complete analytics and statistics</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600 mb-1">Total Expenses</p>
            <p class="text-2xl font-bold text-gray-900">${{ number_format($totalExpenses, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600 mb-1">Total Services</p>
            <p class="text-2xl font-bold text-gray-900">{{ $serviceCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600 mb-1">Maintenance Items</p>
            <p class="text-2xl font-bold text-gray-900">{{ $maintenanceCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600 mb-1">Current Mileage</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($vehicle->current_mileage) }}</p>
        </div>
    </div>

    <!-- Expenses by Category -->
    <div class="grid lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Expenses by Category</h2>
            <div class="space-y-3">
                @foreach($expensesByCategory as $expense)
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">{{ ucfirst($expense->category) }}</span>
                    <span class="font-bold text-gray-900">${{ number_format($expense->total, 2) }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Monthly Expenses (Last 12 Months)</h2>
            <div class="space-y-3">
                @foreach($monthlyExpenses as $expense)
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">{{ date('M Y', strtotime($expense->month . '-01')) }}</span>
                    <span class="font-bold text-gray-900">${{ number_format($expense->total, 2) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection