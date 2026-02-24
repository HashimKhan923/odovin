@extends('admin.layouts.app')
@section('title', 'Provider Details')
@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6"><a href="{{ route('admin.providers.index') }}" class="text-blue-600">← Back</a></div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow"><h3 class="text-gray-500 text-sm">Total Bookings</h3><p class="text-3xl font-bold">{{ $stats['total_bookings'] }}</p></div>
        <div class="bg-white p-6 rounded-lg shadow"><h3 class="text-gray-500 text-sm">Completed</h3><p class="text-3xl font-bold">{{ $stats['completed_bookings'] }}</p></div>
        <div class="bg-white p-6 rounded-lg shadow"><h3 class="text-gray-500 text-sm">Average Rating</h3><p class="text-3xl font-bold">{{ number_format($stats['average_rating'], 1) }}</p></div>
        <div class="bg-white p-6 rounded-lg shadow"><h3 class="text-gray-500 text-sm">Total Revenue</h3><p class="text-3xl font-bold">${{ number_format($stats['total_revenue'], 2) }}</p></div>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-6">{{ $provider->name }}</h2>
        <dl class="grid grid-cols-2 gap-6">
            <div><dt class="text-sm text-gray-500">Email</dt><dd class="text-sm">{{ $provider->email }}</dd></div>
            <div><dt class="text-sm text-gray-500">Phone</dt><dd class="text-sm">{{ $provider->phone }}</dd></div>
            <div><dt class="text-sm text-gray-500">Address</dt><dd class="text-sm">{{ $provider->address }}</dd></div>
            <div><dt class="text-sm text-gray-500">Status</dt><dd class="text-sm"><span class="px-2 py-1 rounded-full {{ $provider->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ ucfirst($provider->status) }}</span></dd></div>
        </dl>
    </div>
</div>
@endsection
