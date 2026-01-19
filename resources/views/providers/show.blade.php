@extends('layouts.app')

@section('title', $provider->name)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('providers.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center mb-2">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Providers
        </a>
        <h1 class="text-3xl font-bold text-gray-900">{{ $provider->name }}</h1>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Provider Details Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst(str_replace('_', ' ', $provider->type)) }}
                            </span>
                            @if($provider->is_verified)
                                <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                    </svg>
                                    Verified Provider
                                </span>
                            @endif
                        </div>
                    </div>
                    @if($provider->rating > 0)
                        <div class="text-right">
                            <div class="flex items-center text-yellow-500 text-2xl font-bold mb-1">
                                <svg class="w-6 h-6 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                {{ number_format($provider->rating, 1) }}
                            </div>
                            <p class="text-sm text-gray-600">{{ $provider->total_reviews }} reviews</p>
                        </div>
                    @endif
                </div>

                <div class="space-y-4">
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Contact Information</h3>
                        <div class="space-y-2">
                            <div class="flex items-center text-gray-700">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <a href="tel:{{ $provider->phone }}" class="hover:text-blue-600">{{ $provider->phone }}</a>
                            </div>
                            @if($provider->email)
                            <div class="flex items-center text-gray-700">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <a href="mailto:{{ $provider->email }}" class="hover:text-blue-600">{{ $provider->email }}</a>
                            </div>
                            @endif
                            <div class="flex items-start text-gray-700">
                                <svg class="w-5 h-5 mr-3 mt-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>{{ $provider->address }}<br>{{ $provider->city }}, {{ $provider->state }} {{ $provider->zip_code }}</span>
                            </div>
                        </div>
                    </div>

                    @if($provider->services_offered)
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Services Offered</h3>
                        <p class="text-gray-700">{{ $provider->services_offered }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Reviews -->
            @if($provider->bookings->isNotEmpty())
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Recent Reviews</h3>
                <div class="space-y-4">
                    @foreach($provider->bookings as $booking)
                        <div class="border-b border-gray-200 pb-4 last:border-0">
                            <div class="flex items-center mb-2">
                                <div class="flex text-yellow-500 mr-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= $booking->rating ? 'text-yellow-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-600">{{ $booking->updated_at->format('M d, Y') }}</span>
                            </div>
                            @if($booking->review)
                                <p class="text-gray-700">{{ $booking->review }}</p>
                            @endif
                            <p class="text-sm text-gray-500 mt-1">Service: {{ $booking->service_type }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Booking Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Book a Service</h3>
                
                <form action="{{ route('bookings.create') }}" method="GET">
                    <input type="hidden" name="service_provider_id" value="{{ $provider->id }}">
                    
                    <div class="mb-4">
                        <label for="vehicle_select" class="block text-sm font-medium text-gray-700 mb-2">
                            Select Your Vehicle
                        </label>
                        <select id="vehicle_select" name="vehicle_id" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Choose a vehicle</option>
                            @foreach($userVehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">
                                    {{ $vehicle->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                        Continue to Booking
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 text-center">
                        Call us directly at<br>
                        <a href="tel:{{ $provider->phone }}" class="text-blue-600 hover:text-blue-800 font-semibold text-lg">
                            {{ $provider->phone }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection