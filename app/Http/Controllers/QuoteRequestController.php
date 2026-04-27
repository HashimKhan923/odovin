<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\QuoteRequest;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;

class QuoteRequestController extends Controller
{
    // ── GET /providers/{provider}/quote ───────────────────────────

    public function create(Request $request, ServiceProvider $provider)
    {
        return $request;
        abort_unless($provider->is_active, 404);

        $vehicles     = auth()->user()->vehicles()->active()->get();
        $serviceTypes = [
            'Oil Change', 'Tire Rotation', 'Brake Service', 'Engine Repair',
            'Transmission Service', 'AC Service', 'Battery Replacement',
            'Suspension & Steering', 'Wheel Alignment', 'Detailing',
            'Body Work', 'Electrical', 'Diagnostic', 'Other',
        ];

        $preselectedService = $request->query('service');

        return view('quotes.create', compact('provider', 'vehicles', 'serviceTypes', 'preselectedService'));
    }

    // ── POST /providers/{provider}/quote ──────────────────────────

    public function store(Request $request, ServiceProvider $provider)
    {
        abort_unless($provider->is_active, 404);

        $validated = $request->validate([
            'vehicle_id'     => 'nullable|exists:vehicles,id',
            'service_type'   => 'required|string|max:255',
            'description'    => 'required|string|min:20|max:2000',
            'preferred_date' => 'nullable|date|after:today',
            'preferred_time' => 'nullable|string|max:50',
            'budget_min'     => 'nullable|numeric|min:0',
            'budget_max'     => 'nullable|numeric|min:0|gte:budget_min',
            'urgency'        => 'required|in:flexible,this_week,today',
        ]);

        $existing = QuoteRequest::where('user_id', auth()->id())
            ->where('service_provider_id', $provider->id)
            ->where('status', 'pending')
            ->where('service_type', $validated['service_type'])
            ->where('created_at', '>=', now()->subDays(\App\Models\AppSetting::int('quote_duplicate_block_days', 3)))
            ->first();

        if ($existing) {
            return back()->with('error',
                "You already have a pending quote request for {$validated['service_type']} with this provider. " .
                '<a href="' . route('quotes.index') . '" style="text-decoration:underline;">View your requests →</a>'
            );
        }

        $quote = QuoteRequest::create([
            ...$validated,
            'user_id'             => auth()->id(),
            'service_provider_id' => $provider->id,
        ]);

        Alert::create([
            'user_id'      => $provider->user_id,
            'type'         => 'booking',
            'title'        => '📋 New Quote Request',
            'message'      => auth()->user()->name . " is requesting a quote for {$quote->service_type}. " . $quote->urgencyLabel(),
            'action_url'   => route('provider.quotes.show', $quote),
            'priority'     => $quote->urgency === 'today' ? 'warning' : 'info',
            'for_provider' => true,
        ]);

        return redirect()->route('quotes.show', $quote)
            ->with('success', "Quote request sent to {$provider->name}! They'll respond within 24 hours.");
    }

    // ── GET /quotes ───────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = QuoteRequest::where('user_id', auth()->id())
            ->with(['provider', 'vehicle'])
            ->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $quotes = $query->paginate(15)->withQueryString();

        $stats = [
            'pending' => QuoteRequest::where('user_id', auth()->id())->where('status', 'pending')->count(),
            'quoted'  => QuoteRequest::where('user_id', auth()->id())->where('status', 'quoted')->count(),
            'total'   => QuoteRequest::where('user_id', auth()->id())->count(),
        ];

        return view('quotes.index', compact('quotes', 'stats'));
    }

    // ── GET /quotes/{quote} ───────────────────────────────────────

    public function show(QuoteRequest $quote)
    {
        abort_unless($quote->user_id === auth()->id(), 403);
        $quote->load(['provider', 'vehicle', 'convertedJob']);
        return view('quotes.show', compact('quote'));
    }

    // ── POST /quotes/{quote}/accept ───────────────────────────────
    // Accepts the quote and converts it to a job post.
    //
    // FIX: The job now uses the provider's own location as a fallback
    // so it appears in their Browse Open Jobs list. It also uses a very
    // small radius (1 mile) so only this assigned provider sees it.
    // The assigned_provider_id ensures the provider can always find it
    // in their Work Queue even if Browse filters it out.

    public function accept(QuoteRequest $quote)
    {
        return $quote;
        abort_unless($quote->user_id === auth()->id(), 403);
        abort_unless($quote->isQuoted(), 403);
        abort_unless(is_null($quote->consumer_action), 403);

        $provider = $quote->provider;

        // Use provider's location so the job appears within their radius.
        // Radius set to 1 km — only this provider will ever see it in Browse.
        $lat    = $provider->latitude;
        $lng    = $provider->longitude;
        $radius = 1;

        $job = \App\Models\ServiceJobPost::create([
            'user_id'               => auth()->id(),
            'vehicle_id'            => $quote->vehicle_id,
            'service_type'          => $quote->service_type,
            'description'           => $quote->description,
            'preferred_date'        => $quote->preferred_date,
            'preferred_time'        => $quote->preferred_time,
            'budget_min'            => $quote->quoted_price,
            'budget_max'            => $quote->quoted_price,
            'preferred_provider_id' => $provider->id,
            'assigned_provider_id'  => $provider->id,
            'latitude'              => $lat,
            'longitude'             => $lng,
            'radius'                => $radius,
            'location_address'      => $provider->address . ', ' . $provider->city . ', ' . $provider->state,
            'status'                => 'open',
        ]);

        $quote->update([
            'consumer_action'    => 'accepted',
            'consumer_action_at' => now(),
            'converted_job_id'   => $job->id,
        ]);

        // Toast notification for provider
        Alert::create([
            'user_id'      => $provider->user_id,
            'type'         => 'booking',
            'title'        => '✅ Quote Accepted — Job Posted!',
            'message'      => auth()->user()->name . " accepted your quote of \${$quote->quoted_price} for {$quote->service_type}. The job is now live.",
            'action_url'   => route('provider.jobs.show', $job),
            'priority'     => 'info',
            'for_provider' => true,
        ]);

        \App\Services\JobNotificationService::notifyAssignedProvider($job);

        return redirect()->route('jobs.show', $job)
            ->with('success', "Quote accepted! Your job has been posted and {$provider->name} has been notified.");
    }

    // ── POST /quotes/{quote}/decline ─────────────────────────────

    public function decline(QuoteRequest $quote)
    {
        abort_unless($quote->user_id === auth()->id(), 403);
        abort_unless($quote->isQuoted(), 403);
        abort_unless(is_null($quote->consumer_action), 403);

        $quote->update([
            'consumer_action'    => 'declined',
            'consumer_action_at' => now(),
        ]);

        Alert::create([
            'user_id'      => $quote->provider->user_id,
            'type'         => 'booking',
            'title'        => '↩ Quote Declined',
            'message'      => auth()->user()->name . " declined your quote for {$quote->service_type}.",
            'action_url'   => route('provider.quotes.index'),
            'priority'     => 'info',
            'for_provider' => true,
        ]);

        return back()->with('info', 'Quote declined. You can browse other providers or post a job to all nearby providers.');
    }

    // ── DELETE /quotes/{quote} ────────────────────────────────────

    public function destroy(QuoteRequest $quote)
    {
        abort_unless($quote->user_id === auth()->id(), 403);
        abort_unless($quote->isPending(), 403);

        $quote->update(['status' => 'expired']);

        return redirect()->route('quotes.index')
            ->with('success', 'Quote request withdrawn.');
    }
}