<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\ServiceRecord;
use App\Models\MaintenanceSchedule;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceRecordController extends Controller
{
    protected function provider()
    {
        return Auth::user()->serviceProvider;
    }

    public function index(Request $request)
    {
        $provider = $this->provider();

        $records = ServiceRecord::where('service_provider_id', $provider->id)
            ->with('vehicle')
            ->when($request->search, fn($q, $s) =>
                $q->where(fn($q2) =>
                    $q2->where('service_type', 'like', "%{$s}%")
                       ->orWhere('description', 'like', "%{$s}%")))
            ->latest('service_date')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total'         => ServiceRecord::where('service_provider_id', $provider->id)->count(),
            'this_month'    => ServiceRecord::where('service_provider_id', $provider->id)
                                ->whereMonth('service_date', now()->month)
                                ->whereYear('service_date', now()->year)->count(),
            'total_revenue' => ServiceRecord::where('service_provider_id', $provider->id)->sum('cost'),
        ];

        return view('provider.services.records.index', compact('provider', 'records', 'stats'));
    }

    public function create(Request $request)
    {
        $provider = $this->provider();

        $prefill = null;
        if ($request->job_id) {
            $prefill = \App\Models\ServiceJobPost::where('id', $request->job_id)
                ->whereHas('offers', fn($q) =>
                    $q->where('service_provider_id', $provider->id)->where('status', 'accepted'))
                ->with('vehicle')
                ->first();
        }

        return view('provider.services.records.form', compact('provider', 'prefill'));
    }

    public function store(Request $request)
    {
        $provider = $this->provider();

        $validated = $request->validate([
            'vehicle_make'         => 'required|string|max:100',
            'vehicle_model'        => 'required|string|max:100',
            'vehicle_year'         => 'required|integer|min:1900|max:' . (date('Y') + 2),
            'vehicle_plate'        => 'nullable|string|max:20',
            'service_type'         => 'required|string|max:255',
            'description'          => 'required|string|max:2000',
            'service_date'         => 'required|date',
            'mileage_at_service'   => 'nullable|integer|min:0',
            'cost'                 => 'nullable|numeric|min:0',
            'invoice_number'       => 'nullable|string|max:100',
            'parts_replaced'       => 'nullable|string|max:1000',
            'notes'                => 'nullable|string|max:2000',
            'next_service_date'    => 'nullable|date',
            'next_service_mileage' => 'nullable|integer|min:0',
            'vehicle_id'           => 'nullable|exists:vehicles,id',
        ]);

        DB::transaction(function () use ($validated, $provider) {

            // ── 1. Build description (embed vehicle info if no system vehicle) ───
            $description = $validated['description'];
            $vehicle     = null;

            if (!empty($validated['vehicle_id'])) {
                $vehicle = Vehicle::find($validated['vehicle_id']);
            } else {
                $vehLine     = "{$validated['vehicle_year']} {$validated['vehicle_make']} {$validated['vehicle_model']}";
                if (!empty($validated['vehicle_plate'])) {
                    $vehLine .= " · Plate: {$validated['vehicle_plate']}";
                }
                $description .= "\n[Vehicle: {$vehLine}]";
            }

            // ── 2. Create service record ─────────────────────────────────────────
            $record = ServiceRecord::create([
                'service_provider_id'  => $provider->id,
                'vehicle_id'           => $vehicle?->id,
                'service_type'         => $validated['service_type'],
                'description'          => $description,
                'service_date'         => $validated['service_date'],
                'mileage_at_service'   => $validated['mileage_at_service'] ?? null,
                'cost'                 => $validated['cost'] ?? null,
                'invoice_number'       => $validated['invoice_number'] ?? null,
                'parts_replaced'       => !empty($validated['parts_replaced'])
                    ? array_values(array_filter(array_map('trim', explode(',', $validated['parts_replaced']))))
                    : null,
                'notes'                => $validated['notes'] ?? null,
                'next_service_date'    => $validated['next_service_date'] ?? null,
                'next_service_mileage' => $validated['next_service_mileage'] ?? null,
            ]);

            // ── Everything below requires a linked system vehicle ────────────────
            if (!$vehicle) return;

            // ── 3. Update vehicle mileage (triggers overdue check on schedules) ─
            if (!empty($validated['mileage_at_service'])) {
                $vehicle->updateMileage((int) $validated['mileage_at_service']);
            }

            // ── 4. Create expense record ─────────────────────────────────────────
            if (!empty($validated['cost'])) {
                $vehicle->expenses()->create([
                    'service_record_id' => $record->id,
                    'category'          => 'maintenance',
                    'description'       => $validated['service_type'] . ' — ' . $validated['description'],
                    'amount'            => $validated['cost'],
                    'expense_date'      => $validated['service_date'],
                    'odometer_reading'  => $validated['mileage_at_service'] ?? null,
                ]);
            }

            // ── 5. Maintenance: mark matching pending/overdue schedule done ──────
            $this->handleMaintenanceOnSave($vehicle, $validated, $record);
        });

        return redirect()->route('provider.service-records.index')
            ->with('success', 'Service record saved! Vehicle mileage, expenses, and maintenance updated.');
    }

    public function edit(ServiceRecord $serviceRecord)
    {
        $provider = $this->provider();
        abort_unless($serviceRecord->service_provider_id === $provider->id, 403);

        return view('provider.services.records.form', compact('provider', 'serviceRecord'));
    }

    public function update(Request $request, ServiceRecord $serviceRecord)
    {
        $provider = $this->provider();
        abort_unless($serviceRecord->service_provider_id === $provider->id, 403);

        $validated = $request->validate([
            'vehicle_make'         => 'required|string|max:100',
            'vehicle_model'        => 'required|string|max:100',
            'vehicle_year'         => 'required|integer|min:1900|max:' . (date('Y') + 2),
            'vehicle_plate'        => 'nullable|string|max:20',
            'service_type'         => 'required|string|max:255',
            'description'          => 'required|string|max:2000',
            'service_date'         => 'required|date',
            'mileage_at_service'   => 'nullable|integer|min:0',
            'cost'                 => 'nullable|numeric|min:0',
            'invoice_number'       => 'nullable|string|max:100',
            'parts_replaced'       => 'nullable|string|max:1000',
            'notes'                => 'nullable|string|max:2000',
            'next_service_date'    => 'nullable|date',
            'next_service_mileage' => 'nullable|integer|min:0',
        ]);

        DB::transaction(function () use ($validated, $serviceRecord) {

            // ── 1. Update service record ─────────────────────────────────────────
            $serviceRecord->update([
                'service_type'         => $validated['service_type'],
                'description'          => $validated['description'],
                'service_date'         => $validated['service_date'],
                'mileage_at_service'   => $validated['mileage_at_service'] ?? null,
                'cost'                 => $validated['cost'] ?? null,
                'invoice_number'       => $validated['invoice_number'] ?? null,
                'parts_replaced'       => !empty($validated['parts_replaced'])
                    ? array_values(array_filter(array_map('trim', explode(',', $validated['parts_replaced']))))
                    : null,
                'notes'                => $validated['notes'] ?? null,
                'next_service_date'    => $validated['next_service_date'] ?? null,
                'next_service_mileage' => $validated['next_service_mileage'] ?? null,
            ]);

            $vehicle = $serviceRecord->vehicle;
            if (!$vehicle) return;

            // ── 2. Update vehicle mileage ────────────────────────────────────────
            if (!empty($validated['mileage_at_service'])) {
                $vehicle->updateMileage((int) $validated['mileage_at_service']);
            }

            // ── 3. Update or create linked expense ───────────────────────────────
            if (!empty($validated['cost'])) {
                $expense = $serviceRecord->expense;
                if ($expense) {
                    $expense->update([
                        'description'      => $validated['service_type'] . ' — ' . $validated['description'],
                        'amount'           => $validated['cost'],
                        'expense_date'     => $validated['service_date'],
                        'odometer_reading' => $validated['mileage_at_service'] ?? null,
                    ]);
                } else {
                    $vehicle->expenses()->create([
                        'service_record_id' => $serviceRecord->id,
                        'category'          => 'maintenance',
                        'description'       => $validated['service_type'] . ' — ' . $validated['description'],
                        'amount'            => $validated['cost'],
                        'expense_date'      => $validated['service_date'],
                        'odometer_reading'  => $validated['mileage_at_service'] ?? null,
                    ]);
                }
            }

            // ── 4. Maintenance: mark matching schedule done, schedule next ───────
            $this->handleMaintenanceOnSave($vehicle, $validated, $serviceRecord);
        });

        return redirect()->route('provider.service-records.index')
            ->with('success', 'Service record updated! Vehicle mileage, expenses, and maintenance updated.');
    }

    public function destroy(ServiceRecord $serviceRecord)
    {
        $provider = $this->provider();
        abort_unless($serviceRecord->service_provider_id === $provider->id, 403);

        // Expense is deleted by DB cascade (service_record_id onDelete set null)
        $serviceRecord->delete();

        return back()->with('success', 'Service record deleted.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Maintenance logic
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * When a service record is saved:
     *
     * 1. Find the best matching pending/overdue schedule for this service_type
     *    and mark it completed (which auto-schedules next if recurring).
     *
     * 2. If no matching schedule found but next_service_date or
     *    next_service_mileage was filled in → create a new pending schedule.
     *
     * 3. If provider filled in next_service_date/mileage AND a matching
     *    schedule was found → update the new recurring schedule's due values
     *    (overrides the auto-calculated ones from createNextSchedule).
     */
    protected function handleMaintenanceOnSave(Vehicle $vehicle, array $data, ServiceRecord $record): void
    {
        $serviceType = $data['service_type'];
        $hasNextDate    = !empty($data['next_service_date']);
        $hasNextMileage = !empty($data['next_service_mileage']);

        // ── Find matching schedule (pending or overdue, same vehicle + type) ─
        $schedule = MaintenanceSchedule::where('vehicle_id', $vehicle->id)
            ->where('service_type', $serviceType)
            ->whereIn('status', ['pending', 'overdue', 'scheduled'])
            ->orderByRaw("FIELD(status, 'overdue', 'pending', 'scheduled')")  // overdue first
            ->orderBy('due_date')
            ->first();

        if ($schedule) {
            // Mark it done — if recurring, createNextSchedule() fires automatically
            $schedule->markCompleted();

            // If provider specified custom next date/mileage, update the newly
            // created recurring schedule to use those values instead
            if ($hasNextDate || $hasNextMileage) {
                $nextSchedule = MaintenanceSchedule::where('vehicle_id', $vehicle->id)
                    ->where('service_type', $serviceType)
                    ->where('status', 'pending')
                    ->latest()
                    ->first();

                if ($nextSchedule) {
                    $updates = [];
                    if ($hasNextDate)    $updates['due_date']    = $data['next_service_date'];
                    if ($hasNextMileage) $updates['due_mileage'] = $data['next_service_mileage'];
                    $nextSchedule->update($updates);
                }
            }
        } else {
            // No existing schedule — if provider gave next service info, create one
            if ($hasNextDate || $hasNextMileage) {
                MaintenanceSchedule::create([
                    'vehicle_id'  => $vehicle->id,
                    'service_type'=> $serviceType,
                    'description' => "Next {$serviceType} — scheduled by provider after service on " .
                                     \Carbon\Carbon::parse($data['service_date'])->format('M d, Y'),
                    'due_date'    => $hasNextDate    ? $data['next_service_date']    : null,
                    'due_mileage' => $hasNextMileage ? $data['next_service_mileage'] : null,
                    'priority'    => 'medium',
                    'status'      => 'pending',
                    'is_recurring'=> false,
                ]);
            }
        }
    }
}