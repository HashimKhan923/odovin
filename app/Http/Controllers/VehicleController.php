<?php
// app/Http/Controllers/VehicleController.php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Services\VinDecoderService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;


class VehicleController extends Controller
{
    protected $vinDecoder;

    public function __construct(VinDecoderService $vinDecoder)
    {
        $this->vinDecoder = $vinDecoder;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        /**
         * 1. Get vehicles (basic data)
         */
        $vehicles = $user->vehicles()
            ->with(['documents']) // keep what you already had
            ->latest()
            ->get();

        if ($vehicles->isEmpty()) {
            return view('vehicles.index', compact('vehicles'));
        }

        /**
         * 2. Collect vehicle IDs once
         */
        $vehicleIds = $vehicles->pluck('id');

        /**
         * 3. Get overdue maintenance counts (ONE QUERY)
         */
        $overdueMaintenanceCounts = \App\Models\MaintenanceSchedule::whereIn('vehicle_id', $vehicleIds)
            ->where('status', 'overdue')
            ->selectRaw('vehicle_id, COUNT(*) as count')
            ->groupBy('vehicle_id')
            ->pluck('count', 'vehicle_id');

        /**
         * 4. Get open recall counts (ONE QUERY)
         */
        $openRecallCounts = \App\Models\VehicleRecall::whereIn('vehicle_id', $vehicleIds)
            ->where('is_open', true)
            ->selectRaw('vehicle_id, COUNT(*) as count')
            ->groupBy('vehicle_id')
            ->pluck('count', 'vehicle_id');

        /**
         * 5. Attach health info to each vehicle
         */
        $vehicles->transform(function ($vehicle) use ($overdueMaintenanceCounts, $openRecallCounts) {

            $overdue = $overdueMaintenanceCounts[$vehicle->id] ?? 0;
            $recalls = $openRecallCounts[$vehicle->id] ?? 0;

            // Simple, understandable health logic
            if ($recalls > 0 || $overdue >= 2) {
                $healthStatus = 'needs_attention';
            } elseif ($overdue === 1) {
                $healthStatus = 'good';
            } else {
                $healthStatus = 'excellent';
            }

            // Attach computed properties (NOT saved in DB)
            $vehicle->health_status = $healthStatus;
            $vehicle->overdue_maintenance_count = $overdue;
            $vehicle->open_recall_count = $recalls;

            return $vehicle;
        });

        /**
         * 6. Return view
         */
        return view('vehicles.index', compact('vehicles'));
    }


    public function create()
    {
        return view('vehicles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vin' => 'required|string|size:17|unique:vehicles,vin',
            'license_plate' => 'nullable|string|max:20',
            'color' => 'nullable|string|max:50',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'current_mileage' => 'nullable|integer|min:0',
            'is_primary' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Decode VIN and get vehicle details
            $vinData = $this->vinDecoder->decode($validated['vin']);

            // If this is set as primary, remove primary from other vehicles
            if ($request->is_primary) {
                $request->user()->vehicles()->update(['is_primary' => false]);
            }

            // Create vehicle with decoded data
            $vehicle = $request->user()->vehicles()->create(array_merge(
                $validated,
                [
                    'make' => $vinData['make'] ?? null,
                    'model' => $vinData['model'] ?? null,
                    'year' => $vinData['year'] ?? null,
                    'trim' => $vinData['trim'] ?? null,
                    'engine' => $vinData['engine'] ?? null,
                    'transmission' => $vinData['transmission'] ?? null,
                    'fuel_type' => $vinData['fuel_type'] ?? null,
                    'specifications' => $vinData['specifications'] ?? [],
                ]
            ));

            // Create default maintenance schedules
            $this->createDefaultMaintenanceSchedules($vehicle);

            DB::commit();

            return redirect()
                ->route('vehicles.show', $vehicle)
                ->with('success', 'Vehicle added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to add vehicle: ' . $e->getMessage());
        }
    }

