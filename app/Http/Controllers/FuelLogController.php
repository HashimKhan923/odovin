<?php

namespace App\Http\Controllers;

use App\Models\FuelLog;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class FuelLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = FuelLog::whereHas('vehicle', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with('vehicle')
            ->when($request->vehicle_id, function ($query, $vehicleId) {
                return $query->where('vehicle_id', $vehicleId);
            })
            ->latest('fill_date')
            ->paginate(20);

        $vehicles = $request->user()->vehicles;

        // Calculate statistics
        $stats = [
            'total_gallons' => $logs->sum('gallons'),
            'total_cost' => $logs->sum('total_cost'),
            'average_mpg' => $logs->where('mpg', '>', 0)->avg('mpg'),
            'average_price_per_gallon' => $logs->avg('price_per_gallon'),
        ];

        return view('fuel.index', compact('logs', 'vehicles', 'stats'));
    }

    public function create()
    {
        $vehicles = auth()->user()->vehicles()->active()->get();
        return view('fuel.create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'fill_date' => 'required|date',
            'odometer' => 'required|integer|min:0',
            'gallons' => 'required|numeric|min:0',
            'price_per_gallon' => 'required|numeric|min:0',
            'total_cost' => 'required|numeric|min:0',
            'is_full_tank' => 'boolean',
            'gas_station' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $vehicle = Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        // Calculate MPG if possible
        $lastLog = $vehicle->fuelLogs()->latest('fill_date')->first();
        if ($lastLog && $validated['is_full_tank']) {
            $miles = $validated['odometer'] - $lastLog->odometer;
            $validated['mpg'] = $miles / $validated['gallons'];
        }

        $log = FuelLog::create($validated);

        // Update vehicle mileage
        $vehicle->updateMileage($validated['odometer']);

        // Create expense
        $vehicle->expenses()->create([
            'category' => 'fuel',
            'description' => 'Fuel fill-up at ' . ($validated['gas_station'] ?? 'Gas Station'),
            'amount' => $validated['total_cost'],
            'expense_date' => $validated['fill_date'],
            'odometer_reading' => $validated['odometer'],
        ]);

        return redirect()
            ->route('fuel.index')
            ->with('success', 'Fuel log added successfully!');
    }
}