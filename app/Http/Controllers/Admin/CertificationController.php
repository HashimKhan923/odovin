<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\ProviderCertification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificationController extends Controller
{
    // GET /admin/certifications
    public function index(Request $request)
    {
        $query = ProviderCertification::with(['provider', 'reviewer'])->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $certifications = $query->paginate(20)->withQueryString();

        $stats = [
            'pending'  => ProviderCertification::where('status', 'pending')->count(),
            'approved' => ProviderCertification::where('status', 'approved')->count(),
            'rejected' => ProviderCertification::where('status', 'rejected')->count(),
            'total'    => ProviderCertification::count(),
        ];

        return view('admin.certifications.index', compact('certifications', 'stats'));
    }

    // GET /admin/certifications/{certification}
    public function show(ProviderCertification $certification)
    {
        $certification->load(['provider', 'reviewer']);
        return view('admin.certifications.show', compact('certification'));
    }

    // POST /admin/certifications/{certification}/approve
    public function approve(Request $request, ProviderCertification $certification)
    {
        abort_unless($certification->isPending() || $certification->isRejected(), 422);

        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $certification->update([
            'status'      => 'approved',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Check if provider now has at least one approved cert → mark verified
        $approvedCount = ProviderCertification::where('service_provider_id', $certification->service_provider_id)
            ->where('status', 'approved')
            ->count();

        if ($approvedCount >= 1) {
            $certification->provider->update(['is_verified' => true]);
        }

        // Notify provider
        Alert::create([
            'user_id'      => $certification->provider->user_id,
            'type'         => 'system',
            'title'        => '✅ Certification Approved!',
            'message'      => "Your certification \"{$certification->name}\" has been verified. Your profile now shows the verified badge.",
            'action_url'   => route('provider.certifications.index'),
            'priority'     => 'info',
            'for_provider' => true,
        ]);

        return back()->with('success', "Certification approved. Provider has been notified.");
    }

    // POST /admin/certifications/{certification}/reject
    public function reject(Request $request, ProviderCertification $certification)
    {
        abort_unless($certification->isPending(), 422);

        $request->validate([
            'admin_notes' => 'required|string|max:500',
        ]);

        $certification->update([
            'status'      => 'rejected',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Notify provider
        Alert::create([
            'user_id'      => $certification->provider->user_id,
            'type'         => 'system',
            'title'        => '↩ Certification Needs Attention',
            'message'      => "Your certification \"{$certification->name}\" could not be verified. Reason: {$request->admin_notes}",
            'action_url'   => route('provider.certifications.index'),
            'priority'     => 'warning',
            'for_provider' => true,
        ]);

        return back()->with('info', "Certification rejected. Provider has been notified.");
    }
}