    public function show(Vehicle $vehicle)
    {
        // $this->authorize('view', $vehicle);

        $vehicle->load([
            'documents',
            'maintenanceSchedules' => function ($query) {
                $query->latest()->limit(10);
            },
            'serviceRecords' => function ($query) {
                $query->latest()->limit(10);
            },
            'expenses' => function ($query) {
                $query->latest()->limit(10);
            },
            'aiInsight',
        ]);

        // Get statistics
        $stats = [
            'total_expenses' => $vehicle->getTotalExpenses(),
            'this_month_expenses' => $vehicle->getTotalExpenses(
                now()->startOfMonth(),
                now()->endOfMonth()
            ),
            'total_services' => $vehicle->serviceRecords()->count(),
            'pending_maintenance' => $vehicle->maintenanceSchedules()
                ->whereIn('status', ['pending', 'overdue'])
                ->count(),
        ];

        return view('vehicles.show', compact('vehicle', 'stats'));
    }

    public function edit(Vehicle $vehicle)
    {
        // $this->authorize('update', $vehicle);
        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        // $this->authorize('update', $vehicle);

        $validated = $request->validate([
            'license_plate' => 'nullable|string|max:20',
            'color' => 'nullable|string|max:50',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'current_mileage' => 'nullable|integer|min:' . $vehicle->current_mileage,
            'status' => 'in:active,sold,inactive',
        ]);

        $vehicle->update($validated);

        return redirect()
            ->route('vehicles.show', $vehicle)
            ->with('success', 'Vehicle updated successfully!');
    }

    public function destroy(Vehicle $vehicle)
    {
        // $this->authorize('delete', $vehicle);

        $vehicle->delete();

        return redirect()
            ->route('vehicles.index')
            ->with('success', 'Vehicle deleted successfully!');
    }

    public function setPrimary(Request $request, Vehicle $vehicle)
    {
        // $this->authorize('update', $vehicle);

        DB::transaction(function () use ($request, $vehicle) {
            // Remove primary from all user vehicles
            $request->user()->vehicles()->update(['is_primary' => false]);
            
            // Set this vehicle as primary
            $vehicle->update(['is_primary' => true]);
        });

        return back()->with('success', 'Primary vehicle updated!');
    }

    public function updateMileage(Request $request, Vehicle $vehicle)
    {
        // $this->authorize('update', $vehicle);

        $validated = $request->validate([
            'mileage' => 'required|integer|min:' . $vehicle->current_mileage,
        ]);

        $vehicle->updateMileage($validated['mileage']);

        return back()->with('success', 'Mileage updated successfully!');
    }

    public function decodeVin(Request $request)
    {
        
        // $request->validate([
        //     'vin' => 'required|string|size:17',
        // ]);

        try {
            $data = $this->vinDecoder->decode($request->vin);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to decode VIN: ' . $e->getMessage()
            ], 400);
        }
    }

    protected function createDefaultMaintenanceSchedules(Vehicle $vehicle)
    {
        $schedules = [
            [
                'service_type' => 'Oil Change',
                'description' => 'Regular oil and filter change',
                'due_mileage' => $vehicle->current_mileage + 5000,
                'priority' => 'high',
                'is_recurring' => true,
                'recurrence_mileage' => 5000,
            ],
            [
                'service_type' => 'Tire Rotation',
                'description' => 'Rotate tires for even wear',
                'due_mileage' => $vehicle->current_mileage + 7500,
                'priority' => 'medium',
                'is_recurring' => true,
                'recurrence_mileage' => 7500,
            ],
            [
                'service_type' => 'Air Filter Replacement',
                'description' => 'Replace engine air filter',
                'due_mileage' => $vehicle->current_mileage + 15000,
                'priority' => 'medium',
                'is_recurring' => true,
                'recurrence_mileage' => 15000,
            ],
            [
                'service_type' => 'Brake Inspection',
                'description' => 'Inspect brake pads and rotors',
                'due_mileage' => $vehicle->current_mileage + 20000,
                'priority' => 'high',
                'is_recurring' => true,
                'recurrence_mileage' => 20000,
            ],
        ];

        foreach ($schedules as $schedule) {
            $vehicle->maintenanceSchedules()->create($schedule);
        }
    }
}