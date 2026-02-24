@extends('admin.layouts.app')
@section('title', 'Booking Statistics')
@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Booking Statistics</h1>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow"><h3 class="text-gray-500 text-sm">Total Bookings</h3><p class="text-3xl font-bold">{{ $stats['total_bookings'] }}</p></div>
        <div class="bg-white p-6 rounded-lg shadow"><h3 class="text-gray-500 text-sm">Completed</h3><p class="text-3xl font-bold">{{ $stats['completed_bookings'] }}</p></div>
        <div class="bg-white p-6 rounded-lg shadow"><h3 class="text-gray-500 text-sm">Pending</h3><p class="text-3xl font-bold">{{ $stats['pending_bookings'] }}</p></div>
        <div class="bg-white p-6 rounded-lg shadow"><h3 class="text-gray-500 text-sm">Total Revenue</h3><p class="text-3xl font-bold">${{ number_format($stats['total_revenue'], 2) }}</p></div>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Bookings by Service Type</h3>
        @foreach($stats['bookings_by_service_type'] as $type => $count)
        <div class="flex justify-between py-2 border-b">
            <span>{{ ucfirst(str_replace('_', ' ', $type)) }}</span>
            <span class="font-semibold">{{ $count }}</span>
        </div>
        @endforeach
    </div>
</div>
@endsection
