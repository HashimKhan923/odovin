<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceRecordResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'vehicle_id' => $this->vehicle_id,
            'service_provider_id' => $this->service_provider_id,
            'service_type' => $this->service_type,
            'description' => $this->description,
            'service_date' => $this->service_date->format('Y-m-d'),
            'mileage_at_service' => $this->mileage_at_service,
            'cost' => $this->cost,
            'invoice_number' => $this->invoice_number,
            'invoice_file' => $this->invoice_file ? asset('storage/' . $this->invoice_file) : null,
            'parts_replaced' => $this->parts_replaced,
            'notes' => $this->notes,
            'next_service_mileage' => $this->next_service_mileage,
            'next_service_date' => $this->next_service_date?->format('Y-m-d'),
            'created_at' => $this->created_at->toISOString(),
            
            'vehicle' => new VehicleResource($this->whenLoaded('vehicle')),
            'service_provider' => new ServiceProviderResource($this->whenLoaded('serviceProvider')),
        ];
    }
}