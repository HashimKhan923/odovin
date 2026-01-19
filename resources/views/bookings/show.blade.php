@extends('layouts.app')

@section('title', 'Booking Details')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('bookings.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center mb-2">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Bookings
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Booking #{{ $booking->booking_number }}</h1>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $booking->service_type }}</h2>
                <span class="px-3 py-1 text-sm font-medium rounded-full
                    {{ $booking->status === 'completed' ? 'bg-green-100 text-green-800' : 
                       ($booking->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : 
                       ($booking->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' :
                       ($booking->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) }}">
                    {{ ucfirst($booking->status) }}
                </span>
            </div>
            
            <div class="flex gap-2">
                @if(in_array($booking->status, ['pending', 'confirmed']))
                    <a href="{{ route('bookings.edit', $booking) }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                        Edit
                    </a>
                    <form action="{{ route('bookings.cancel', $booking) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                        @csrf
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                            Cancel Booking
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="font-semibold text-gray-900 mb-3">Booking Information</h3>
                <dl class="space-y-2">
                    <div>
                        <dt class="text-sm text-gray-600">Vehicle</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $booking->vehicle->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-600">Scheduled Date & Time</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $booking->scheduled_date->format('l, F j, Y \a\t g:i A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-600">Service Type</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $booking->service_type }}</dd>
                    </div>
                    @if($booking->estimated_cost)
                    <div>
                        <dt class="text-sm text-gray-600">Estimated Cost</dt>
                        <dd class="text-sm font-medium text-gray-900">${{ number_format($booking->estimated_cost, 2) }}</dd>
                    </div>
                    @endif
                    @if($booking->final_cost)
                    <div>
                        <dt class="text-sm text-gray-600">Final Cost</dt>
                        <dd class="text-sm font-medium text-gray-900">${{ number_format($booking->final_cost, 2) }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <div>
                <h3 class="font-semibold text-gray-900 mb-3">Service Provider</h3>
                <dl class="space-y-2">
                    <div>
                        <dt class="text-sm text-gray-600">Name</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $booking->serviceProvider->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-600">Phone</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $booking->serviceProvider->phone }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-600">Address</dt>
                        <dd class="text-sm font-medium text-gray-900">
                            {{ $booking->serviceProvider->address }}<br>
                            {{ $booking->serviceProvider->city }}, {{ $booking->serviceProvider->state }} {{ $booking->serviceProvider->zip_code }}
                        </dd>
                    </div>
                    @if($booking->serviceProvider->rating > 0)
                    <div>
                        <dt class="text-sm text-gray-600">Rating</dt>
                        <dd class="text-sm font-medium text-gray-900">⭐ {{ number_format($booking->serviceProvider->rating, 1) }} ({{ $booking->serviceProvider->total_reviews }} reviews)</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <div class="md:col-span-2">
                <h3 class="font-semibold text-gray-900 mb-2">Description</h3>
                <p class="text-sm text-gray-700">{{ $booking->description }}</p>
            </div>

            @if($booking->customer_notes)
            <div class="md:col-span-2">
                <h3 class="font-semibold text-gray-900 mb-2">Your Notes</h3>
                <p class="text-sm text-gray-700">{{ $booking->customer_notes }}</p>
            </div>
            @endif

            @if($booking->provider_notes)
            <div class="md:col-span-2">
                <h3 class="font-semibold text-gray-900 mb-2">Provider Notes</h3>
                <p class="text-sm text-gray-700">{{ $booking->provider_notes }}</p>
            </div>
            @endif
        </div>

        @if($booking->status === 'completed' && !$booking->rating)
        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
            <h3 class="font-semibold text-gray-900 mb-3">Rate This Service</h3>
            <form action="{{ route('bookings.rate', $booking) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                    <div class="flex gap-2">
                        @for($i = 1; $i <= 5; $i++)
                            <label class="cursor-pointer">
                                <input type="radio" name="rating" value="{{ $i }}" class="sr-only peer" required>
                                <span class="text-3xl peer-checked:text-yellow-400 text-gray-300 hover:text-yellow-300 transition">⭐</span>
                            </label>
                        @endfor
                    </div>
                </div>
                <div class="mb-4">
                    <label for="review" class="block text-sm font-medium text-gray-700 mb-2">Review (Optional)</label>
                    <textarea name="review" id="review" rows="3" 
                              class="w-full rounded-lg border-gray-300"
                              placeholder="Share your experience..."></textarea>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Submit Rating
                </button>
            </form>
        </div>
        @elseif($booking->rating)
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <h3 class="font-semibold text-gray-900 mb-2">Your Rating</h3>
            <div class="flex items-center gap-2 mb-2">
                @for($i = 1; $i <= 5; $i++)
                    <span class="text-2xl {{ $i <= $booking->rating ? 'text-yellow-400' : 'text-gray-300' }}">⭐</span>
                @endfor
            </div>
            @if($booking->review)
                <p class="text-sm text-gray-700">{{ $booking->review }}</p>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection