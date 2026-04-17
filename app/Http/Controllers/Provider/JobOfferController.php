<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\ServiceJobPost;
use App\Models\ServiceJobOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\JobNotificationService;
use App\Events\NewOfferReceived;

class JobOfferController extends Controller
{
    protected function provider()
    {
        return Auth::user()->serviceProvider;
    }

    // ── Provider: browse open nearby job posts ────────────────────────────

    public function index(Request $request)
    {
        $provider = $this->provider();

        // Get provider's plan for radius boost
        $plan        = $provider->currentPlan();
        $radiusBoost = (int) ($plan->radius_boost_km ?? 0);

        // ── Base: open jobs not already accepted/rejected by this provider
        // Only show: unassigned jobs (public) OR jobs assigned directly to me
        $base = ServiceJobPost::open()
            ->with(['user', 'vehicle', 'offers'])
            ->whereDoesntHave('offers', function ($q) use ($provider) {
                $q->where('service_provider_id', $provider->id)
                  ->whereIn('status', ['accepted', 'rejected']);
            })
            ->where(function ($q) use ($provider) {
                $q->whereNull('assigned_provider_id')
                  ->orWhere('assigned_provider_id', $provider->id);
            });

        // Optional service type filter
        $base->when($request->service_type, fn($q, $t) => $q->where('service_type', $t));

        // ── Distance-based query with radius boost ────────────────────────
        if ($provider->latitude && $provider->longitude) {
            $lat       = $provider->latitude;
            $lng       = $provider->longitude;
            $maxRadius = ($request->radius ?? \App\Models\AppSetting::int('default_browse_radius_miles', 50)) + $radiusBoost;

            $nearbyQuery = (clone $base)
                ->selectRaw("
                    service_job_posts.*,
                    (3959 * acos(
                        cos(radians(?)) * cos(radians(latitude)) *
                        cos(radians(longitude) - radians(?)) +
                        sin(radians(?)) * sin(radians(latitude))
                    )) AS distance,
                    CASE WHEN assigned_provider_id = ? THEN 1 ELSE 0 END AS is_direct_request
                ", [$lat, $lng, $lat, $provider->id])
                ->where(function ($q) use ($lat, $lng, $maxRadius, $provider) {
                    $q->whereRaw("
                        (3959 * acos(
                            cos(radians(?)) * cos(radians(latitude)) *
                            cos(radians(longitude) - radians(?)) +
                            sin(radians(?)) * sin(radians(latitude))
                        )) <= ?
                    ", [$lat, $lng, $lat, $maxRadius])
                    ->orWhere('assigned_provider_id', $provider->id);
                })
                // Direct requests first, then by distance
                ->orderByRaw('is_direct_request DESC')
                ->orderByRaw("
                    (3959 * acos(
                        cos(radians(?)) * cos(radians(latitude)) *
                        cos(radians(longitude) - radians(?)) +
                        sin(radians(?)) * sin(radians(latitude))
                    )) ASC
                ", [$lat, $lng, $lat]);

            $jobs = $nearbyQuery->paginate(15)->withQueryString();

        } else {
            $jobs = (clone $base)
                ->selectRaw('service_job_posts.*, CASE WHEN assigned_provider_id = ? THEN 1 ELSE 0 END AS is_direct_request', [$provider->id])
                ->orderByRaw('is_direct_request DESC')
                ->latest()
                ->paginate(15)
                ->withQueryString();
        }

        $myOfferJobIds = ServiceJobOffer::where('service_provider_id', $provider->id)
            ->pluck('job_post_id')
            ->toArray();

        $serviceTypes = ServiceJobPost::open()->distinct('service_type')->pluck('service_type');

        return view('provider.jobs.index', compact('provider', 'jobs', 'myOfferJobIds', 'serviceTypes', 'radiusBoost', 'plan'));
    }

    // ── Provider: view a specific job post ───────────────────────────────

    public function show(ServiceJobPost $job)
    {
        $provider = $this->provider();

        $myOffer = ServiceJobOffer::where('job_post_id', $job->id)
            ->where('service_provider_id', $provider->id)
            ->first();

        $isAssignedToMe = $job->assigned_provider_id === $provider->id;

        abort_unless($job->isOpen() || $myOffer || $isAssignedToMe, 404);

        $job->load(['vehicle', 'user']);

        return view('provider.jobs.show', compact('provider', 'job', 'myOffer'));
    }

    // ── Provider: submit an offer ─────────────────────────────────────────

    public function submitOffer(Request $request, ServiceJobPost $job)
    {
        $provider = $this->provider();

        if (!$job->isOpen()) {
            return back()->with('error', 'This job post is no longer accepting offers.');
        }

        if (ServiceJobOffer::where('job_post_id', $job->id)
                           ->where('service_provider_id', $provider->id)
                           ->exists()) {
            return back()->with('error', 'You have already submitted an offer for this job.');
        }

        $validated = $request->validate([
            'offered_price'      => 'required|numeric|min:1',
            'available_date'     => 'required|date|after:today',
            'available_time'     => 'required|string',
            'estimated_duration' => 'nullable|integer|min:15|max:480',
            'message'            => 'nullable|string|max:1000',
        ]);

        $offer = ServiceJobOffer::create([
            ...$validated,
            'job_post_id'         => $job->id,
            'service_provider_id' => $provider->id,
            'status'              => 'pending',
        ]);

        // Increment bid usage (CheckBidLimit middleware sets subscription on request)
        $subscription = $request->attributes->get('subscription');
        if ($subscription) {
            $subscription->incrementBidUsage();
        }

        JobNotificationService::newOffer($offer->load(['jobPost.user', 'serviceProvider']));
        broadcast(new NewOfferReceived($offer));

        return redirect()->route('provider.jobs.show', $job)
            ->with('success', 'Your offer has been submitted! The customer will review it shortly.');
    }

    // ── Provider: my offers list ──────────────────────────────────────────

    public function myOffers(Request $request)
    {
        $provider = $this->provider();

        $query = ServiceJobOffer::where('service_provider_id', $provider->id)
            ->with(['jobPost.vehicle', 'jobPost.user'])
            ->latest();

        $query->when($request->status, fn($q, $s) => $q->where('status', $s));

        $offers = $query->paginate(15)->withQueryString();

        $stats = [
            'pending'  => ServiceJobOffer::where('service_provider_id', $provider->id)->where('status', 'pending')->count(),
            'accepted' => ServiceJobOffer::where('service_provider_id', $provider->id)->where('status', 'accepted')->count(),
            'rejected' => ServiceJobOffer::where('service_provider_id', $provider->id)->where('status', 'rejected')->count(),
        ];

        return view('provider.jobs.my-offers', compact('provider', 'offers', 'stats'));
    }
}