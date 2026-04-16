<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobEscrow extends Model
{
    protected $fillable = [
        'job_post_id',
        'stripe_payment_intent_id',
        'stripe_transfer_id',
        'amount',
        'platform_fee',
        'currency',
        'status',
        'held_at',
        'release_at',
        'released_at',
        'refunded_at',
    ];

    protected $casts = [
        'amount'       => 'integer',
        'platform_fee' => 'integer',
        'held_at'      => 'datetime',
        'release_at'   => 'datetime',
        'released_at'  => 'datetime',
        'refunded_at'  => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(ServiceJobPost::class, 'job_post_id');
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    /** Amount the provider receives after platform fee, in cents */
    public function providerAmount(): int
    {
        return $this->amount - $this->platform_fee;
    }

    /** Consumer-facing formatted total */
    public function formattedAmount(): string
    {
        return '$' . number_format($this->amount / 100, 2);
    }

    public function isOverdue(): bool
    {
        return $this->status === 'held'
            && $this->release_at
            && $this->release_at->isPast();
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeOverdue($query)
    {
        return $query->where('status', 'held')
                     ->where('release_at', '<=', now());
    }
}