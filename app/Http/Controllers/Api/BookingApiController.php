<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceBooking;
use App\Models\Vehicle;
use App\Http\Resources\ServiceBookingResource;
use Illuminate\Http\Request;

class BookingApiController extends Controller
{
    public function index(Request $request)
    {
        $bookings = ServiceBooking::where('user_id', $request->user()->id)
            ->with(['vehicle', 'serviceProvider'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->vehicle_id, function ($query, $vehicleId) {
                return $query->where('vehicle_id', $vehicleId);
            })
            ->latest('scheduled_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ServiceBookingResource::collection($bookings),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'service_provider_id' => 'required|exists:service_providers,id',
            'service_type' => 'required|string|max:255',
            'description' => 'required|string',
            'scheduled_date' => 'required|date|after:now',
            'estimated_cost' => 'nullable|numeric|min:0',
            'customer_notes' => 'nullable|string',
        ]);

        $vehicle = Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $booking = ServiceBooking::create([
            'user_id' => $request->user()->id,
            'vehicle_id' => $validated['vehicle_id'],
            'service_provider_id' => $validated['service_provider_id'],
            'service_type' => $validated['service_type'],
            'description' => $validated['description'],
            'scheduled_date' => $validated['scheduled_date'],
            'estimated_cost' => $validated['estimated_cost'] ?? null,
            'customer_notes' => $validated['customer_notes'] ?? null,
            'status' => 'pending',
        ]);

        $request->user()->alerts()->create([
            'vehicle_id' => $vehicle->id,
            'type' => 'booking',
            'title' => 'Booking Created',
            'message' => "Your {$validated['service_type']} booking has been created",
            'priority' => 'info',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'data' => new ServiceBookingResource($booking->load(['vehicle', 'serviceProvider'])),
        ], 201);
    }

    public function show(ServiceBooking $booking)
    {
        $this->authorize('view', $booking);

        return response()->json([
            'success' => true,
            'data' => new ServiceBookingResource($booking->load(['vehicle', 'serviceProvider'])),
        ]);
    }

    public function update(Request $request, ServiceBooking $booking)
    {
        $this->authorize('update', $booking);

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update booking in current status',
            ], 400);
        }

        $validated = $request->validate([
            'scheduled_date' => 'date|after:now',
            'estimated_cost' => 'nullable|numeric|min:0',
            'customer_notes' => 'nullable|string',
        ]);

        $booking->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Booking updated successfully',
            'data' => new ServiceBookingResource($booking),
        ]);
    }

    public function destroy(ServiceBooking $booking)
    {
        $this->authorize('delete', $booking);

        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Booking deleted successfully',
        ]);
    }

    public function cancel(ServiceBooking $booking)
    {
        $this->authorize('update', $booking);

        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Booking is already cancelled',
            ], 400);
        }

        if ($booking->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel completed booking',
            ], 400);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully',
            'data' => new ServiceBookingResource($booking),
        ]);
    }

    public function confirm(ServiceBooking $booking)
    {
        $this->authorize('update', $booking);

        if ($booking->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending bookings can be confirmed',
            ], 400);
        }

        $booking->update(['status' => 'confirmed']);

        return response()->json([
            'success' => true,
            'message' => 'Booking confirmed successfully',
            'data' => new ServiceBookingResource($booking),
        ]);
    }

    public function complete(ServiceBooking $booking)
    {
        $this->authorize('update', $booking);

        if (!in_array($booking->status, ['confirmed', 'in_progress'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot complete booking in current status',
            ], 400);
        }

        $booking->update(['status' => 'completed']);

        return response()->json([
            'success' => true,
            'message' => 'Booking completed successfully',
            'data' => new ServiceBookingResource($booking),
        ]);
    }

    public function rate(Request $request, ServiceBooking $booking)
    {
        $this->authorize('update', $booking);

        if ($booking->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Can only rate completed bookings',
            ], 400);
        }

        if ($booking->rating) {
            return response()->json([
                'success' => false,
                'message' => 'Booking has already been rated',
            ], 400);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $booking->update($validated);
        $booking->serviceProvider->updateRating();

        return response()->json([
            'success' => true,
            'message' => 'Rating submitted successfully',
            'data' => new ServiceBookingResource($booking),
        ]);
    }
}