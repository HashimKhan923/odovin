<?php
// app/Http/Controllers/ServiceBookingController.php

namespace App\Http\Controllers;

use App\Models\ServiceBooking;
use App\Models\Vehicle;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceBookingController extends Controller
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
            ->paginate(15);

        $vehicles = $request->user()->vehicles;
        
        return view('bookings.index', compact('bookings', 'vehicles'));
    }

    public function create(Request $request)
    {
        $vehicles = $request->user()->vehicles()->active()->get();
        
        $vehicleId = $request->vehicle_id ?? $vehicles->firstWhere('is_primary', true)?->id;
        
        $providers = ServiceProvider::active()
            ->verified()
            ->with(['bookings' => function ($query) {
                $query->latest()->limit(5);
            }])
            ->get();

        $serviceTypes = [
            'Oil Change',
            'Tire Rotation',
            'Brake Service',
            'Battery Replacement',
            'Air Filter Replacement',
            'Transmission Service',
            'Engine Diagnostics',
            'Wheel Alignment',
            'Detailing',
            'Inspection',
            'Other',
        ];

        return view('bookings.create', compact('vehicles', 'providers', 'serviceTypes', 'vehicleId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'service_provider_id' => 'required|exists:service_providers,id',
            'service_type' => 'required|string|max:255',
            'description' => 'required|string',
            'scheduled_date' => 'required|date|after:now',
            'scheduled_time' => 'required',
            'estimated_cost' => 'nullable|numeric|min:0',
            'customer_notes' => 'nullable|string',
        ]);

        // Verify vehicle belongs to user
        $vehicle = Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $scheduledDateTime = $validated['scheduled_date'] . ' ' . $validated['scheduled_time'];

        $booking = ServiceBooking::create([
            'user_id' => $request->user()->id,
            'vehicle_id' => $validated['vehicle_id'],
            'service_provider_id' => $validated['service_provider_id'],
            'service_type' => $validated['service_type'],
            'description' => $validated['description'],
            'scheduled_date' => $scheduledDateTime,
            'estimated_cost' => $validated['estimated_cost'] ?? null,
            'customer_notes' => $validated['customer_notes'] ?? null,
            'status' => 'pending',
        ]);

        // Create alert for the user
        $request->user()->alerts()->create([
            'vehicle_id' => $vehicle->id,
            'type' => 'booking',
            'title' => 'Booking Created',
            'message' => "Your {$validated['service_type']} booking has been created for " . date('M d, Y', strtotime($scheduledDateTime)),
            'priority' => 'info',
        ]);

        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', 'Booking created successfully! The service provider will confirm your appointment.');
    }

    public function show(ServiceBooking $booking)
    {
        // $this->authorize('view', $booking);

        $booking->load(['vehicle', 'serviceProvider']);

        return view('bookings.show', compact('booking'));
    }

    public function edit(ServiceBooking $booking)
    {
        // $this->authorize('update', $booking);

        // Can only edit pending or confirmed bookings
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Cannot edit booking in current status.');
        }

        $vehicles = auth()->user()->vehicles()->active()->get();
        $providers = ServiceProvider::active()->verified()->get();
        
        $serviceTypes = [
            'Oil Change',
            'Tire Rotation',
            'Brake Service',
            'Battery Replacement',
            'Air Filter Replacement',
            'Transmission Service',
            'Engine Diagnostics',
            'Wheel Alignment',
            'Detailing',
            'Inspection',
            'Other',
        ];

        return view('bookings.edit', compact('booking', 'vehicles', 'providers', 'serviceTypes'));
    }

    public function update(Request $request, ServiceBooking $booking)
    {
        // $this->authorize('update', $booking);

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Cannot update booking in current status.');
        }

        $validated = $request->validate([
            'scheduled_date' => 'required|date|after:now',
            'scheduled_time' => 'required',
            'estimated_cost' => 'nullable|numeric|min:0',
            'customer_notes' => 'nullable|string',
        ]);

        $scheduledDateTime = $validated['scheduled_date'] . ' ' . $validated['scheduled_time'];

        $booking->update([
            'scheduled_date' => $scheduledDateTime,
            'estimated_cost' => $validated['estimated_cost'] ?? $booking->estimated_cost,
            'customer_notes' => $validated['customer_notes'] ?? $booking->customer_notes,
        ]);

        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', 'Booking updated successfully!');
    }

    public function destroy(ServiceBooking $booking)
    {
        // $this->authorize('delete', $booking);

        $booking->delete();

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Booking deleted successfully!');
    }

    public function cancel(Request $request, ServiceBooking $booking)
    {
        $this->authorize('update', $booking);

        if ($booking->status === 'cancelled') {
            return back()->with('error', 'Booking is already cancelled.');
        }

        if ($booking->status === 'completed') {
            return back()->with('error', 'Cannot cancel completed booking.');
        }

        $booking->update(['status' => 'cancelled']);

        $request->user()->alerts()->create([
            'vehicle_id' => $booking->vehicle_id,
            'type' => 'booking',
            'title' => 'Booking Cancelled',
            'message' => "Your {$booking->service_type} booking has been cancelled.",
            'priority' => 'info',
        ]);

        return back()->with('success', 'Booking cancelled successfully!');
    }

    public function rate(Request $request, ServiceBooking $booking)
    {
        // $this->authorize('update', $booking);

        if ($booking->status !== 'completed') {
            return back()->with('error', 'Can only rate completed bookings.');
        }

        if ($booking->rating) {
            return back()->with('error', 'Booking has already been rated.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $booking->update($validated);

        // Update service provider rating
        $booking->serviceProvider->updateRating();

        return back()->with('success', 'Thank you for your rating!');
    }
}