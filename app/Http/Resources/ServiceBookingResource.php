<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceBookingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'booking_number' => $this->booking_number,
            'vehicle_id' => $this->vehicle_id,
            'service_provider_id' => $this->service_provider_id,
            'service_type' => $this->service_type,
            'description' => $this->description,
            'scheduled_date' => $this->scheduled_date->toISOString(),
            'status' => $this->status,
            'estimated_cost' => $this->estimated_cost,
            'final_cost' => $this->final_cost,
            'customer_notes' => $this->customer_notes,
            'provider_notes' => $this->provider_notes,
            'rating' => $this->rating,
            'review' => $this->review,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            'vehicle' => new VehicleResource($this->whenLoaded('vehicle')),
            'service_provider' => new ServiceProviderResource($this->whenLoaded('serviceProvider')),
        ];
    }
}