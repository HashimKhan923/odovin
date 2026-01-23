<?php
namespace App\Http\Controllers;

use App\Models\MaintenanceSchedule;
use App\Models\ServiceRecord;
use App\Models\Vehicle;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use App\Models\Expense;


class MaintenanceController extends Controller
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
            ->paginate(15);

        $vehicles = $request->user()->vehicles;

        $vehicleIds = $vehicles->pluck('id');

        $maintenanceExpensesQuery = Expense::whereIn('vehicle_id', $vehicleIds)
            ->whereIn('category', ['maintenance', 'service', 'repair']);

        /**
         * Analytics
         */
        $analytics = [
            'month_cost' => (clone $maintenanceExpensesQuery)
                ->whereMonth('expense_date', now()->month)
                ->whereYear('expense_date', now()->year)
                ->sum('amount'),

            'overdue_count' => \App\Models\MaintenanceSchedule::whereIn('vehicle_id', $vehicleIds)
                ->where('status', 'overdue')
                ->count(),

            'upcoming_30_days' => \App\Models\MaintenanceSchedule::whereIn('vehicle_id', $vehicleIds)
                ->where('status', 'pending')
                ->whereBetween('due_date', [now(), now()->addDays(30)])
                ->count(),

            'top_service' => (clone $maintenanceExpensesQuery)
                ->whereMonth('expense_date', now()->month)
                ->whereYear('expense_date', now()->year)
                ->selectRaw('description, SUM(amount) as total')
                ->groupBy('description')
                ->orderByDesc('total')
                ->first(),
        ];

        return view('maintenance.index', compact('schedules', 'vehicles','analytics'));
    }

    public function create()
    {
        $vehicles = auth()->user()->vehicles()->active()->get();
        
        return view('maintenance.create', compact('vehicles'));
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
        Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $schedule = MaintenanceSchedule::create($validated);

        return redirect()
            ->route('maintenance.index')
            ->with('success', 'Maintenance schedule created successfully!');
    }

    public function edit(Request $request, MaintenanceSchedule $maintenance)
    {
        // $this->authorize('update', $maintenance->vehicle);

        $vehicles = $request->user()->vehicles;

        return view('maintenance.edit', compact('maintenance', 'vehicles'));
    }

    public function update(Request $request, MaintenanceSchedule $maintenance)
    {
        // $this->authorize('update', $maintenance->vehicle);

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
        Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $maintenance->update($validated);

        return redirect()
            ->route('maintenance.index')
            ->with('success', 'Maintenance schedule updated successfully!');
    }

    public function destroy(Request $request, MaintenanceSchedule $maintenance)
    {
        // $this->authorize('delete', $maintenance->vehicle);

        $maintenance->delete();

        return redirect()
            ->route('maintenance.index')
            ->with('success', 'Maintenance schedule deleted successfully!');
    }

    public function markComplete(Request $request, MaintenanceSchedule $maintenance)
    {
        // $this->authorize('update', $maintenance->vehicle);

        $maintenance->markCompleted();

        return back()->with('success', 'Maintenance marked as completed!');
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
            ->paginate(15);

        $vehicles = $request->user()->vehicles;

        return view('maintenance.records.index', compact('records', 'vehicles'));
    }

    public function createRecord()
    {
        $vehicles = auth()->user()->vehicles()->active()->get();
        $providers = ServiceProvider::active()->verified()->get();
        
        return view('maintenance.records.create', compact('vehicles', 'providers'));
    }

    public function destroyRecord(ServiceRecord $record)
    {
        
        // $this->authorize('delete', $record);

        $record->delete();

        return redirect()
            ->route('maintenance.records.index')
            ->with('success', 'Service record deleted successfully!');
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
            'invoice_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'parts_replaced' => 'nullable|array',
            'notes' => 'nullable|string',
            'next_service_mileage' => 'nullable|integer|min:0',
            'next_service_date' => 'nullable|date|after:service_date',
        ]);

        // Verify vehicle belongs to user
        $vehicle = Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($request->hasFile('invoice_file')) {
            $validated['invoice_file'] = $request->file('invoice_file')->store('invoices/' . $vehicle->id, 'public');
        }

        $record = ServiceRecord::create($validated);

        // Update vehicle mileage if higher
        $vehicle->updateMileage($validated['mileage_at_service']);

        // Create expense record
        $vehicle->expenses()->create([
            'service_record_id' => $record->id,
            'category' => 'maintenance',
            'description' => $validated['service_type'] . ' - ' . $validated['description'],
            'amount' => $validated['cost'],
            'expense_date' => $validated['service_date'],
            'odometer_reading' => $validated['mileage_at_service'],
        ]);

        return redirect()
            ->route('maintenance.records.index')
            ->with('success', 'Service record added successfully!');
    }
}