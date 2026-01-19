@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Reports & Analytics</h1>
        <p class="mt-1 text-sm text-gray-600">Analyze your vehicle expenses and maintenance</p>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Expense Summary -->
        <a href="{{ route('reports.expense-summary') }}" 
           class="block bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Expense Summary</h3>
                    <p class="text-sm text-gray-600">View detailed expense breakdown</p>
                </div>
            </div>
            <p class="text-blue-600 text-sm font-medium">View Report →</p>
        </a>

        <!-- Maintenance History -->
        <a href="{{ route('reports.maintenance-history') }}" 
           class="block bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Maintenance History</h3>
                    <p class="text-sm text-gray-600">Complete service records</p>
                </div>
            </div>
            <p class="text-green-600 text-sm font-medium">View Report →</p>
        </a>

        <!-- Vehicle Analytics -->
        @foreach($vehicles as $vehicle)
        <a href="{{ route('reports.vehicle-analytics', $vehicle) }}" 
           class="block bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">{{ $vehicle->full_name }}</h3>
                    <p class="text-sm text-gray-600">Vehicle analytics</p>
                </div>
            </div>
            <p class="text-purple-600 text-sm font-medium">View Analytics →</p>
        </a>
        @endforeach
    </div>
</div>
@endsection