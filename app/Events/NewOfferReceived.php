<?php

namespace App\Events;

use App\Models\ServiceJobOffer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOfferReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ServiceJobOffer $offer) {}

    public function broadcastOn(): array
    {
        $consumerId = $this->offer->jobPost->user_id;
        return [
            // Private channel for the specific job page
            new PrivateChannel('job.' . $this->offer->job_post_id),
            // Private channel for the consumer's global badge (any dashboard page)
            new PrivateChannel('user.' . $consumerId),
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
            ],
        ];
    }
}