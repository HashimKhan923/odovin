<?php

namespace App\Http\Controllers;

use App\Models\ServiceJobPost;
use App\Models\ServiceJobOffer;
use App\Models\ServiceProvider;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\JobNotificationService;
use App\Events\NewJobPosted;

class JobPostController extends Controller
{
    // ── Consumer: list own job posts ──────────────────────────────────────

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

    // ── Consumer: create form ─────────────────────────────────────────────

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

        // Pre-selected provider (coming from provider profile page)
        $assignedProvider = $request->provider_id
            ? ServiceProvider::find($request->provider_id)
            : null;

        // Recent providers this user has worked with (from completed jobs)
        $recentProviders = ServiceJobPost::where('user_id', $request->user()->id)
            ->where('work_status', 'completed')
            ->with('acceptedOffer.serviceProvider')
            ->latest()
            ->get()
            ->pluck('acceptedOffer.serviceProvider')
            ->filter()
            ->unique('id')
            ->take(5);

        return view('jobs.create', compact(
            'vehicles', 'serviceTypes', 'timePreferences',
            'assignedProvider', 'recentProviders'
        ));
    }

    // ── Consumer: store new job post ──────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id'           => 'required|exists:vehicles,id',
            'service_type'         => 'required|string|max:255',
            'description'          => 'required|string|max:2000',
            'budget_min'           => 'nullable|numeric|min:0',
            'budget_max'           => 'nullable|numeric|min:0',
            'preferred_date'       => 'nullable|date|after:today',
            'preferred_time'       => 'nullable|string|max:100',
            'latitude'             => 'required|numeric',
            'longitude'            => 'required|numeric',
            'location_address'     => 'nullable|string|max:500',
            'radius'               => 'nullable|integer|min:5|max:100',
            'customer_notes'       => 'nullable|string|max:1000',
            'assigned_provider_id' => 'nullable|exists:service_providers,id',
        ]);

        // Make sure vehicle belongs to this user
        $vehicle = Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $job = ServiceJobPost::create([
            ...$validated,
            'user_id'    => $request->user()->id,
            'radius'     => $validated['radius'] ?? 25,
            'status'     => 'open',
            'expires_at' => now()->addHours(24),
        ]);

        // ── Notify providers ─────────────────────────────────────────────
        if ($job->assigned_provider_id) {
            // Direct assignment — only notify that one provider
            JobNotificationService::notifyAssignedProvider($job);
        } else {
            // Open job — notify all nearby providers
            JobNotificationService::notifyNearbyProviders($job);
            // Broadcast to all providers listening on job-board channel (WebSocket)
            broadcast(new NewJobPosted($job));
        }

        $request->user()->alerts()->create([
            'user_id'    => $request->user()->id,
            'vehicle_id' => $vehicle->id,
            'type'       => 'booking',
            'title'      => 'Job Posted',
            'message'    => "Your {$validated['service_type']} job has been posted. Providers near you will send their offers shortly.",
            'priority'   => 'info',
        ]);

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Job posted! Nearby providers will send you their offers shortly.');
    }

    // ── Consumer: view job + all offers ──────────────────────────────────

    public function show(ServiceJobPost $job)
    {
        abort_unless($job->user_id === auth()->id(), 403);

        $job->load(['vehicle', 'offers.serviceProvider', 'acceptedOffer.serviceProvider']);

        // Attach distance to each offer's provider
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

        return view('jobs.show', compact('job', 'pendingOffers', 'acceptedOffer', 'rejectedOffers'));
    }

    // ── Consumer: accept an offer ─────────────────────────────────────────

    public function acceptOffer(Request $request, ServiceJobPost $job, ServiceJobOffer $offer)
    {
        abort_unless($job->user_id === auth()->id(), 403);
        abort_unless($offer->job_post_id === $job->id, 403);

        if (!$job->isOpen()) {
            return back()->with('error', 'This job is no longer open for offers.');
        }

        DB::transaction(function () use ($job, $offer) {
            // Accept the chosen offer
            $offer->update(['status' => 'accepted']);

            // Reject all other offers
            $job->offers()
                ->where('id', '!=', $offer->id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);

            // Update job status
            $job->update([
                'status'           => 'accepted',
                'accepted_offer_id'=> $offer->id,
            ]);
        });

        // Notify winning provider
        JobNotificationService::offerAccepted($offer->load(['jobPost.user', 'serviceProvider']));

        // Notify rejected providers
        foreach ($job->offers->where('status', 'rejected') as $rej) {
            JobNotificationService::offerRejected($rej->load(['jobPost', 'serviceProvider']));
        }

        foreach ($job->offers->where('status', 'rejected') as $rej) {
        }

        // Alert the consumer
        auth()->user()->alerts()->create([
            'user_id'    => auth()->id(),
            'vehicle_id' => $job->vehicle_id,
            'type'       => 'booking',
            'title'      => 'Offer Accepted',
            'message'    => "You accepted {$offer->serviceProvider->name}'s offer for {$job->service_type}.",
            'priority'   => 'info',
        ]);

        return redirect()->route('jobs.show', $job)
            ->with('success', "Offer accepted! {$offer->serviceProvider->name} will be in touch shortly.");
    }

    // ── Consumer: cancel job post ─────────────────────────────────────────

    public function cancel(ServiceJobPost $job)
    {
        abort_unless($job->user_id === auth()->id(), 403);

        if (!in_array($job->status, ['open', 'accepted'])) {
            return back()->with('error', 'Cannot cancel this job.');
        }

        $job->update(['status' => 'cancelled']);

        // Notify any pending providers
        foreach ($job->offers()->where('status', 'pending')->with('serviceProvider')->get() as $offer) {
            JobNotificationService::jobCancelled($offer->load(['jobPost', 'serviceProvider']));
            $offer->update(['status' => 'rejected']);
        }

        return redirect()->route('jobs.index')->with('success', 'Job post cancelled.');
    }

    // ── Consumer: mark job completed ─────────────────────────────────────

    /**
     * Consumer confirms the job is done (optional — provider marks complete too).
     */
    public function complete(ServiceJobPost $job)
    {
        abort_unless($job->user_id === auth()->id(), 403);

        if ($job->work_status !== 'completed') {
            return back()->with('error', 'Job must be completed by the provider first.');
        }

        return redirect()->route('jobs.show', $job);
    }

    /**
     * Consumer rates a completed job (mirrors BookingController::rate).
     */
    public function rate(Request $request, ServiceJobPost $job)
    {
        abort_unless($job->user_id === auth()->id(), 403);

        if ($job->work_status !== 'completed') {
            return back()->with('error', 'Can only rate completed jobs.');
        }
        if (!is_null($job->rating)) {
            return back()->with('error', 'You have already rated this job.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $job->update($validated);

        // Update provider's overall rating
        $provider = $job->acceptedOffer?->serviceProvider;
        if ($provider) {
            // Merge rating from bookings + job posts
            $bookingAvg = $provider->bookings()->whereNotNull('rating')->avg('rating') ?? 0;
            $bookingCnt = $provider->bookings()->whereNotNull('rating')->count();
            $jobAvg = ServiceJobPost::whereHas('offers', fn($q) =>
                $q->where('service_provider_id', $provider->id)->where('status','accepted'))
                ->whereNotNull('rating')->avg('rating') ?? 0;
            $jobCnt = ServiceJobPost::whereHas('offers', fn($q) =>
                $q->where('service_provider_id', $provider->id)->where('status','accepted'))
                ->whereNotNull('rating')->count();
            $total = $bookingCnt + $jobCnt;
            $avg   = $total > 0 ? (($bookingAvg * $bookingCnt) + ($jobAvg * $jobCnt)) / $total : 0;
            $provider->update(['rating' => round($avg, 2), 'total_reviews' => $total]);
        }

        // Notify provider about the new review
        JobNotificationService::jobReviewSubmitted($job->load(['user', 'acceptedOffer.serviceProvider']));

        return back()->with('success', 'Thank you for your review!');
    }

    // ── Private: Haversine distance (miles) ───────────────────────────────

    private function haversine($lat1, $lng1, $lat2, $lng2): float
    {
        $earthRadius = 3959; // miles
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return round($earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a)), 1);
    }
}