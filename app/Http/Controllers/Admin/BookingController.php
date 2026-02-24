<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceBooking;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceBooking::with(['user', 'vehicle', 'serviceProvider']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('serviceProvider', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by service type
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_date', '<=', $request->date_to);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'scheduled_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $bookings = $query->paginate(15)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(ServiceBooking $booking)
    {
        $booking->load(['user', 'vehicle', 'serviceProvider']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, ServiceBooking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $booking->update($validated);

        return back()->with('success', 'Booking status updated successfully.');
    }

    public function destroy(ServiceBooking $booking)
    {
        $booking->delete();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking deleted successfully.');
    }

    public function statistics()
    {
        $stats = [
            'total_bookings' => ServiceBooking::count(),
            'pending_bookings' => ServiceBooking::where('status', 'pending')->count(),
            'confirmed_bookings' => ServiceBooking::where('status', 'confirmed')->count(),
            'completed_bookings' => ServiceBooking::where('status', 'completed')->count(),
            'cancelled_bookings' => ServiceBooking::where('status', 'cancelled')->count(),
            'total_revenue' => ServiceBooking::where('status', 'completed')->sum('price'),
            'average_rating' => ServiceBooking::whereNotNull('rating')->avg('rating'),
            'bookings_by_service_type' => ServiceBooking::selectRaw('service_type, COUNT(*) as count')
                ->groupBy('service_type')
                ->pluck('count', 'service_type'),
        ];

        return view('admin.bookings.statistics', compact('stats'));
    }
}