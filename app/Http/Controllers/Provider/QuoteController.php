<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    private function provider()
    {
        return auth()->user()->serviceProvider;
    }

    // ── GET /provider/quotes ──────────────────────────────────────
    // Provider: list incoming quote requests

    public function index(Request $request)
    {
        $provider = $this->provider();

        $query = QuoteRequest::where('service_provider_id', $provider->id)
            ->with(['user', 'vehicle'])
            ->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $quotes = $query->paginate(15)->withQueryString();

        $stats = [
            'pending'  => QuoteRequest::where('service_provider_id', $provider->id)->where('status', 'pending')->count(),
            'quoted'   => QuoteRequest::where('service_provider_id', $provider->id)->where('status', 'quoted')->count(),
            'accepted' => QuoteRequest::where('service_provider_id', $provider->id)->where('consumer_action', 'accepted')->count(),
        ];

        return view('provider.quotes.index', compact('provider', 'quotes', 'stats'));
    }

    // ── GET /provider/quotes/{quote} ──────────────────────────────
    // Provider: view a single request

    public function show(QuoteRequest $quote)
    {
        abort_unless($quote->service_provider_id === $this->provider()->id, 403);
        $quote->load(['user', 'vehicle']);
        return view('provider.quotes.show', compact('quote'));
    }

    // ── POST /provider/quotes/{quote}/respond ─────────────────────
    // Provider sends back their price quote

    public function respond(Request $request, QuoteRequest $quote)
    {
        abort_unless($quote->service_provider_id === $this->provider()->id, 403);
        abort_unless($quote->isPending(), 403);

        $validated = $request->validate([
            'quoted_price'       => 'required|numeric|min:1',
            'provider_message'   => 'required|string|min:10|max:1500',
            'estimated_duration' => 'nullable|string|max:100',
        ]);

        

        $quote->update([
            ...$validated,
            'status'       => 'quoted',
            'responded_at' => now(),
        ]);


        // Notify consumer
        Alert::create([
            'user_id'      => $quote->user_id,
            'type'         => 'booking',
            'title'        => '💰 Quote Received!',
            'message'      => "{$this->provider()->name} sent a quote of \${$validated['quoted_price']} for your {$quote->service_type} request.",
            'action_url'   => route('quotes.show', $quote),
            'priority'     => 'info',
            'for_provider' => false,
        ]);

        return redirect()->route('provider.quotes.index')
            ->with('success', 'Quote sent! The customer has been notified.');
    }

    // ── POST /provider/quotes/{quote}/decline ─────────────────────
    // Provider declines the request (too far, not available, etc.)

    public function decline(Request $request, QuoteRequest $quote)
    {
        abort_unless($quote->service_provider_id === $this->provider()->id, 403);
        abort_unless($quote->isPending(), 403);

        $request->validate([
            'decline_reason' => 'nullable|string|max:500',
        ]);

        $quote->update([
            'status'           => 'declined',
            'provider_message' => $request->decline_reason,
            'responded_at'     => now(),
        ]);

        // Notify consumer
        Alert::create([
            'user_id'      => $quote->user_id,
            'type'         => 'booking',
            'title'        => 'Quote Request Declined',
            'message'      => "{$this->provider()->name} is unable to fulfil your {$quote->service_type} request. Try posting to all nearby providers.",
            'action_url'   => route('jobs.create'),
            'priority'     => 'info',
            'for_provider' => false,
        ]);

        return redirect()->route('provider.quotes.index')
            ->with('info', 'Request declined. The customer has been notified.');
    }
}