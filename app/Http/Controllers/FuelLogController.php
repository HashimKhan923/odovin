<?php

namespace App\Http\Controllers;

use App\Models\FuelLog;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;


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

        // Get the selected vehicle ID

        $selectedVehicleId = $request->vehicle_id;

        $mpgChartData = FuelLog::whereHas('vehicle', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            })
            ->when($selectedVehicleId, function ($q) use ($selectedVehicleId) {
                $q->where('vehicle_id', $selectedVehicleId);
            })
            ->whereNotNull('mpg')
            ->where('mpg', '>', 0)
            ->orderBy('fill_date')
            ->get()
            ->map(function ($log) {
                return [
                    'date' => $log->fill_date->format('Y-m-d'),
                    'mpg' => round($log->mpg, 2),
                ];
            });

            // Calculate Fuel Cost per Mile

            $totalMiles = 0;
            $totalFuelCost = 0;

            $fuelLogsForCost = FuelLog::whereHas('vehicle', function ($q) use ($request) {
                    $q->where('user_id', $request->user()->id);
                })
                ->when($selectedVehicleId, function ($q) use ($selectedVehicleId) {
                    $q->where('vehicle_id', $selectedVehicleId);
                })
                ->orderBy('fill_date')
                ->get();

            for ($i = 1; $i < $fuelLogsForCost->count(); $i++) {
                $previous = $fuelLogsForCost[$i - 1];
                $current = $fuelLogsForCost[$i];

                $milesDriven = $current->odometer - $previous->odometer;

                if ($milesDriven > 0) {
                    $totalMiles += $milesDriven;
                    $totalFuelCost += $current->total_cost;
                }
            }

            $fuelCostPerMile = $totalMiles > 0
                ? round($totalFuelCost / $totalMiles, 3)
                : null;

            // Calculate Vehicle Average MPG

            $vehicleAvgMpg = FuelLog::whereHas('vehicle', function ($q) use ($request) {
                    $q->where('user_id', $request->user()->id);
                })
                ->when($selectedVehicleId, fn ($q) => $q->where('vehicle_id', $selectedVehicleId))
                    ->whereNotNull('mpg')
                    ->where('mpg', '>', 0)
                    ->avg('mpg');

                $logs->getCollection()->transform(function ($log) use ($vehicleAvgMpg) {
                    $log->mpg_anomaly = $this->evaluateMpgAnomaly($log, $vehicleAvgMpg);
                    return $log;
                });

            ////////////////////////////////////////////////////////////////////////////////    



        return view('fuel.index', compact('logs', 'vehicles', 'stats', 'mpgChartData', 'selectedVehicleId', 'fuelCostPerMile',
            'totalMiles',
            'totalFuelCost'));
    }

    public function create()
    {
        $vehicles = auth()->user()->vehicles()->active()->get();
        return view('fuel.create', compact('vehicles'));
    }

    public function edit(FuelLog $fuelLog)
    {
        // $this->authorize('update', $fuelLog);

        $vehicles = auth()->user()->vehicles()->active()->get();

        return view('fuel.edit', compact('fuelLog', 'vehicles'));
    }

    public function update(Request $request, FuelLog $fuelLog)
    {
        // $this->authorize('update', $fuelLog);

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
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Recalculate MPG
        $validated['mpg'] = null;
        if ($validated['is_full_tank']) {
            $previousLog = $vehicle->fuelLogs()
                ->where('id', '!=', $fuelLog->id)
                ->where('fill_date', '<', $validated['fill_date'])
                ->latest('fill_date')
                ->first();

            if ($previousLog) {
                $miles = $validated['odometer'] - $previousLog->odometer;
                if ($miles > 0) {
                    $validated['mpg'] = round($miles / $validated['gallons'], 2);
                }
            }
        }

        $fuelLog->update($validated);

        return redirect()
            ->route('fuel.index')
            ->with('success', 'Fuel log updated successfully.');
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

    public function destroy(FuelLog $fuelLog)
    {
        // $this->authorize('delete', $fuelLog);

        $fuelLog->delete();

        return back()->with('success', 'Fuel log deleted.');
    }

    private function evaluateMpgAnomaly(FuelLog $log, float $vehicleAvgMpg = null): ?string
    {
        if (!$log->mpg || $log->mpg <= 0) {
            return null;
        }

        if ($log->mpg > 200) {
            return 'impossible';
        }

        if ($log->mpg < 5 || $log->mpg > 80) {
            return 'unrealistic';
        }

        if ($vehicleAvgMpg && abs($log->mpg - $vehicleAvgMpg) / $vehicleAvgMpg > 0.5) {
            return 'suspicious';
        }

        return null;
    }

    public function importForm()
    {
        return view('fuel.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = fopen($request->file('file')->getRealPath(), 'r');

        $header = fgetcsv($file);
        $rowsImported = 0;
        $rowsSkipped = 0;

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($file)) !== false) {
                $data = array_combine($header, $row);

                // Validate row
                $validator = Validator::make($data, [
                    'vehicle_id' => 'required|integer',
                    'fill_date' => 'required|date',
                    'odometer' => 'required|integer|min:0',
                    'gallons' => 'required|numeric|min:0',
                    'price_per_gallon' => 'required|numeric|min:0',
                    'total_cost' => 'required|numeric|min:0',
                    'is_full_tank' => 'nullable|boolean',
                ]);

                if ($validator->fails()) {
                    $rowsSkipped++;
                    continue;
                }

                // Verify vehicle ownership
                $vehicle = Vehicle::where('id', $data['vehicle_id'])
                    ->where('user_id', auth()->id())
                    ->first();

                if (!$vehicle) {
                    $rowsSkipped++;
                    continue;
                }

                // Prevent duplicates
                $exists = FuelLog::where('vehicle_id', $vehicle->id)
                    ->where('fill_date', $data['fill_date'])
                    ->where('odometer', $data['odometer'])
                    ->exists();

                if ($exists) {
                    $rowsSkipped++;
                    continue;
                }

                // MPG calculation
                $mpg = null;
                if (!empty($data['is_full_tank'])) {
                    $previous = $vehicle->fuelLogs()
                        ->where('fill_date', '<', $data['fill_date'])
                        ->latest('fill_date')
                        ->first();

                    if ($previous) {
                        $miles = $data['odometer'] - $previous->odometer;
                        if ($miles > 0) {
                            $mpg = round($miles / $data['gallons'], 2);
                        }
                    }
                }

                FuelLog::create([
                    'vehicle_id' => $vehicle->id,
                    'fill_date' => $data['fill_date'],
                    'odometer' => $data['odometer'],
                    'gallons' => $data['gallons'],
                    'price_per_gallon' => $data['price_per_gallon'],
                    'total_cost' => $data['total_cost'],
                    'is_full_tank' => !empty($data['is_full_tank']),
                    'gas_station' => $data['gas_station'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'mpg' => $mpg,
                ]);

                $rowsImported++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }

        return redirect()
            ->route('fuel.index')
            ->with('success', "{$rowsImported} rows imported, {$rowsSkipped} skipped.");
    }

    public function exportCsv(Request $request, $vehicle_id = null)
    {
        $user = $request->user();

        // If vehicle_id is provided, validate ownership
        if ($vehicle_id) {
            $request->merge(['vehicle_id' => $vehicle_id]);

            $request->validate([
                'vehicle_id' => [
                    'integer',
                    \Illuminate\Validation\Rule::exists('vehicles', 'id')
                        ->where('user_id', $user->id),
                ],
            ]);
        }

        $logsQuery = FuelLog::whereHas('vehicle', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with('vehicle');

        if ($vehicle_id) {
            $logsQuery->where('vehicle_id', $vehicle_id);
        }

        $logs = $logsQuery->orderBy('fill_date')->get();

        if ($logs->isEmpty()) {
            return back()->with('error', 'No fuel logs found for export.');
        }

        $filename = 'fuel_logs_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($logs) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Vehicle',
                'Fill Date',
                'Odometer',
                'Gallons',
                'Price Per Gallon',
                'Total Cost',
                'MPG',
                'Gas Station',
                'Notes',
            ]);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->vehicle->full_name,
                    $log->fill_date->format('Y-m-d'),
                    $log->odometer,
                    $log->gallons,
                    $log->price_per_gallon,
                    $log->total_cost,
                    $log->mpg,
                    $log->gas_station,
                    $log->notes,
                ]);
            }

            fclose($handle);
        }, $filename);
    }


    public function exportPdf(Request $request)
    {
        $user = $request->user();

        $logsQuery = FuelLog::whereHas('vehicle', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with('vehicle');

        if ($request->vehicle_id) {
            $logsQuery->where('vehicle_id', $request->vehicle_id);
        }

        $logs = $logsQuery->orderBy('fill_date')->get();

        $pdf = Pdf::loadView('fuel.export-pdf', [
            'logs' => $logs,
            'user' => $user,
        ]);

        return $pdf->download('fuel_logs_' . now()->format('Ymd') . '.pdf');
    }



}