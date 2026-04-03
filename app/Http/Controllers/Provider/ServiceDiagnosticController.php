<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\ServiceDiagnostic;
use App\Models\ServiceRecord;
use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceDiagnosticController extends Controller
{
    protected function provider()
    {
        return Auth::user()->serviceProvider;
    }

    // ── List all issues logged by this provider ───────────────────────────────
    public function index(Request $request)
    {
        $provider = $this->provider();
        $query = ServiceDiagnostic::where('service_provider_id', $provider->id)
            ->with(['vehicle', 'serviceRecord'])
            ->latest();

        $query->when($request->status,   fn($q, $s) => $q->where('status', $s));
        $query->when($request->severity, fn($q, $s) => $q->where('severity', $s));

        $issues = $query->paginate(20)->withQueryString();

        $stats = [
            'open'     => ServiceDiagnostic::where('service_provider_id', $provider->id)->whereIn('status', ['open','acknowledged','in_progress','monitoring'])->count(),
            'resolved' => ServiceDiagnostic::where('service_provider_id', $provider->id)->where('status', 'resolved')->count(),
            'critical' => ServiceDiagnostic::where('service_provider_id', $provider->id)->where('severity', 'critical')->whereIn('status', ['open','acknowledged','in_progress'])->count(),
        ];

        return view('provider.service-diagnostics.index', compact('issues', 'stats'));
    }

    // ── Update status of an issue ─────────────────────────────────────────────
    public function updateStatus(Request $request, ServiceDiagnostic $issue)
    {
        $provider = $this->provider();
        abort_unless($issue->service_provider_id === $provider->id, 403);

        $validated = $request->validate([
            'status'       => 'required|in:open,acknowledged,in_progress,resolved,ignored,monitoring',
            'status_notes' => 'nullable|string|max:500',
            'resolution_notes' => 'nullable|string|max:1000',
        ]);

        $updateData = [
            'status'                      => $validated['status'],
            'status_notes'                => $validated['status_notes'] ?? null,
            'status_updated_by_provider_id' => $provider->id,
            'status_updated_at'           => now(),
        ];

        if ($validated['status'] === 'resolved') {
            $updateData['resolved_by_provider_id'] = $provider->id;
            $updateData['resolved_at']             = now();
            $updateData['resolution_notes']        = $validated['resolution_notes'] ?? null;
        }

        $issue->update($updateData);

        // Notify the vehicle owner
        $this->notifyOwner($issue, $validated['status']);

        return back()->with('success', 'Issue status updated.');
    }

    // ── Notify the vehicle owner of status change ─────────────────────────────
    private function notifyOwner(ServiceDiagnostic $issue, string $newStatus): void
    {
        $vehicle = $issue->vehicle;
        if (!$vehicle?->user_id) return;

        $providerName = $issue->serviceProvider?->business_name ?? 'your provider';

        $matchResult = match($newStatus) {
            'resolved' => [
                '✅ Issue Resolved: ' . $issue->title,
                "The \"{$issue->title}\" issue on your {$vehicle->year} {$vehicle->make} {$vehicle->model} has been marked as resolved by {$providerName}.",
                'info',
            ],
            'in_progress' => [
                '🔧 Issue Being Fixed: ' . $issue->title,
                "Work has started on \"{$issue->title}\" for your {$vehicle->year} {$vehicle->make} {$vehicle->model}.",
                'info',
            ],
            default => null,
        };

        if (!$matchResult) {
            return;
        }

        [$title, $message, $priority] = $matchResult;

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
}