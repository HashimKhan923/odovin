@extends('admin.layouts.app')
@section('title', 'Bookings Management')
@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Bookings Management</h1>
        <a href="{{ route('admin.bookings.statistics') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg">View Statistics</a>
    </div>
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form method="GET" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search bookings..." class="flex-1 border rounded-lg px-4 py-2">
            <select name="status" class="border rounded-lg px-4 py-2">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg">Filter</button>
        </form>
    </div>
    <div class="bg-white rounded-lg shadow">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Provider</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($bookings as $booking)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ $booking->user->name }}</td>
                    <td class="px-6 py-4">{{ ucfirst(str_replace('_', ' ', $booking->service_type)) }}</td>
                    <td class="px-6 py-4 text-sm">{{ $booking->serviceProvider->name }}</td>
                    <td class="px-6 py-4 text-sm">{{ $booking->scheduled_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full font-semibold
                            @if($booking->status == 'completed') bg-green-100 text-green-800
                            @elseif($booking->status == 'confirmed') bg-blue-100 text-blue-800
                            @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="text-blue-600 mr-3">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">No bookings found</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($bookings->hasPages())<div class="px-6 py-4">{{ $bookings->links() }}</div>@endif
    </div>
</div>
@endsection
