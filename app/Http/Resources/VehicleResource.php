<?php
// app/Http/Resources/VehicleResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'vin' => $this->vin,
            'make' => $this->make,
            'model' => $this->model,
            'year' => $this->year,
            'full_name' => $this->full_name,
            'trim' => $this->trim,
            'engine' => $this->engine,
            'transmission' => $this->transmission,
            'fuel_type' => $this->fuel_type,
            'color' => $this->color,
            'license_plate' => $this->license_plate,
            'current_mileage' => $this->current_mileage,
            'purchase_date' => $this->purchase_date?->format('Y-m-d'),
            'purchase_price' => $this->purchase_price,
            'specifications' => $this->specifications,
            'is_primary' => $this->is_primary,
            'status' => $this->status,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // Relationships
            'documents' => VehicleDocumentResource::collection($this->whenLoaded('documents')),
            'maintenance_schedules' => MaintenanceScheduleResource::collection($this->whenLoaded('maintenanceSchedules')),
            'service_records' => ServiceRecordResource::collection($this->whenLoaded('serviceRecords')),
            'expenses' => ExpenseResource::collection($this->whenLoaded('expenses')),
            
            // Counts
            'maintenance_schedules_count' => $this->when(isset($this->maintenance_schedules_count), $this->maintenance_schedules_count),
            'service_records_count' => $this->when(isset($this->service_records_count), $this->service_records_count),
            'expenses_count' => $this->when(isset($this->expenses_count), $this->expenses_count),
        ];
    }
}