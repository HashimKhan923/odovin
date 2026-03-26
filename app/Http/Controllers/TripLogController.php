<?php

namespace App\Http\Controllers;

use App\Models\TripLog;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class TripLogController extends Controller
{
    public function index(Request $request)
    {
        $query = TripLog::whereHas('vehicle', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            })
            ->with('vehicle')
            ->when($request->vehicle_id, fn($q, $v) => $q->where('vehicle_id', $v))
            ->when($request->purpose,    fn($q, $p) => $q->where('purpose', $p))
            ->when($request->start_date, fn($q, $d) => $q->where('trip_date', '>=', $d))
            ->when($request->end_date,   fn($q, $d) => $q->where('trip_date', '<=', $d))
            ->latest('trip_date');

        $trips    = $query->paginate(15)->withQueryString();
        $vehicles = $request->user()->vehicles()->active()->get();

        // All-time stats (not paginated)
        $allTrips = TripLog::whereHas('vehicle', fn($q) => $q->where('user_id', $request->user()->id));

        $stats = [
            'total_miles'    => $allTrips->sum('distance'),
            'total_trips'    => $allTrips->count(),
            'business_miles' => (clone $allTrips)->where('purpose', 'business')->sum('distance'),
            'personal_miles' => (clone $allTrips)->where('purpose', 'personal')->sum('distance'),
            'commute_miles'  => (clone $allTrips)->where('purpose', 'commute')->sum('distance'),
            'this_month'     => (clone $allTrips)->whereMonth('trip_date', now()->month)
                                                  ->whereYear('trip_date', now()->year)
                                                  ->sum('distance'),
        ];

        return view('trips.index', compact('trips', 'vehicles', 'stats'));
    }

    public function create()
    {
        $vehicles = auth()->user()->vehicles()->active()->get();
        return view('trips.create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id'      => 'required|exists:vehicles,id',
            'trip_date'       => 'required|date|before_or_equal:today',
            'start_location'  => 'nullable|string|max:255',
            'start_odometer'  => 'required|integer|min:0',
            'end_odometer'    => 'required|integer|min:0|gt:start_odometer',
            'purpose'         => 'required|in:business,personal,commute',
            'destination'     => 'required|string|max:255',
            'notes'           => 'nullable|string|max:1000',
        ]);

        $vehicle = Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $validated['distance'] = $validated['end_odometer'] - $validated['start_odometer'];

        TripLog::create($validated);

        // Update vehicle mileage if the end odometer is higher than current
        $vehicle->updateMileage($validated['end_odometer']);

        return redirect()->route('trips.index')
            ->with('success', 'Trip logged! Vehicle mileage updated to ' . number_format($validated['end_odometer']) . ' mi.');
    }

    public function edit(TripLog $tripLog)
    {
        // Ensure ownership
        abort_unless($tripLog->vehicle->user_id === auth()->id(), 403);

        $vehicles = auth()->user()->vehicles()->active()->get();
        return view('trips.edit', compact('tripLog', 'vehicles'));
    }

    public function update(Request $request, TripLog $tripLog)
    {
        abort_unless($tripLog->vehicle->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'vehicle_id'     => 'required|exists:vehicles,id',
            'trip_date'      => 'required|date|before_or_equal:today',
            'start_location' => 'nullable|string|max:255',
            'start_odometer' => 'required|integer|min:0',
            'end_odometer'   => 'required|integer|min:0|gt:start_odometer',
            'purpose'        => 'required|in:business,personal,commute',
            'destination'    => 'required|string|max:255',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $vehicle = Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $validated['distance'] = $validated['end_odometer'] - $validated['start_odometer'];

        $tripLog->update($validated);

        // Update vehicle mileage if the end odometer is higher than current
        $vehicle->updateMileage($validated['end_odometer']);

        return redirect()->route('trips.index')
            ->with('success', 'Trip updated! Vehicle mileage updated to ' . number_format($validated['end_odometer']) . ' mi.');
    }

    public function destroy(TripLog $tripLog)
    {
        abort_unless($tripLog->vehicle->user_id === auth()->id(), 403);
        $tripLog->delete();

        return back()->with('success', 'Trip deleted.');
    }

    public function export(Request $request)
    {
        $trips = TripLog::whereHas('vehicle', fn($q) => $q->where('user_id', $request->user()->id))
            ->with('vehicle')
            ->when($request->vehicle_id,  fn($q, $v) => $q->where('vehicle_id', $v))
            ->when($request->purpose,     fn($q, $p) => $q->where('purpose', $p))
            ->when($request->start_date,  fn($q, $d) => $q->where('trip_date', '>=', $d))
            ->when($request->end_date,    fn($q, $d) => $q->where('trip_date', '<=', $d))
            ->orderBy('trip_date')
            ->get();

        $vehicles = $request->user()->vehicles()->active()->get();

        $summary = [
            'total_miles'    => $trips->sum('distance'),
            'total_trips'    => $trips->count(),
            'business_miles' => $trips->where('purpose', 'business')->sum('distance'),
            'personal_miles' => $trips->where('purpose', 'personal')->sum('distance'),
            'commute_miles'  => $trips->where('purpose', 'commute')->sum('distance'),
        ];

        return view('trips.export', compact('trips', 'vehicles', 'summary'));
    }
}