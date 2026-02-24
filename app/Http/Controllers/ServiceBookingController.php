<?php

namespace App\Http\Controllers;

use App\Models\ServiceBooking;
use App\Models\Vehicle;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use App\Services\ProviderNotificationService;
use App\Models\Alert;

class ServiceBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceBooking::where('user_id', $request->user()->id)
            ->with(['vehicle', 'serviceProvider']);

        $query->when($request->status,     fn($q, $s)   => $q->where('status', $s));
        $query->when($request->vehicle_id, fn($q, $vid) => $q->where('vehicle_id', $vid));

        if ($request->date_from) $query->where('scheduled_date', '>=', $request->date_from);
        if ($request->date_to)   $query->where('scheduled_date', '<=', $request->date_to . ' 23:59:59');

        $bookings = $query->latest('scheduled_date')->paginate(15)->withQueryString();
        $vehicles = $request->user()->vehicles;

        $stats = [
            'total'       => ServiceBooking::where('user_id', $request->user()->id)->count(),
            'upcoming'    => ServiceBooking::where('user_id', $request->user()->id)
                                ->whereIn('status', ['pending', 'confirmed'])
                                ->where('scheduled_date', '>=', now())->count(),
            'completed'   => ServiceBooking::where('user_id', $request->user()->id)
                                ->where('status', 'completed')->count(),
            'total_spent' => ServiceBooking::where('user_id', $request->user()->id)
                                ->where('status', 'completed')->sum('final_cost'),
        ];

        return view('bookings.index', compact('bookings', 'vehicles', 'stats'));
    }

    public function create(Request $request)
    {
        $vehicles  = $request->user()->vehicles()->active()->get();

        $vehicleId = $request->vehicle_id 
            ?? $vehicles->firstWhere('is_primary', true)?->id;

        $providers = ServiceProvider::active()
            ->verified()
            ->orderByDesc('rating')
            ->get();

        $preselectedProvider = $request->service_provider_id
            ? ServiceProvider::find($request->service_provider_id)
            : null;


        $serviceTypes = [
            'Oil Change', 'Tire Rotation', 'Brake Service', 'Battery Replacement',
            'Air Filter Replacement', 'Transmission Service', 'Engine Diagnostics',
            'Wheel Alignment', 'Detailing', 'Full Inspection', 'AC Service',
            'Coolant Flush', 'Spark Plug Replacement', 'EV Battery Check',
            'Windshield Repair', 'Other',
        ];

        return view('bookings.create', compact(
            'vehicles',
            'providers',
            'serviceTypes',
            'vehicleId',
            'preselectedProvider'
        ));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id'          => 'required|exists:vehicles,id',
            'service_provider_id' => 'required|exists:service_providers,id',
            'service_type'        => 'required|string|max:255',
            'description'         => 'required|string',
            'scheduled_date'      => 'required|date|after:now',
            'scheduled_time'      => 'required',
            'estimated_cost'      => 'nullable|numeric|min:0',
            'customer_notes'      => 'nullable|string',
        ]);

        $vehicle = Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $serviceProvider = ServiceProvider::where('id', $validated['service_provider_id'])
            ->where('is_active', true)
            ->firstOrFail();    

        $scheduledDateTime = $validated['scheduled_date'] . ' ' . $validated['scheduled_time'];

        $booking = ServiceBooking::create([
            'user_id'             => $request->user()->id,
            'vehicle_id'          => $validated['vehicle_id'],
            'service_provider_id' => $validated['service_provider_id'],
            'service_type'        => $validated['service_type'],
            'description'         => $validated['description'],
            'scheduled_date'      => $scheduledDateTime,
            'estimated_cost'      => $validated['estimated_cost'] ?? null,
            'customer_notes'      => $validated['customer_notes'] ?? null,
            'status'              => 'pending',
        ]);

        $request->user()->alerts()->create([
            'user_id'    => $request->user()->id,
            'vehicle_id' => $vehicle->id,
            'type'       => 'booking',
            'title'      => 'Booking Created',
            'message'    => "Your {$validated['service_type']} booking has been created for "
                           . date('M d, Y', strtotime($scheduledDateTime)),
            'priority'   => 'info',
        ]);

        ProviderNotificationService::newBooking($booking->load(['user', 'serviceProvider']));




        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking created! The service provider will confirm your appointment.');
    }

    public function show(ServiceBooking $booking)
    {
        abort_unless($booking->user_id === auth()->id(), 403);
        $booking->load(['vehicle', 'serviceProvider']);
        return view('bookings.show', compact('booking'));
    }

    public function edit(ServiceBooking $booking)
    {
        abort_unless($booking->user_id === auth()->id(), 403);

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Cannot edit booking in current status.');
        }

        $vehicles     = auth()->user()->vehicles()->active()->get();
        $providers    = ServiceProvider::active()->verified()->get();
        $serviceTypes = [
            'Oil Change', 'Tire Rotation', 'Brake Service', 'Battery Replacement',
            'Air Filter Replacement', 'Transmission Service', 'Engine Diagnostics',
            'Wheel Alignment', 'Detailing', 'Full Inspection', 'AC Service',
            'Coolant Flush', 'Spark Plug Replacement', 'EV Battery Check',
            'Windshield Repair', 'Other',
        ];
        $timeSlots = [];
        for ($h = 7; $h <= 18; $h++) {
            $timeSlots[] = sprintf('%02d:00', $h);
            $timeSlots[] = sprintf('%02d:30', $h);
        }

        return view('bookings.edit', compact('booking', 'vehicles', 'providers', 'serviceTypes', 'timeSlots'));
    }

    public function update(Request $request, ServiceBooking $booking)
    {
        abort_unless($booking->user_id === auth()->id(), 403);

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Cannot update booking in current status.');
        }

        $validated = $request->validate([
            'scheduled_date' => 'required|date|after:now',
            'scheduled_time' => 'required',
            'estimated_cost' => 'nullable|numeric|min:0',
            'customer_notes' => 'nullable|string',
        ]);

        $booking->update([
            'scheduled_date' => $validated['scheduled_date'] . ' ' . $validated['scheduled_time'],
            'estimated_cost' => $validated['estimated_cost'] ?? $booking->estimated_cost,
            'customer_notes' => $validated['customer_notes'] ?? $booking->customer_notes,
        ]);

        return redirect()->route('bookings.show', $booking)->with('success', 'Booking updated successfully!');
    }

    public function destroy(ServiceBooking $booking)
    {
        abort_unless($booking->user_id === auth()->id(), 403);
        $booking->delete();
        return redirect()->route('bookings.index')->with('success', 'Booking deleted.');
    }

    public function cancel(Request $request, ServiceBooking $booking)
    {
        abort_unless($booking->user_id === auth()->id(), 403);

        if ($booking->status === 'cancelled') return back()->with('error', 'Booking already cancelled.');
        if ($booking->status === 'completed')  return back()->with('error', 'Cannot cancel completed booking.');

        $validated = $request->validate(['cancel_reason' => 'nullable|string|max:500']);
        $booking->update(['status' => 'cancelled', 'provider_notes' => $validated['cancel_reason'] ?? null]);

        $request->user()->alerts()->create([
            'user_id'    => $request->user()->id,
            'vehicle_id' => $booking->vehicle_id,
            'type'       => 'booking',
            'title'      => 'Booking Cancelled',
            'message'    => "Your {$booking->service_type} booking has been cancelled.",
            'priority'   => 'info',
        ]);

                ProviderNotificationService::bookingCancelled($booking->load(['user', 'serviceProvider']));


        return back()->with('success', 'Booking cancelled successfully!');
    }

    public function rate(Request $request, ServiceBooking $booking)
    {
        abort_unless($booking->user_id === auth()->id(), 403);

        if ($booking->status !== 'completed') return back()->with('error', 'Can only rate completed bookings.');
        if ($booking->rating)                  return back()->with('error', 'Booking has already been rated.');

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $booking->update($validated);
        $booking->serviceProvider->updateRating();

                ProviderNotificationService::newReview($booking->load(['user', 'serviceProvider']));


        return back()->with('success', 'Thank you for your review!');
    }
}