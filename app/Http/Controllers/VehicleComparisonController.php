<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VehicleComparisonController extends Controller
{
    public function index()
    {
        $vehicles = auth()->user()->vehicles()->active()->get();
        return view('comparison.index', compact('vehicles'));
    }

    public function compare(Request $request)
    {
        $validated = $request->validate([
            'vehicle_ids' => 'required|array|min:2|max:4',
            'vehicle_ids.*' => 'exists:vehicles,id',
        ]);

        $vehicles = Vehicle::whereIn('id', $validated['vehicle_ids'])
            ->where('user_id', auth()->id())
            ->with(['expenses', 'serviceRecords', 'maintenanceSchedules'])
            ->get();

        if ($vehicles->count() < 2) {
            return back()->with('error', 'Please select at least 2 vehicles to compare.');
        }

        $comparison = [];
        foreach ($vehicles as $vehicle) {
            $comparison[$vehicle->id] = [
                'vehicle' => $vehicle,
                'total_expenses' => $vehicle->getTotalExpenses(),
                'monthly_avg' => $vehicle->getTotalExpenses() / max(1, $vehicle->created_at->diffInMonths(now())),
                'cost_per_mile' => $vehicle->current_mileage > 0 ? $vehicle->getTotalExpenses() / $vehicle->current_mileage : 0,
                'service_count' => $vehicle->serviceRecords()->count(),
                'fuel_cost' => $vehicle->expenses()->where('category', 'fuel')->sum('amount'),
                'maintenance_cost' => $vehicle->expenses()->where('category', 'maintenance')->sum('amount'),
            ];
        }

        return view('comparison.results', compact('comparison'));
    }
}