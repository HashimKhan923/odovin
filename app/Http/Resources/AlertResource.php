<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AlertResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'vehicle_id' => $this->vehicle_id,
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'priority' => $this->priority,
            'is_read' => $this->is_read,
            'read_at' => $this->read_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            
            'vehicle' => new VehicleResource($this->whenLoaded('vehicle')),
        ];
    }
}