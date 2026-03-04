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

        // Base query: open, not expired
        $query = ServiceJobPost::open()
            ->with(['user', 'vehicle', 'offers'])
            ->whereDoesntHave('offers', function ($q) use ($provider) {
                // Hide jobs the provider already offered on (they use myOffers for that)
                $q->where('service_provider_id', $provider->id)
                  ->whereIn('status', ['accepted', 'rejected']);
            });

        // Optional: filter by service type
        $query->when($request->service_type, fn($q, $t) => $q->where('service_type', $t));

        // Distance-based filtering if provider has coordinates
        if ($provider->latitude && $provider->longitude) {
            $lat = $provider->latitude;
            $lng = $provider->longitude;

            $query->selectRaw("
                service_job_posts.*,
                (3959 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )) AS distance
            ", [$lat, $lng, $lat]);

            $maxRadius = $request->radius ?? 50;
            $query->havingRaw('distance <= ?', [$maxRadius])
                  ->orderBy('distance', 'asc');
        } else {
            $query->select('service_job_posts.*')->latest();
        }

        $jobs = $query->paginate(15)->withQueryString();

        // Tag each job with whether this provider already submitted an offer
        $myOfferJobIds = ServiceJobOffer::where('service_provider_id', $provider->id)
            ->pluck('job_post_id')
            ->toArray();

        $serviceTypes = ServiceJobPost::open()->distinct('service_type')->pluck('service_type');

        return view('provider.jobs.index', compact('provider', 'jobs', 'myOfferJobIds', 'serviceTypes'));
    }

    // ── Provider: view a specific job post ───────────────────────────────

    public function show(ServiceJobPost $job)
    {
        $provider = $this->provider();

        // Only show open jobs unless provider already offered
        $myOffer = ServiceJobOffer::where('job_post_id', $job->id)
            ->where('service_provider_id', $provider->id)
            ->first();

        abort_unless($job->isOpen() || $myOffer, 404);

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

        // Check duplicate
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

        // Alert the consumer
        JobNotificationService::newOffer($offer->load(['jobPost.user', 'serviceProvider']));
        // Broadcast to consumer's private channel (WebSocket)
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