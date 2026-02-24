<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\FuelLog;
use App\Models\ServiceRecord;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::with('user');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('make', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('vin', 'like', "%{$search}%")
                  ->orWhere('license_plate', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $vehicles = $query->paginate(15)->withQueryString();
        $users = User::where('user_type', 'user')->get();

        return view('admin.vehicles.index', compact('vehicles', 'users'));
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['user', 'fuelLogs', 'serviceRecords', 'expenses', 'documents']);
        
        $stats = [
            'total_fuel_logs' => $vehicle->fuelLogs()->count(),
            'total_fuel_cost' => $vehicle->fuelLogs()->sum('total_cost'),
            'total_service_records' => $vehicle->serviceRecords()->count(),
            'total_expenses' => $vehicle->expenses()->sum('amount'),
            'average_fuel_economy' => $vehicle->fuelLogs()->avg('gallons')
        ];

        return view('admin.vehicles.show', compact('vehicle', 'stats'));
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle deleted successfully.');
    }

    public function statistics()
    {
        $stats = [
            'total_vehicles' => Vehicle::count(),
            'by_type' => Vehicle::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type'),
            'by_fuel_type' => Vehicle::selectRaw('fuel_type, COUNT(*) as count')
                ->groupBy('fuel_type')
                ->pluck('count', 'fuel_type'),
            'total_mileage' => Vehicle::sum('mileage'),
            'average_year' => Vehicle::avg('year'),
        ];

        return view('admin.vehicles.statistics', compact('stats'));
    }
}