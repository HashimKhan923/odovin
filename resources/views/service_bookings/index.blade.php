@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">My Service Bookings</h1>
        <a href="{{ route('bookings.create') }}" class="btn btn-primary">Book a Service</a>
    </div>

    <table class="min-w-full bg-white border border-gray-200">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Vehicle</th>
                <th class="py-2 px-4 border-b">Service Date</th>
                <th class="py-2 px-4 border-b">Service Type</th>
                <th class="py-2 px-4 border-b">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings as $booking)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $booking->vehicle->name }}</td>
                    <td class="py-2 px-4 border-b">{{ $booking->service_date->format('M d, Y') }}</td>
                    <td class="py-2 px-4 border-b">{{ $booking->service_type }}</td>
                    <td class="py-2 px-4 border-b">
                        <a href="{{ route('bookings.edit', $booking) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form action="{{ route('bookings.destroy', $booking) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
