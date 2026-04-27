<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'service_provider_id',
        'job_post_id',
        'service_type',
        'description',
        'service_date',
        'mileage_at_service',
        'cost',
        'invoice_number',
        'invoice_file',
        'parts_replaced',
        'notes',
        'next_service_mileage',
        'next_service_date',
        'before_photos',
        'after_photos',
        'evidence_notes',
    ];

    protected $casts = [
        'service_date'     => 'date',
        'cost'             => 'decimal:2',
        'parts_replaced'   => 'array',
        'next_service_date'=> 'date',
        'before_photos'    => 'array',
        'after_photos'     => 'array',
    ];

    // ── Photo helpers ──────────────────────────────────────────────

    public function hasEvidence(): bool
    {
        return !empty($this->before_photos) || !empty($this->after_photos);
    }

    public function beforePhotoUrls(): array
    {
        return array_map(
            fn($p) => \Illuminate\Support\Facades\Storage::disk('public')->url($p),
            $this->before_photos ?? []
        );
    }

    public function afterPhotoUrls(): array
    {
        return array_map(
            fn($p) => \Illuminate\Support\Facades\Storage::disk('public')->url($p),
            $this->after_photos ?? []
        );
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function serviceProvider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    public function expense(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Expense::class);
    }

    public function serviceDiagnostics()
    {
        return $this->hasMany(ServiceDiagnostic::class);
    }

}