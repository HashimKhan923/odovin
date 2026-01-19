<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Insurance extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'provider',
        'policy_number',
        'coverage_type',
        'start_date',
        'end_date',
        'premium_amount',
        'payment_frequency',
        'deductible',
        'coverage_limits',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'premium_amount' => 'decimal:2',
        'deductible' => 'decimal:2',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function isExpiringSoon($days = 30): bool
    {
        return $this->end_date->between(now(), now()->addDays($days));
    }

    public function isExpired(): bool
    {
        return $this->end_date->isPast();
    }

    public function getMonthlyPremiumAttribute(): float
    {
        return match($this->payment_frequency) {
            'monthly' => $this->premium_amount,
            'quarterly' => $this->premium_amount / 3,
            'semi-annual' => $this->premium_amount / 6,
            'annual' => $this->premium_amount / 12,
            default => 0,
        };
    }
}