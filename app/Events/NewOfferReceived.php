<?php

namespace App\Events;

use App\Models\ServiceJobOffer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a provider submits an offer on a job.
 * Broadcasts on a PRIVATE channel for the job owner (consumer) only.
 */
class NewOfferReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ServiceJobOffer $offer)
    {
        //
    }

    public function broadcastOn(): array
    {
        // Private channel scoped to the job owner's user ID
        return [
            new PrivateChannel('job.' . $this->offer->jobPost->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new-offer';
    }

    public function broadcastWith(): array
    {
        $provider = $this->offer->serviceProvider;

        return [
            'offer_id'           => $this->offer->id,
            'job_id'             => $this->offer->job_post_id,
            'offered_price'      => $this->offer->offered_price,
            'available_date'     => $this->offer->available_date,
            'available_time'     => $this->offer->available_time,
            'estimated_duration' => $this->offer->estimated_duration,
            'message'            => $this->offer->message,
            'status'             => $this->offer->status,
            'created_at'         => $this->offer->created_at->toIso8601String(),
            'provider' => [
                'id'            => $provider->id,
                'name'          => $provider->name,
                'type'          => $provider->type,
                'is_verified'   => $provider->is_verified,
                'rating'        => $provider->rating,
                'total_reviews' => $provider->total_reviews,
                'city'          => $provider->city,
                'state'         => $provider->state,
                'latitude'      => $provider->latitude,
                'longitude'     => $provider->longitude,
            ],
        ];
    }
}