<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VehicleComparisonController extends Controller
{
    public function index()
    {
        $vehicles = auth()
            ->user()
            ->vehicles()
            ->active()
            ->select('id', 'make', 'model', 'year', 'current_mileage')
            ->get();

        return view('comparison.index', compact('vehicles'));
    }

    public function compare(Request $request)
    {
        $validated = $request->validate([
            'vehicle_ids'   => 'required|array|min:2|max:4',
            'vehicle_ids.*' => 'exists:vehicles,id',
        ]);

        $vehicles = Vehicle::whereIn('id', $validated['vehicle_ids'])
            ->where('user_id', auth()->id())
            ->with(['expenses', 'serviceRecords'])
            ->get();

        abort_if($vehicles->count() < 2, 403, 'At least two vehicles are required for comparison.');

        $comparison = $vehicles->mapWithKeys(function ($vehicle) {
            return [
                $vehicle->id => [
                    'vehicle'          => $vehicle,
                    'total_expenses'   => $vehicle->totalExpenses(),
                    'monthly_average'  => round(
                        $vehicle->totalExpenses() / $vehicle->vehicleAgeInMonths(),
                        2
                    ),
                    'cost_per_mile'    => $vehicle->costPerMile(),
                    'service_count'    => $vehicle->serviceRecords->count(),
                    'fuel_cost'        => $vehicle->expenseByCategory('fuel'),
                    'maintenance_cost' => $vehicle->expenseByCategory('maintenance'),
                ]
            ];
        });

        return view('comparison.results', compact('comparison'));
    }
}