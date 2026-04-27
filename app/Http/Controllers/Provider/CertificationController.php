<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\ProviderCertification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CertificationController extends Controller
{
    private function provider()
    {
        return Auth::user()->serviceProvider;
    }

    // GET /provider/certifications
    public function index()
    {
        $provider       = $this->provider();
        $certifications = ProviderCertification::where('service_provider_id', $provider->id)
            ->latest()
            ->get();

        $stats = [
            'total'    => $certifications->count(),
            'approved' => $certifications->where('status', 'approved')->count(),
            'pending'  => $certifications->where('status', 'pending')->count(),
            'rejected' => $certifications->where('status', 'rejected')->count(),
        ];

        return view('provider.certifications.index', compact('provider', 'certifications', 'stats'));
    }

    // GET /provider/certifications/create
    public function create()
    {
        $provider = $this->provider();
        return view('provider.certifications.create', compact('provider'));
    }

    // POST /provider/certifications
    public function store(Request $request)
    {
        $provider = $this->provider();

        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'issuing_body'       => 'required|string|max:255',
            'certificate_number' => 'nullable|string|max:100',
            'issued_at'          => 'nullable|date|before_or_equal:today',
            'expires_at'         => 'nullable|date|after:issued_at',
            'show_on_profile'    => 'boolean',
            'file'               => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:5120', // 5MB
        ]);

        $file     = $request->file('file');
        $path     = $file->store("certifications/{$provider->id}", 'public');

        ProviderCertification::create([
            ...$validated,
            'service_provider_id' => $provider->id,
            'file_path'           => $path,
            'file_original_name'  => $file->getClientOriginalName(),
            'file_mime'           => $file->getMimeType(),
            'file_size'           => $file->getSize(),
            'status'              => 'pending',
            'show_on_profile'     => $request->boolean('show_on_profile', true),
        ]);

        // Notify all admins
        $adminIds = \App\Models\User::where('role', 'admin')->pluck('id');
        foreach ($adminIds as $adminId) {
            Alert::create([
                'user_id'      => $adminId,
                'type'         => 'system',
                'title'        => '📋 New Certification Submitted',
                'message'      => "{$provider->business_name} submitted a new certification: {$validated['name']}. Requires review.",
                'action_url'   => route('admin.certifications.show', ProviderCertification::where('service_provider_id', $provider->id)->latest()->first()),
                'priority'     => 'info',
                'for_provider' => false,
            ]);
        }

        return redirect()->route('provider.certifications.index')
            ->with('success', 'Certification submitted for review. You\'ll be notified once it\'s approved.');
    }

    // DELETE /provider/certifications/{certification}
    public function destroy(ProviderCertification $certification)
    {
        abort_unless($certification->service_provider_id === $this->provider()->id, 403);

        Storage::disk('public')->delete($certification->file_path);
        $certification->delete();

        return back()->with('success', 'Certification removed.');
    }

    // PATCH /provider/certifications/{certification}/toggle
    public function toggle(ProviderCertification $certification)
    {
        abort_unless($certification->service_provider_id === $this->provider()->id, 403);
        abort_unless($certification->isApproved(), 403);

        $certification->update(['show_on_profile' => !$certification->show_on_profile]);

        return back()->with('success', $certification->show_on_profile
            ? 'Badge now visible on your profile.'
            : 'Badge hidden from your profile.'
        );
    }
}