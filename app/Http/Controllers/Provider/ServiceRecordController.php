<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\ServiceRecord;
use App\Models\ServiceDiagnostic;
use App\Models\Vehicle;
use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceRecordController extends Controller
{
    protected function provider()
    {
        return Auth::user()->serviceProvider;
    }

    public function index()
    {
        $provider = $this->provider();
        $records  = ServiceRecord::where('service_provider_id', $provider->id)
            ->with(['vehicle'])
            ->withCount('serviceDiagnostics')
            ->latest('service_date')
            ->paginate(20);

        $stats = [
            'total'    => ServiceRecord::where('service_provider_id', $provider->id)->count(),
            'this_month' => ServiceRecord::where('service_provider_id', $provider->id)
                ->whereMonth('service_date', now()->month)
                ->whereYear('service_date', now()->year)
                ->count(),
            'open_diagnostics' => \App\Models\ServiceDiagnostic::where('service_provider_id', $provider->id)
                ->whereIn('status', ['open', 'acknowledged', 'in_progress'])
                ->count(),
            'total_revenue' => ServiceRecord::where('service_provider_id', $provider->id)
                ->whereMonth('service_date', now()->month)
                ->sum('cost'),
        ];

        return view('provider.services.records.index', compact('records', 'stats'));
    }

    public function create(Request $request)
    {
        $prefill = null;
        if ($request->job_id) {
            $prefill = \App\Models\ServiceJobPost::with('vehicle')
                ->where('id', $request->job_id)
                ->first();
        }
        return view('provider.services.records.form', compact('prefill'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_make'        => 'required|string|max:100',
            'vehicle_model'       => 'required|string|max:100',
            'vehicle_year'        => 'required|integer|min:1990|max:' . (date('Y') + 2),
            'vehicle_plate'       => 'nullable|string|max:20',
            'mileage_at_service'  => 'nullable|integer|min:0',
            'service_type'        => 'required|string|max:100',
            'service_date'        => 'required|date|before_or_equal:today',
            'description'         => 'required|string|max:3000',
            'parts_replaced'      => 'nullable|string|max:500',
            'invoice_number'      => 'nullable|string|max:100',
            'notes'               => 'nullable|string|max:2000',
            'cost'                => 'nullable|numeric|min:0',
            'next_service_date'   => 'nullable|date|after:today',
            'next_service_mileage'=> 'nullable|integer|min:0',
            'vehicle_id'          => 'nullable|exists:vehicles,id',
            // Before/after photo evidence
            'before_photos'       => 'nullable|array|max:6',
            'before_photos.*'     => 'file|image|mimes:jpg,jpeg,png,webp|max:8192',
            'after_photos'        => 'nullable|array|max:6',
            'after_photos.*'      => 'file|image|mimes:jpg,jpeg,png,webp|max:8192',
            'evidence_notes'      => 'nullable|string|max:1000',
            // Issues
            'issues'              => 'nullable|array',
            'issues.*.title'       => 'required|string|max:200',
            'issues.*.description' => 'required|string|max:1000',
            'issues.*.category'    => 'nullable|string',
            'issues.*.location'    => 'nullable|string|max:100',
            'issues.*.severity'    => 'nullable|in:low,medium,high,critical',
            'issues.*.is_safety_critical' => 'nullable|in:0,1',
            'issues.*.estimated_cost_min' => 'nullable|numeric|min:0',
            'issues.*.estimated_cost_max' => 'nullable|numeric|min:0',
        ]);

        $provider = $this->provider();

        // Upload before/after evidence photos
        $beforePaths = [];
        $afterPaths  = [];
        if ($request->hasFile('before_photos')) {
            foreach ($request->file('before_photos') as $file) {
                $beforePaths[] = $file->store('service-records/before', 'public');
            }
        }
        if ($request->hasFile('after_photos')) {
            foreach ($request->file('after_photos') as $file) {
                $afterPaths[] = $file->store('service-records/after', 'public');
            }
        }

        DB::transaction(function () use ($validated, $request, $provider, $beforePaths, $afterPaths) {
            // Parse parts
            $parts = null;
            if (!empty($validated['parts_replaced'])) {
                $parts = array_values(array_filter(
                    array_map('trim', explode(',', $validated['parts_replaced']))
                ));
            }

            $record = ServiceRecord::create([
                'service_provider_id'  => $provider->id,
                'vehicle_id'           => $validated['vehicle_id'] ?? null,
                'service_type'         => $validated['service_type'],
                'description'          => $validated['description'],
                'service_date'         => $validated['service_date'],
                'mileage_at_service'   => $validated['mileage_at_service'] ?? null,
                'cost'                 => $validated['cost'] ?? null,
                'invoice_number'       => $validated['invoice_number'] ?? null,
                'parts_replaced'       => $parts,
                'notes'                => $validated['notes'] ?? null,
                'next_service_date'    => $validated['next_service_date'] ?? null,
                'next_service_mileage' => $validated['next_service_mileage'] ?? null,
                'before_photos'        => $beforePaths ?: null,
                'after_photos'         => $afterPaths  ?: null,
                'evidence_notes'       => $validated['evidence_notes'] ?? null,
            ]);

            // Save issues
            if (!empty($validated['issues'])) {
                $this->saveIssues($record, $validated['issues'], $provider);
            }
        });

        return redirect()->route('provider.service-records.index')
            ->with('success', 'Service record saved successfully!');
    }

    public function show($record)
    {
        $record = ServiceRecord::findOrFail($record);
        // $this->authorizeRecord($record);

        $record->load(['vehicle', 'serviceDiagnostics']);
        return view('provider.services.records.show', compact('record'));
    }

public function edit(ServiceRecord $serviceRecord)
{
    $serviceRecord->load('serviceDiagnostics');

    return view('provider.services.records.form', [
        'serviceRecord' => $serviceRecord
    ]);
}

    public function destroy($id)
    {
        $record = ServiceRecord::findOrFail($id);
        $record->delete();
        return redirect()->route('provider.service-records.index')
            ->with('success', 'Service record deleted.');
    }

    public function update(Request $request, ServiceRecord $record)
    {
        $this->authorizeRecord($record);

        $validated = $request->validate([
            'vehicle_make'        => 'required|string|max:100',
            'vehicle_model'       => 'required|string|max:100',
            'vehicle_year'        => 'required|integer|min:1990|max:' . (date('Y') + 2),
            'mileage_at_service'  => 'nullable|integer|min:0',
            'service_type'        => 'required|string|max:100',
            'service_date'        => 'required|date|before_or_equal:today',
            'description'         => 'required|string|max:3000',
            'parts_replaced'      => 'nullable|string|max:500',
            'invoice_number'      => 'nullable|string|max:100',
            'notes'               => 'nullable|string|max:2000',
            'cost'                => 'nullable|numeric|min:0',
            'next_service_date'   => 'nullable|date|after:today',
            'next_service_mileage'=> 'nullable|integer|min:0',
            // Before/after photo evidence
            'before_photos'       => 'nullable|array|max:6',
            'before_photos.*'     => 'file|image|mimes:jpg,jpeg,png,webp|max:8192',
            'after_photos'        => 'nullable|array|max:6',
            'after_photos.*'      => 'file|image|mimes:jpg,jpeg,png,webp|max:8192',
            'evidence_notes'      => 'nullable|string|max:1000',
            'issues'              => 'nullable|array',
            'issues.*.title'       => 'required|string|max:200',
            'issues.*.description' => 'required|string|max:1000',
            'issues.*.category'    => 'nullable|string',
            'issues.*.location'    => 'nullable|string|max:100',
            'issues.*.severity'    => 'nullable|in:low,medium,high,critical',
            'issues.*.is_safety_critical' => 'nullable|in:0,1',
            'issues.*.estimated_cost_min' => 'nullable|numeric|min:0',
            'issues.*.estimated_cost_max' => 'nullable|numeric|min:0',
        ]);

        $provider = $this->provider();

        // Upload new evidence photos (merge with existing)
        $beforePaths = $record->before_photos ?? [];
        $afterPaths  = $record->after_photos  ?? [];
        if ($request->hasFile('before_photos')) {
            foreach ($request->file('before_photos') as $file) {
                $beforePaths[] = $file->store('service-records/before', 'public');
            }
        }
        if ($request->hasFile('after_photos')) {
            foreach ($request->file('after_photos') as $file) {
                $afterPaths[] = $file->store('service-records/after', 'public');
            }
        }

        DB::transaction(function () use ($validated, $record, $provider, $beforePaths, $afterPaths) {
            $parts = null;
            if (!empty($validated['parts_replaced'])) {
                $parts = array_values(array_filter(
                    array_map('trim', explode(',', $validated['parts_replaced']))
                ));
            }

            $record->update([
                'service_type'         => $validated['service_type'],
                'description'          => $validated['description'],
                'service_date'         => $validated['service_date'],
                'mileage_at_service'   => $validated['mileage_at_service'] ?? null,
                'cost'                 => $validated['cost'] ?? null,
                'invoice_number'       => $validated['invoice_number'] ?? null,
                'parts_replaced'       => $parts,
                'notes'                => $validated['notes'] ?? null,
                'next_service_date'    => $validated['next_service_date'] ?? null,
                'next_service_mileage' => $validated['next_service_mileage'] ?? null,
                'before_photos'        => $beforePaths ?: null,
                'after_photos'         => $afterPaths  ?: null,
                'evidence_notes'       => !empty($validated['evidence_notes'])
                                            ? $validated['evidence_notes']
                                            : $record->evidence_notes,
            ]);

            // Append new issues (don't delete existing ones)
            if (!empty($validated['issues'])) {
                $this->saveIssues($record, $validated['issues'], $provider);
            }
        });

        return redirect()->route('provider.service-records.index')
            ->with('success', 'Service record updated!');
    }

    // ── Save issues and alert vehicle owner ──────────────────────────────────
    private function saveIssues(ServiceRecord $record, array $issues, $provider): void
    {
        $hasSafetyCritical = false;
        $issuesList        = [];

        foreach ($issues as $data) {
            if (empty($data['title']) || empty($data['description'])) continue;

            $isSafety = ($data['is_safety_critical'] ?? '0') === '1';
            if ($isSafety) $hasSafetyCritical = true;

            $issue = ServiceDiagnostic::create([
                'vehicle_id'          => $record->vehicle_id,
                'service_record_id'   => $record->id,
                'service_provider_id' => $provider->id,
                'title'               => $data['title'],
                'description'         => $data['description'],
                'category'            => $data['category'] ?? 'other',
                'location'            => $data['location'] ?? null,
                'severity'            => $data['severity'] ?? 'medium',
                'is_safety_critical'  => $isSafety,
                'estimated_cost_min'  => $data['estimated_cost_min'] ?? null,
                'estimated_cost_max'  => $data['estimated_cost_max'] ?? null,
                'status'              => 'open',
            ]);

            $issuesList[] = $issue;
        }

        // Alert vehicle owner
        if (!empty($issuesList) && $record->vehicle?->user_id) {
            $this->alertVehicleOwner($record, $issuesList, $hasSafetyCritical, $provider);
        }
    }

    private function alertVehicleOwner(ServiceRecord $record, array $issues, bool $hasSafety, $provider): void
    {
        $vehicle  = $record->vehicle;
        $count    = count($issues);
        $provName = $provider->business_name ?? 'Your service provider';
        $vehName  = "{$vehicle->year} {$vehicle->make} {$vehicle->model}";

        if ($hasSafety) {
            $title    = "⚠️ Safety Issue Found on Your {$vehName}";
            $message  = "{$provName} flagged {$count} issue" . ($count > 1 ? 's' : '') .
                        " during your recent service — including a SAFETY CRITICAL concern. Please review immediately.";
            $priority = 'critical';
        } else {
            $severities = array_count_values(array_column(
                array_map(fn($i) => ['severity' => $i->severity], $issues),
                'severity'
            ));
            $worst    = array_key_exists('high', $severities) ? 'high' : 'medium';
            $title    = "🔍 {$count} Issue" . ($count > 1 ? 's' : '') . " Found on Your {$vehName}";
            $message  = "{$provName} flagged {$count} issue" . ($count > 1 ? 's' : '') .
                        " during your recent " . $record->service_type . " service. Tap to view details and cost estimates.";
            $priority = $worst === 'high' ? 'warning' : 'info';
        }

        Alert::create([
            'user_id'      => $vehicle->user_id,
            'vehicle_id'   => $vehicle->id,
            'type'         => 'maintenance',
            'title'        => $title,
            'message'      => $message,
            'action_url'   => route('vehicles.show', $vehicle),
            'priority'     => $priority,
            'for_provider' => false,
        ]);
    }

    private function authorizeRecord(ServiceRecord $record): void
    {
        abort_unless(
            $record->service_provider_id === $this->provider()->id,
            403
        );
    }
}