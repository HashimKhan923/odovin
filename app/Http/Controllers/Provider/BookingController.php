<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\ServiceProvider;
use App\Models\ServiceBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    protected function provider()
    {
        return Auth::user()->serviceProvider;
    }

    public function index(Request $request)
    {
        $provider = $this->provider();
        $query    = $provider->bookings()->with(['vehicle', 'user']);

        $query->when($request->status, fn($q, $s) => $q->where('status', $s));
        $query->when($request->search, function ($q, $search) {
            $q->where(function ($query) use ($search) {
                $query->where('service_type', 'like', "%$search%")
                      ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%$search%"))
                      ->orWhere('booking_number', 'like', "%$search%");
            });
        });

        if ($request->date_from) $query->where('scheduled_date', '>=', $request->date_from);
        if ($request->date_to)   $query->where('scheduled_date', '<=', $request->date_to . ' 23:59:59');

        $sort = $request->sort ?? 'scheduled_date_desc';
        switch ($sort) {
            case 'scheduled_date_asc': $query->orderBy('scheduled_date', 'asc'); break;
            case 'created_desc':       $query->orderByDesc('created_at'); break;
            default:                   $query->orderByDesc('scheduled_date');
        }

        $bookings = $query->paginate(15)->withQueryString();

        $stats = [
            'pending'     => $provider->bookings()->where('status', 'pending')->count(),
            'confirmed'   => $provider->bookings()->where('status', 'confirmed')->count(),
            'in_progress' => $provider->bookings()->where('status', 'in_progress')->count(),
            'completed'   => $provider->bookings()->where('status', 'completed')->count(),
        ];

        return view('provider.bookings.index', compact('provider', 'bookings', 'stats'));
    }

    public function show(ServiceBooking $booking)
    {
        $provider = $this->provider();
        abort_unless($booking->service_provider_id === $provider->id, 403);
        $booking->load(['vehicle', 'user']);
        return view('provider.bookings.show', compact('provider', 'booking'));
    }

    public function updateStatus(Request $request, ServiceBooking $booking)
    {
        $provider = $this->provider();
        abort_unless($booking->service_provider_id === $provider->id, 403);

        $validated = $request->validate([
            'status'         => 'required|in:pending,confirmed,in_progress,completed,cancelled',
            'provider_notes' => 'nullable|string|max:1000',
            'final_cost'     => 'nullable|numeric|min:0',
        ]);

        $updateData = ['status' => $validated['status']];
        if (!empty($validated['provider_notes'])) $updateData['provider_notes'] = $validated['provider_notes'];
        if (!empty($validated['final_cost']))     $updateData['final_cost']     = $validated['final_cost'];

        $booking->update($updateData);

        if ($validated['status'] === 'completed') {
            $booking->serviceProvider->updateRating();
        }

        return back()->with('success', 'Booking updated to: ' . ucfirst(str_replace('_', ' ', $validated['status'])));
    }

    public function calendar(Request $request)
    {
        $provider = $this->provider();
        $month    = $request->month ? Carbon::parse($request->month) : Carbon::now();

        $bookings = $provider->bookings()
            ->with(['vehicle', 'user'])
            ->whereYear('scheduled_date',  $month->year)
            ->whereMonth('scheduled_date', $month->month)
            ->whereIn('status', ['pending', 'confirmed', 'in_progress', 'completed'])
            ->orderBy('scheduled_date')
            ->get()
            ->groupBy(fn($b) => $b->scheduled_date->format('Y-m-d'));

        return view('provider.bookings.calendar', compact('provider', 'bookings', 'month'));
    }
}