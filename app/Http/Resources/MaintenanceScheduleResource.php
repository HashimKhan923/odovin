<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceScheduleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'vehicle_id' => $this->vehicle_id,
            'service_type' => $this->service_type,
            'description' => $this->description,
            'due_mileage' => $this->due_mileage,
            'due_date' => $this->due_date?->format('Y-m-d'),
            'priority' => $this->priority,
            'status' => $this->status,
            'is_recurring' => $this->is_recurring,
            'recurrence_mileage' => $this->recurrence_mileage,
            'recurrence_months' => $this->recurrence_months,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            'vehicle' => new VehicleResource($this->whenLoaded('vehicle')),
        ];
    }
}