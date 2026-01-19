<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceSchedule;
use App\Models\ServiceRecord;
use App\Models\Vehicle;
use App\Models\ServiceProvider;
use App\Http\Resources\MaintenanceScheduleResource;
use App\Http\Resources\ServiceRecordResource;
use Illuminate\Http\Request;

class MaintenanceApiController extends Controller
{
    public function index(Request $request)
    {
        $schedules = MaintenanceSchedule::whereHas('vehicle', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with('vehicle')
            ->when($request->vehicle_id, function ($query, $vehicleId) {
                return $query->where('vehicle_id', $vehicleId);
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('due_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => MaintenanceScheduleResource::collection($schedules),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'service_type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_mileage' => 'nullable|integer|min:0',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high,critical',
            'is_recurring' => 'boolean',
            'recurrence_mileage' => 'nullable|required_if:is_recurring,true|integer|min:0',
            'recurrence_months' => 'nullable|required_if:is_recurring,true|integer|min:1',
        ]);

        // Verify vehicle belongs to user
        $vehicle = Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $schedule = MaintenanceSchedule::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Maintenance schedule created successfully',
            'data' => new MaintenanceScheduleResource($schedule->load('vehicle')),
        ], 201);
    }

    public function show(MaintenanceSchedule $maintenance)
    {
        $this->authorize('update', $maintenance->vehicle);

        return response()->json([
            'success' => true,
            'data' => new MaintenanceScheduleResource($maintenance->load('vehicle')),
        ]);
    }

    public function update(Request $request, MaintenanceSchedule $maintenance)
    {
        $this->authorize('update', $maintenance->vehicle);

        $validated = $request->validate([
            'service_type' => 'string|max:255',
            'description' => 'nullable|string',
            'due_mileage' => 'nullable|integer|min:0',
            'due_date' => 'nullable|date',
            'priority' => 'in:low,medium,high,critical',
            'status' => 'in:pending,scheduled,completed,overdue',
        ]);

        $maintenance->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Maintenance schedule updated successfully',
            'data' => new MaintenanceScheduleResource($maintenance),
        ]);
    }

    public function destroy(MaintenanceSchedule $maintenance)
    {
        $this->authorize('delete', $maintenance->vehicle);

        $maintenance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Maintenance schedule deleted successfully',
        ]);
    }

    public function complete(MaintenanceSchedule $maintenance)
    {
        $this->authorize('update', $maintenance->vehicle);

        $maintenance->markCompleted();

        return response()->json([
            'success' => true,
            'message' => 'Maintenance marked as completed',
            'data' => new MaintenanceScheduleResource($maintenance),
        ]);
    }

    public function upcoming(Request $request)
    {
        $schedules = MaintenanceSchedule::whereHas('vehicle', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with('vehicle')
            ->whereIn('status', ['pending', 'scheduled'])
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => MaintenanceScheduleResource::collection($schedules),
        ]);
    }

    public function overdue(Request $request)
    {
        $schedules = MaintenanceSchedule::whereHas('vehicle', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with('vehicle')
            ->where('status', 'overdue')
            ->orderBy('due_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => MaintenanceScheduleResource::collection($schedules),
        ]);
    }

    // Service Records
    public function records(Request $request)
    {
        $records = ServiceRecord::whereHas('vehicle', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with(['vehicle', 'serviceProvider'])
            ->when($request->vehicle_id, function ($query, $vehicleId) {
                return $query->where('vehicle_id', $vehicleId);
            })
            ->latest('service_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ServiceRecordResource::collection($records),
        ]);
    }

    public function storeRecord(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'service_provider_id' => 'nullable|exists:service_providers,id',
            'service_type' => 'required|string|max:255',
            'description' => 'required|string',
            'service_date' => 'required|date',
            'mileage_at_service' => 'required|integer|min:0',
            'cost' => 'required|numeric|min:0',
            'invoice_number' => 'nullable|string|max:255',
            'parts_replaced' => 'nullable|array',
            'notes' => 'nullable|string',
            'next_service_mileage' => 'nullable|integer|min:0',
            'next_service_date' => 'nullable|date|after:service_date',
        ]);

        $vehicle = Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $record = ServiceRecord::create($validated);

        $vehicle->updateMileage($validated['mileage_at_service']);

        $vehicle->expenses()->create([
            'service_record_id' => $record->id,
            'category' => 'maintenance',
            'description' => $validated['service_type'] . ' - ' . $validated['description'],
            'amount' => $validated['cost'],
            'expense_date' => $validated['service_date'],
            'odometer_reading' => $validated['mileage_at_service'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Service record created successfully',
            'data' => new ServiceRecordResource($record->load(['vehicle', 'serviceProvider'])),
        ], 201);
    }

    public function showRecord(ServiceRecord $record)
    {
        $this->authorize('view', $record->vehicle);

        return response()->json([
            'success' => true,
            'data' => new ServiceRecordResource($record->load(['vehicle', 'serviceProvider'])),
        ]);
    }

    public function updateRecord(Request $request, ServiceRecord $record)
    {
        $this->authorize('update', $record->vehicle);

        $validated = $request->validate([
            'service_type' => 'string|max:255',
            'description' => 'string',
            'service_date' => 'date',
            'mileage_at_service' => 'integer|min:0',
            'cost' => 'numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $record->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Service record updated successfully',
            'data' => new ServiceRecordResource($record),
        ]);
    }

    public function deleteRecord(ServiceRecord $record)
    {
        $this->authorize('delete', $record->vehicle);

        $record->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service record deleted successfully',
        ]);
    }

    public function maintenanceHistory(Request $request)
    {
        $records = ServiceRecord::whereHas('vehicle', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with(['vehicle', 'serviceProvider'])
            ->when($request->vehicle_id, function ($query, $vehicleId) {
                return $query->where('vehicle_id', $vehicleId);
            })
            ->latest('service_date')
            ->get();

        $totalCost = $records->sum('cost');
        $totalServices = $records->count();

        return response()->json([
            'success' => true,
            'data' => [
                'records' => ServiceRecordResource::collection($records),
                'statistics' => [
                    'total_cost' => $totalCost,
                    'total_services' => $totalServices,
                    'average_cost' => $totalServices > 0 ? $totalCost / $totalServices : 0,
                ],
            ],
        ]);
    }
}