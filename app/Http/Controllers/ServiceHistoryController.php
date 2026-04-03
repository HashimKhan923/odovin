<?php

namespace App\Http\Controllers;

use App\Models\ServiceRecord;
use App\Models\ServiceDiagnostic;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class ServiceHistoryController extends Controller
{
    // All service records across all consumer's vehicles
    public function index(Request $request)
    {
        $userId   = auth()->id();
        $vehicles = Vehicle::where('user_id', $userId)->get();
        $vehicleIds = $vehicles->pluck('id');

        $records = ServiceRecord::whereIn('vehicle_id', $vehicleIds)
            ->with(['vehicle', 'serviceProvider', 'serviceDiagnostics'])
            ->when($request->vehicle_id, fn($q, $v) => $q->where('vehicle_id', $v))
            ->when($request->service_type, fn($q, $t) => $q->where('service_type', $t))
            ->latest('service_date')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total_services'   => ServiceRecord::whereIn('vehicle_id', $vehicleIds)->count(),
            'total_spent'      => ServiceRecord::whereIn('vehicle_id', $vehicleIds)->sum('cost'),
            'open_diagnostics' => ServiceDiagnostic::whereIn('vehicle_id', $vehicleIds)
                                    ->whereIn('status', ['open', 'acknowledged', 'in_progress'])->count(),
            'this_year'        => ServiceRecord::whereIn('vehicle_id', $vehicleIds)
                                    ->whereYear('service_date', now()->year)->count(),
        ];

        return view('service-history.index', compact('records', 'vehicles', 'stats'));
    }

    // Single service record detail
    public function show(ServiceRecord $record)
    {
        // Ensure the record belongs to one of this user's vehicles
        abort_unless(
            Vehicle::where('id', $record->vehicle_id)
                   ->where('user_id', auth()->id())
                   ->exists(),
            403
        );

        $record->load(['vehicle', 'serviceProvider', 'serviceDiagnostics']);

        return view('service-history.show', compact('record'));
    }

    // All diagnostics across all vehicles
    public function diagnostics(Request $request)
    {
        $vehicleIds = Vehicle::where('user_id', auth()->id())->pluck('id');

        $diagnostics = ServiceDiagnostic::whereIn('vehicle_id', $vehicleIds)
            ->with(['vehicle', 'serviceProvider', 'serviceRecord'])
            ->when($request->status,   fn($q, $s) => $q->where('status', $s))
            ->when($request->severity, fn($q, $s) => $q->where('severity', $s))
            ->when($request->vehicle_id, fn($q, $v) => $q->where('vehicle_id', $v))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $vehicles = Vehicle::where('user_id', auth()->id())->get();

        $stats = [
            'open'     => ServiceDiagnostic::whereIn('vehicle_id', $vehicleIds)->whereIn('status', ['open','acknowledged','in_progress'])->count(),
            'critical' => ServiceDiagnostic::whereIn('vehicle_id', $vehicleIds)->where('severity', 'critical')->whereIn('status', ['open','acknowledged','in_progress'])->count(),
            'safety'   => ServiceDiagnostic::whereIn('vehicle_id', $vehicleIds)->where('is_safety_critical', true)->whereIn('status', ['open','acknowledged','in_progress'])->count(),
            'resolved' => ServiceDiagnostic::whereIn('vehicle_id', $vehicleIds)->where('status', 'resolved')->count(),
        ];

        return view('service-history.diagnostics', compact('diagnostics', 'vehicles', 'stats'));
    }
}