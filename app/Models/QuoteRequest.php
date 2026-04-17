<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class QuoteRequest extends Model
{
    protected $fillable = [
        'user_id', 'service_provider_id', 'vehicle_id',
        'reference', 'service_type', 'description',
        'preferred_date', 'preferred_time',
        'budget_min', 'budget_max', 'urgency', 'status',
        'quoted_price', 'provider_message', 'estimated_duration',
        'responded_at', 'consumer_action', 'consumer_action_at',
        'converted_job_id', 'expires_at',
    ];

    protected $casts = [
        'budget_min'         => 'decimal:2',
        'budget_max'         => 'decimal:2',
        'quoted_price'       => 'decimal:2',
        'responded_at'       => 'datetime',
        'consumer_action_at' => 'datetime',
        'expires_at'         => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($q) {
            $q->reference = 'QR-' . strtoupper(Str::random(8));
            $q->expires_at = $q->expires_at ?? now()->addDays(\App\Models\AppSetting::int('quote_expiry_days', 7));
        });
    }

    // ── Relationships ──────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class, 'service_provider_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function convertedJob(): BelongsTo
    {
        return $this->belongsTo(ServiceJobPost::class, 'converted_job_id');
    }

    // ── State helpers ─────────────────────────────────────────────

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isQuoted(): bool    { return $this->status === 'quoted'; }
    public function isDeclined(): bool  { return $this->status === 'declined'; }
    public function isExpired(): bool   { return $this->status === 'expired' || ($this->expires_at && $this->expires_at->isPast() && $this->isPending()); }
    public function isConverted(): bool { return !is_null($this->converted_job_id); }

    public function consumerAccepted(): bool
    {
        return $this->consumer_action === 'accepted';
    }

    public function budgetLabel(): string
    {
        if ($this->budget_min && $this->budget_max) {
            return '$' . number_format($this->budget_min, 0) . '–$' . number_format($this->budget_max, 0);
        }
        if ($this->budget_min) return 'From $' . number_format($this->budget_min, 0);
        if ($this->budget_max) return 'Up to $' . number_format($this->budget_max, 0);
        return 'Flexible';
    }

    public function urgencyLabel(): string
    {
        return match($this->urgency) {
            'today'     => '🔴 Today',
            'this_week' => '🟡 This Week',
            default     => '🟢 Flexible',
        };
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'pending'  => 'Awaiting Response',
            'quoted'   => 'Quote Received',
            'declined' => 'Declined',
            'expired'  => 'Expired',
            default    => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'pending'  => '#ffaa00',
            'quoted'   => '#00d4ff',
            'declined' => '#ff8099',
            'expired'  => '#666',
            default    => '#aaa',
        };
    }
}