<?php

namespace App\Http\Controllers;

use App\Models\TripLog;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class TripLogController extends Controller
{
    public function index(Request $request)
    {
        $trips = TripLog::whereHas('vehicle', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with('vehicle')
            ->when($request->vehicle_id, function ($query, $vehicleId) {
                return $query->where('vehicle_id', $vehicleId);
            })
            ->when($request->purpose, function ($query, $purpose) {
                return $query->where('purpose', $purpose);
            })
            ->latest('trip_date')
            ->paginate(20);

        $vehicles = $request->user()->vehicles;

        $stats = [
            'total_miles' => $trips->sum('distance'),
            'total_trips' => $trips->count(),
            'business_miles' => $trips->where('purpose', 'business')->sum('distance'),
            'personal_miles' => $trips->where('purpose', 'personal')->sum('distance'),
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
            'vehicle_id' => 'required|exists:vehicles,id',
            'trip_date' => 'required|date',
            'start_odometer' => 'required|integer|min:0',
            'end_odometer' => 'required|integer|min:0|gt:start_odometer',
            'purpose' => 'required|in:business,personal,commute',
            'destination' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $validated['distance'] = $validated['end_odometer'] - $validated['start_odometer'];

        TripLog::create($validated);

        return redirect()
            ->route('trips.index')
            ->with('success', 'Trip logged successfully!');
    }

    public function export(Request $request)
    {
        $trips = TripLog::whereHas('vehicle', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with('vehicle')
            ->when($request->vehicle_id, function ($query, $vehicleId) {
                return $query->where('vehicle_id', $vehicleId);
            })
            ->when($request->start_date, function ($query, $date) {
                return $query->where('trip_date', '>=', $date);
            })
            ->when($request->end_date, function ($query, $date) {
                return $query->where('trip_date', '<=', $date);
            })
            ->orderBy('trip_date')
            ->get();

        return view('trips.export', compact('trips'));
    }
}