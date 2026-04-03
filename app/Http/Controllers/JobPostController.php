<?php

namespace App\Http\Controllers;

use App\Models\ServiceJobPost;
use App\Models\ServiceJobOffer;
use App\Models\ServiceProvider;
use App\Models\ServiceDiagnostic;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\JobNotificationService;

class JobPostController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceJobPost::where('user_id', $request->user()->id)
            ->with(['vehicle', 'offers'])
            ->latest();

        $query->when($request->status, fn($q, $s) => $q->where('status', $s));

        $jobs = $query->paginate(12)->withQueryString();

        $stats = [
            'open'      => ServiceJobPost::where('user_id', $request->user()->id)->where('status', 'open')->count(),
            'accepted'  => ServiceJobPost::where('user_id', $request->user()->id)->where('status', 'accepted')->count(),
            'completed' => ServiceJobPost::where('user_id', $request->user()->id)->where('status', 'completed')->count(),
        ];

        return view('jobs.index', compact('jobs', 'stats'));
    }

    public function create(Request $request)
    {
        $vehicles = $request->user()->vehicles()->active()->get();

        $serviceTypes = [
            'Oil Change', 'Tire Rotation', 'Brake Service', 'Battery Replacement',
            'Air Filter Replacement', 'Transmission Service', 'Engine Diagnostics',
            'Wheel Alignment', 'Detailing', 'Full Inspection', 'AC Service',
            'Coolant Flush', 'Spark Plug Replacement', 'EV Battery Check',
            'Windshield Repair', 'Towing', 'Other',
        ];

        $timePreferences = ['Morning (8am–12pm)', 'Afternoon (12pm–5pm)', 'Evening (5pm–8pm)', 'Any Time'];

        // Open diagnostics grouped by vehicle_id
        $vehicleIds  = $vehicles->pluck('id');
        $diagnostics = ServiceDiagnostic::whereIn('vehicle_id', $vehicleIds)
            ->whereIn('status', ['open', 'acknowledged', 'in_progress'])
            ->with(['vehicle', 'serviceProvider'])
            ->orderByRaw("FIELD(severity,'critical','high','medium','low')")
            ->get()
            ->groupBy('vehicle_id');

        // Providers who have previously diagnosed the consumer's vehicles
        $diagProviderIds = ServiceDiagnostic::whereIn('vehicle_id', $vehicleIds)
            ->whereNotNull('service_provider_id')
            ->distinct()
            ->pluck('service_provider_id');
        $diagnosticProviders = ServiceProvider::whereIn('id', $diagProviderIds)->get();

        // Direct assignment
        $assignedProvider = $request->provider_id
            ? ServiceProvider::find($request->provider_id)
            : null;

        // Recent providers (from accepted offers)
        $recentProviderIds = ServiceJobOffer::whereHas('jobPost',
            fn($q) => $q->where('user_id', $request->user()->id))
            ->where('status', 'accepted')
            ->latest()->limit(3)->pluck('service_provider_id');
        $recentProviders = ServiceProvider::whereIn('id', $recentProviderIds)->get();

        return view('jobs.create', compact(
            'vehicles', 'serviceTypes', 'timePreferences',
            'diagnostics', 'diagnosticProviders',
            'assignedProvider', 'recentProviders'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id'                 => 'required|exists:vehicles,id',
            'service_type'               => 'required|string|max:255',
            'description'                => 'required|string|max:2000',
            'budget_min'                 => 'nullable|numeric|min:0',
            'budget_max'                 => 'nullable|numeric|min:0',
            'preferred_date'             => 'nullable|date|after:today',
            'preferred_time'             => 'nullable|string|max:100',
            'latitude'                   => 'required|numeric',
            'longitude'                  => 'required|numeric',
            'location_address'           => 'nullable|string|max:500',
            'radius'                     => 'nullable|integer|min:1|max:100',
            'customer_notes'             => 'nullable|string|max:1000',
            'assigned_provider_id'       => 'nullable|exists:service_providers,id',
            'preferred_provider_id'      => 'nullable|exists:service_providers,id',
            'attached_diagnostic_ids'    => 'nullable|array',
            'attached_diagnostic_ids.*'  => 'integer|exists:service_diagnostics,id',
            'media.*'                    => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,mkv|max:51200',
        ]);

        $vehicle = Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        // Verify attached diagnostics belong to this consumer's vehicle
        if (!empty($validated['attached_diagnostic_ids'])) {
            $vehicleIds = $request->user()->vehicles()->pluck('id');
            $validated['attached_diagnostic_ids'] = ServiceDiagnostic::whereIn('id', $validated['attached_diagnostic_ids'])
                ->whereIn('vehicle_id', $vehicleIds)
                ->pluck('id')
                ->toArray();
        }

        // Handle media uploads
        $mediaFiles = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('job-media', 'public');
                $mediaFiles[] = [
                    'path' => $path,
                    'url'  => Storage::disk('public')->url($path),
                    'type' => str_starts_with($file->getMimeType(), 'video') ? 'video' : 'image',
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ];
            }
        }

        $job = ServiceJobPost::create([
            ...$validated,
            'user_id'                  => $request->user()->id,
            'radius'                   => $validated['radius'] ?? 25,
            'status'                   => 'open',
            'expires_at'               => now()->addHours(24),
            'media'                    => !empty($mediaFiles) ? $mediaFiles : null,
            'attached_diagnostic_ids'  => !empty($validated['attached_diagnostic_ids'])
                                            ? $validated['attached_diagnostic_ids'] : null,
            'preferred_provider_id'    => $validated['preferred_provider_id'] ?? null,
        ]);

        // Notify preferred provider first if selected
        if (!empty($validated['preferred_provider_id'])) {
            JobNotificationService::notifyPreferredProvider($job);
        }

        // Notify all nearby providers
        if ($job->assigned_provider_id) {
            JobNotificationService::notifyAssignedProvider($job);
        } else {
            JobNotificationService::notifyNearbyProviders($job);
            broadcast(new \App\Events\NewJobPosted($job));
        }

        $request->user()->alerts()->create([
            'user_id'    => $request->user()->id,
            'vehicle_id' => $vehicle->id,
            'type'       => 'booking',
            'title'      => 'Job Posted',
            'message'    => "Your {$validated['service_type']} job has been posted. Providers near you will send their offers shortly.",
            'priority'   => 'info',
            'for_provider' => false,
        ]);

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Job posted! Nearby providers will send you their offers shortly.');
    }

    public function show(ServiceJobPost $job)
    {
        abort_unless($job->user_id === auth()->id(), 403);

        $job->load(['vehicle', 'offers.serviceProvider', 'acceptedOffer.serviceProvider', 'preferredProvider']);

        if ($job->latitude && $job->longitude) {
            foreach ($job->offers as $offer) {
                $prov = $offer->serviceProvider;
                if ($prov && $prov->latitude && $prov->longitude) {
                    $offer->distance = $this->haversine(
                        $job->latitude, $job->longitude,
                        $prov->latitude, $prov->longitude
                    );
                } else {
                    $offer->distance = null;
                }
            }
        }

        $pendingOffers  = $job->offers->where('status', 'pending')->sortBy('offered_price');
        $acceptedOffer  = $job->offers->where('status', 'accepted')->first();
        $rejectedOffers = $job->offers->where('status', 'rejected');

        // Load attached diagnostics
        $attachedDiagnostics = !empty($job->attached_diagnostic_ids)
            ? ServiceDiagnostic::whereIn('id', $job->attached_diagnostic_ids)->with('serviceProvider')->get()
            : collect();

        return view('jobs.show', compact('job', 'pendingOffers', 'acceptedOffer', 'rejectedOffers', 'attachedDiagnostics'));
    }

    public function acceptOffer(Request $request, ServiceJobPost $job, ServiceJobOffer $offer)
    {
        abort_unless($job->user_id === auth()->id(), 403);
        abort_unless($offer->job_post_id === $job->id, 403);

        if (!$job->isOpen()) {
            return back()->with('error', 'This job is no longer open for offers.');
        }

        DB::transaction(function () use ($job, $offer) {
            $offer->update(['status' => 'accepted']);
            $job->offers()->where('id', '!=', $offer->id)->where('status', 'pending')->update(['status' => 'rejected']);
            $job->update(['status' => 'accepted', 'accepted_offer_id' => $offer->id]);
        });

        JobNotificationService::offerAccepted($offer->load(['jobPost.user', 'serviceProvider']));

        foreach ($job->offers->where('status', 'rejected') as $rej) {
            JobNotificationService::offerRejected($rej->load(['jobPost', 'serviceProvider']));
        }

        auth()->user()->alerts()->create([
            'user_id'      => auth()->id(),
            'vehicle_id'   => $job->vehicle_id,
            'type'         => 'booking',
            'title'        => 'Offer Accepted',
            'message'      => "You accepted {$offer->serviceProvider->name}'s offer for {$job->service_type}.",
            'priority'     => 'info',
            'for_provider' => false,
        ]);

        return redirect()->route('jobs.show', $job)
            ->with('success', "Offer accepted! {$offer->serviceProvider->name} will be in touch shortly.");
    }

    public function cancel(ServiceJobPost $job)
    {
        abort_unless($job->user_id === auth()->id(), 403);

        if (!in_array($job->status, ['open', 'accepted'])) {
            return back()->with('error', 'Cannot cancel this job.');
        }

        $job->update(['status' => 'cancelled']);

        foreach ($job->offers()->where('status', 'pending')->with('serviceProvider')->get() as $offer) {
            JobNotificationService::jobCancelled($offer->load(['jobPost', 'serviceProvider']));
            $offer->update(['status' => 'rejected']);
        }

        return redirect()->route('jobs.index')->with('success', 'Job post cancelled.');
    }

    public function complete(ServiceJobPost $job)
    {
        abort_unless($job->user_id === auth()->id(), 403);

        if ($job->status !== 'accepted') {
            return back()->with('error', 'Only accepted jobs can be marked as completed.');
        }

        $job->update(['status' => 'completed']);

        return redirect()->route('jobs.show', $job)->with('success', 'Job marked as completed!');
    }

    public function rate(Request $request, ServiceJobPost $job)
    {
        abort_unless($job->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $job->update($validated);

        return back()->with('success', 'Review submitted! Thank you.');
    }

    // ── API: get diagnostics for a vehicle (called by create form JS) ─────────
    public function vehicleDiagnostics(Request $request)
    {
        $vehicleId = $request->vehicle_id;

        abort_unless(
            Vehicle::where('id', $vehicleId)->where('user_id', auth()->id())->exists(),
            403
        );

        $diagnostics = ServiceDiagnostic::where('vehicle_id', $vehicleId)
            ->whereIn('status', ['open', 'acknowledged', 'in_progress'])
            ->with('serviceProvider')
            ->orderByRaw("FIELD(severity,'critical','high','medium','low')")
            ->get()
            ->map(fn($d) => [
                'id'                  => $d->id,
                'title'               => $d->title,
                'category'            => $d->category,
                'severity'            => $d->severity,
                'is_safety_critical'  => $d->is_safety_critical,
                'description'         => \Str::limit($d->description, 100),
                'estimated_cost_min'  => $d->estimated_cost_min,
                'estimated_cost_max'  => $d->estimated_cost_max,
                'provider_id'         => $d->service_provider_id,
                'provider_name'       => $d->serviceProvider?->business_name ?? $d->serviceProvider?->name,
            ]);

        $providers = $diagnostics->pluck('provider_id', 'provider_name')
            ->filter()
            ->map(fn($id, $name) => ['id' => $id, 'name' => $name])
            ->values();

        return response()->json([
            'diagnostics' => $diagnostics,
            'providers'   => ServiceDiagnostic::where('vehicle_id', $vehicleId)
                ->whereNotNull('service_provider_id')
                ->with('serviceProvider')
                ->get()
                ->pluck('serviceProvider')
                ->filter()
                ->unique('id')
                ->map(fn($p) => ['id' => $p->id, 'name' => $p->business_name ?? $p->name])
                ->values(),
        ]);
    }

    private function haversine($lat1, $lng1, $lat2, $lng2): float
    {
        $earthRadius = 3959;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return round($earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a)), 1);
    }
}