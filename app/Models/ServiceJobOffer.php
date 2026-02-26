<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceJobOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_post_id', 'service_provider_id', 'offered_price',
        'available_date', 'available_time', 'estimated_duration',
        'message', 'status',
    ];

    protected $casts = [
        'offered_price' => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(ServiceJobPost::class, 'job_post_id');
    }

    public function serviceProvider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class);
    }
}