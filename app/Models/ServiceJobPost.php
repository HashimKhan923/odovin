<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ServiceJobPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'vehicle_id', 'job_number', 'service_type', 'description',
        'budget_min', 'budget_max', 'preferred_date', 'preferred_time',
        'latitude', 'longitude', 'location_address', 'radius',
        'status', 'accepted_offer_id', 'expires_at', 'customer_notes',
    ];

    protected $casts = [
        'budget_min'  => 'decimal:2',
        'budget_max'  => 'decimal:2',
        'latitude'    => 'decimal:7',
        'longitude'   => 'decimal:7',
        'expires_at'  => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($job) {
            $job->job_number = 'JB' . strtoupper(Str::random(8));
            $job->expires_at = $job->expires_at ?? now()->addHours(24);
        });
    }

    // ── Relationships ──────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(ServiceJobOffer::class, 'job_post_id');
    }

    public function acceptedOffer(): BelongsTo
    {
        return $this->belongsTo(ServiceJobOffer::class, 'accepted_offer_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeOpen($query)
    {
        return $query->where('status', 'open')
                     ->where(function ($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     });
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isOpen(): bool
    {
        return $this->status === 'open' && !$this->isExpired();
    }

    public function budgetLabel(): string
    {
        if ($this->budget_min && $this->budget_max) {
            return '$' . number_format($this->budget_min) . ' – $' . number_format($this->budget_max);
        }
        if ($this->budget_max) {
            return 'Up to $' . number_format($this->budget_max);
        }
        return 'Flexible';
    }
}