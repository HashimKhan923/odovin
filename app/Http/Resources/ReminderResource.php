<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReminderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'vehicle_id' => $this->vehicle_id,
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date->format('Y-m-d'),
            'reminder_date' => $this->reminder_date->format('Y-m-d'),
            'priority' => $this->priority,
            'is_sent' => $this->is_sent,
            'is_completed' => $this->is_completed,
            'created_at' => $this->created_at->toISOString(),
            
            'vehicle' => new VehicleResource($this->whenLoaded('vehicle')),
        ];
    }
}