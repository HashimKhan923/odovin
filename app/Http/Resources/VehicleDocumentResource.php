<?php
// app/Http/Resources/VehicleDocumentResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleDocumentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'file_path' => $this->file_path ? asset('storage/' . $this->file_path) : null,
            'file_type' => $this->file_type,
            'issue_date' => $this->issue_date?->format('Y-m-d'),
            'expiry_date' => $this->expiry_date?->format('Y-m-d'),
            'notes' => $this->notes,
            'is_expiring_soon' => $this->isExpiringSoon(),
            'is_expired' => $this->isExpired(),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
