@extends('admin.layouts.app')
@section('title', 'Booking Details')
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6"><a href="{{ route('admin.bookings.index') }}" class="text-blue-600">← Back to Bookings</a></div>
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-bold">Booking #{{ $booking->id }}</h2>
                <span class="px-3 py-1 text-sm rounded-full mt-2 inline-block
                    @if($booking->status == 'completed') bg-green-100 text-green-800
                    @elseif($booking->status == 'confirmed') bg-blue-100 text-blue-800
                    @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                    @else bg-yellow-100 text-yellow-800 @endif">
                    {{ ucfirst($booking->status) }}
                </span>
            </div>
        </div>
        <dl class="grid grid-cols-2 gap-6">
            <div><dt class="text-sm text-gray-500 mb-1">Customer</dt><dd class="font-semibold">{{ $booking->user->name }}</dd></div>
            <div><dt class="text-sm text-gray-500 mb-1">Service Type</dt><dd class="font-semibold">{{ ucfirst(str_replace('_', ' ', $booking->service_type)) }}</dd></div>
            <div><dt class="text-sm text-gray-500 mb-1">Service Provider</dt><dd class="font-semibold">{{ $booking->serviceProvider->name }}</dd></div>
            <div><dt class="text-sm text-gray-500 mb-1">Vehicle</dt><dd class="font-semibold">{{ $booking->vehicle->year }} {{ $booking->vehicle->make }} {{ $booking->vehicle->model }}</dd></div>
            <div><dt class="text-sm text-gray-500 mb-1">Scheduled Date</dt><dd class="font-semibold">{{ $booking->scheduled_date->format('F d, Y h:i A') }}</dd></div>
            <div><dt class="text-sm text-gray-500 mb-1">Price</dt><dd class="font-semibold">${{ number_format($booking->price, 2) }}</dd></div>
        </dl>
        @if($booking->notes)
        <div class="mt-6 pt-6 border-t"><dt class="text-sm text-gray-500 mb-2">Notes</dt><dd class="text-sm">{{ $booking->notes }}</dd></div>
        @endif
        <div class="mt-6 pt-6 border-t">
            <form method="POST" action="{{ route('admin.bookings.update-status', $booking) }}" class="flex gap-4">
                @csrf @method('PUT')
                <select name="status" class="border rounded-lg px-4 py-2">
                    <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg">Update Status</button>
            </form>
        </div>
    </div>
</div>
@endsection
