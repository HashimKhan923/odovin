<?php

namespace App\Http\Controllers;

use App\Models\ServiceJobOffer;
use App\Models\ServiceJobPost;
use App\Models\Alert;
use App\Services\JobNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CounterOfferController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────
    // CONSUMER: Send a counter-offer on a pending offer
    // POST /jobs/{job}/offers/{offer}/counter
    // ──────────────────────────────────────────────────────────────────────

    public function store(Request $request, ServiceJobPost $job, ServiceJobOffer $offer)
    {
        abort_unless($job->user_id === auth()->id(), 403);
        abort_unless($offer->job_post_id === $job->id, 403);
        abort_unless($job->isOpen(), 403);
        abort_unless($offer->status === 'pending', 403);
        abort_unless(in_array($offer->negotiation_status, ['pending', 'counter_rejected']), 403);

        $validated = $request->validate([
            'counter_price'   => 'required|numeric|min:1|max:99999',
            'counter_message' => 'nullable|string|max:500',
        ]);

        $offer->update([
            'counter_price'      => $validated['counter_price'],
            'counter_message'    => $validated['counter_message'] ?? null,
            'countered_at'       => now(),
            'negotiation_status' => 'countered',
        ]);

        // Notify provider
        $provider = $offer->serviceProvider;
        Alert::create([
            'user_id'      => $provider->user_id,
            'vehicle_id'   => $job->vehicle_id,
            'type'         => 'booking',
            'title'        => '💬 Counter-Offer Received',
            'message'      => "The customer countered your \${$offer->offered_price} offer for {$job->service_type} with \${$validated['counter_price']}.",
            'action_url'   => route('provider.jobs.my-offers'),
            'priority'     => 'warning',
            'for_provider' => true,
        ]);

        return back()->with('success', 'Counter-offer sent! The provider will review it shortly.');
    }

    // ──────────────────────────────────────────────────────────────────────
    // CONSUMER: Withdraw/cancel their counter (go back to original)
    // DELETE /jobs/{job}/offers/{offer}/counter
    // ──────────────────────────────────────────────────────────────────────

    public function destroy(ServiceJobPost $job, ServiceJobOffer $offer)
    {
        abort_unless($job->user_id === auth()->id(), 403);
        abort_unless($offer->job_post_id === $job->id, 403);
        abort_unless($offer->negotiation_status === 'countered', 403);

        $offer->update([
            'counter_price'      => null,
            'counter_message'    => null,
            'countered_at'       => null,
            'negotiation_status' => 'pending',
        ]);

        return back()->with('success', 'Counter-offer withdrawn.');
    }

   
}