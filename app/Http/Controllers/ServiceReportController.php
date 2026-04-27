<?php

namespace App\Http\Controllers;

use App\Models\ServiceReportShare;
use App\Models\Vehicle;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ServiceReportController extends Controller
{
    // ── GET /service-history/report ───────────────────────────────
    // List all share links the consumer has created

    public function index()
    {
        $vehicleIds = Vehicle::where('user_id', auth()->id())->pluck('id');
        $shares     = ServiceReportShare::whereIn('vehicle_id', $vehicleIds)
            ->with('vehicle')
            ->latest()
            ->get();
        $vehicles   = Vehicle::where('user_id', auth()->id())->get();

        return view('service-history.report.index', compact('shares', 'vehicles'));
    }

    // ── GET /service-history/report/create ────────────────────────
    // Show the create share form

    public function create(Request $request)
    {
        $vehicles = Vehicle::where('user_id', auth()->id())->get();
        $selected = $request->vehicle_id
            ? $vehicles->firstWhere('id', $request->vehicle_id)
            : $vehicles->first();

        return view('service-history.report.create', compact('vehicles', 'selected'));
    }

    // ── POST /service-history/report ──────────────────────────────
    // Create a new share link

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id'               => 'required|exists:vehicles,id',
            'label'                    => 'nullable|string|max:100',
            'include_costs'            => 'boolean',
            'include_diagnostics'      => 'boolean',
            'include_provider_details' => 'boolean',
            'include_photos'           => 'boolean',
            'from_date'                => 'nullable|date',
            'to_date'                  => 'nullable|date|after_or_equal:from_date',
            'expires_in'               => 'nullable|in:7,30,90,365,0',
        ]);

        // Ensure vehicle belongs to user
        abort_unless(
            Vehicle::where('id', $validated['vehicle_id'])
                   ->where('user_id', auth()->id())
                   ->exists(),
            403
        );

        $expiresAt = null;
        if (!empty($validated['expires_in']) && $validated['expires_in'] > 0) {
            $expiresAt = now()->addDays((int) $validated['expires_in']);
        }

        $share = ServiceReportShare::create([
            'user_id'                  => auth()->id(),
            'vehicle_id'               => $validated['vehicle_id'],
            'label'                    => $validated['label'] ?? null,
            'include_costs'            => $request->boolean('include_costs', true),
            'include_diagnostics'      => $request->boolean('include_diagnostics', true),
            'include_provider_details' => $request->boolean('include_provider_details', true),
            'include_photos'           => $request->boolean('include_photos', true),
            'from_date'                => $validated['from_date'] ?? null,
            'to_date'                  => $validated['to_date'] ?? null,
            'expires_at'               => $expiresAt,
        ]);

        return redirect()->route('service-history.report.show', $share)
            ->with('success', 'Share link created! Copy the link and send it to anyone.');
    }

    // ── GET /service-history/report/{share} ───────────────────────
    // Owner: preview + manage a share link

    public function show(ServiceReportShare $share)
    {
        abort_unless($share->user_id === auth()->id(), 403);
        $share->load('vehicle');
        $records = $share->recordQuery()->get();

        return view('service-history.report.show', compact('share', 'records'));
    }

    // ── DELETE /service-history/report/{share} ────────────────────
    // Revoke a share link

    public function destroy(ServiceReportShare $share)
    {
        abort_unless($share->user_id === auth()->id(), 403);
        $share->delete();
        return redirect()->route('service-history.report.index')
            ->with('success', 'Share link revoked.');
    }

    // ── GET /report/{token} ───────────────────────────────────────
    // PUBLIC: anyone with the link views the report (HTML)

    public function public(string $token)
    {
        $share = ServiceReportShare::where('token', $token)->firstOrFail();

        if ($share->isExpired()) {
            return view('service-history.report.expired');
        }

        // Track views
        $share->increment('view_count');
        $share->update(['last_viewed_at' => now()]);

        $vehicle = $share->vehicle;
        $records = $share->recordQuery()->get();

        return view('service-history.report.public', compact('share', 'vehicle', 'records'));
    }

    // ── GET /report/{token}/pdf ───────────────────────────────────
    // PUBLIC: download the PDF version

    public function pdf(string $token)
    {
        $share = ServiceReportShare::where('token', $token)->firstOrFail();

        if ($share->isExpired()) {
            abort(410, 'This report link has expired.');
        }

        $vehicle = $share->vehicle;
        $records = $share->recordQuery()->get();

        $pdf = Pdf::loadView('service-history.report.pdf', compact('share', 'vehicle', 'records'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'    => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled'   => false,
                'dpi'            => 150,
            ]);

        $filename = 'service-report-'
            . str($vehicle->make . '-' . $vehicle->model)->slug()
            . '-' . now()->format('Ymd')
            . '.pdf';

        return $pdf->download($filename);
    }
}