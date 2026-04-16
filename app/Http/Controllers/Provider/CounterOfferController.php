<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\ServiceJobOffer;
use Illuminate\Http\Request;

class CounterOfferController extends Controller
{
    private function provider()
    {
        return auth()->user()->serviceProvider;
    }

    // ──────────────────────────────────────────────────────────────────────
    // POST /provider/offers/{offer}/counter/accept
    // Provider accepts the consumer's counter-offer
    // ──────────────────────────────────────────────────────────────────────

    public function accept(ServiceJobOffer $offer)
    {
        $provider = $this->provider();
        abort_unless($offer->service_provider_id === $provider->id, 403);
        abort_unless($offer->negotiation_status === 'countered', 403);

        $counterPrice = $offer->counter_price;

        // Lock in the agreed counter price as the new offered price
        $offer->update([
            'offered_price'      => $counterPrice,
            'negotiation_status' => 'counter_accepted',
        ]);

        $job = $offer->jobPost;

        // Notify the consumer
        Alert::create([
            'user_id'      => $job->user_id,
            'vehicle_id'   => $job->vehicle_id,
            'type'         => 'booking',
            'title'        => '✅ Counter-Offer Accepted!',
            'message'      => "{$provider->business_name} accepted your counter-offer of \${$counterPrice} for {$job->service_type}. You can now confirm the booking.",
            'action_url'   => route('jobs.show', $job),
            'priority'     => 'success',
            'for_provider' => false,
        ]);

        return back()->with('success', "Counter-offer accepted at \${$counterPrice}. The consumer has been notified and can now confirm the booking.");
    }

    // ──────────────────────────────────────────────────────────────────────
    // POST /provider/offers/{offer}/counter/reject
    // Provider declines the counter — original offer price stands
    // ──────────────────────────────────────────────────────────────────────

    public function reject(ServiceJobOffer $offer)
    {
        $provider = $this->provider();
        abort_unless($offer->service_provider_id === $provider->id, 403);
        abort_unless($offer->negotiation_status === 'countered', 403);

        $offer->update([
            'negotiation_status' => 'counter_rejected',
        ]);

        $job = $offer->jobPost;

        // Notify the consumer
        Alert::create([
            'user_id'      => $job->user_id,
            'vehicle_id'   => $job->vehicle_id,
            'type'         => 'booking',
            'title'        => '↩ Counter-Offer Declined',
            'message'      => "{$provider->business_name} declined your counter. Their original offer of \${$offer->offered_price} still stands — you can accept it or send a new counter.",
            'action_url'   => route('jobs.show', $job),
            'priority'     => 'info',
            'for_provider' => false,
        ]);

        return back()->with('info', 'Counter-offer declined. The consumer has been notified — your original offer still stands.');
    }
}