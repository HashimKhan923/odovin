<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProviderSubscription extends Model
{
    protected $fillable = [
        'service_provider_id', 'plan_id',
        'stripe_subscription_id', 'stripe_customer_id',
        'billing_interval', 'status',
        'trial_ends_at', 'current_period_start', 'current_period_end',
        'canceled_at', 'ends_at',
        'bids_used_this_month', 'bids_reset_at',
    ];

    protected $casts = [
        'trial_ends_at'         => 'datetime',
        'current_period_start'  => 'datetime',
        'current_period_end'    => 'datetime',
        'canceled_at'           => 'datetime',
        'ends_at'               => 'datetime',
        'bids_reset_at'         => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function provider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class, 'service_provider_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(SubscriptionInvoice::class, 'service_provider_id', 'service_provider_id');
    }

    // ── State helpers ─────────────────────────────────────────────

    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'trialing'])
            && (!$this->ends_at || $this->ends_at->isFuture());
    }

    public function isTrialing(): bool
    {
        return $this->status === 'trialing'
            && $this->trial_ends_at
            && $this->trial_ends_at->isFuture();
    }

    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }

    public function isPastDue(): bool
    {
        return $this->status === 'past_due';
    }

    /** Days remaining in current billing period */
    public function daysRemaining(): int
    {
        if (!$this->current_period_end) return 0;
        return max(0, (int) now()->diffInDays($this->current_period_end, false));
    }

    // ── Bid limit helpers ─────────────────────────────────────────

    public function canBid(): bool
    {
        if (!$this->isActive()) return false;
        if ($this->plan->hasUnlimitedBids()) return true;

        $this->resetBidsIfNeeded();
        return $this->bids_used_this_month < $this->plan->job_bids_per_month;
    }

    public function bidsRemaining(): int
    {
        if ($this->plan->hasUnlimitedBids()) return PHP_INT_MAX;
        $this->resetBidsIfNeeded();
        return max(0, $this->plan->job_bids_per_month - $this->bids_used_this_month);
    }

    public function incrementBidUsage(): void
    {
        $this->resetBidsIfNeeded();
        $this->increment('bids_used_this_month');
    }

    private bool $bidsResetChecked = false;

    private function resetBidsIfNeeded(): void
    {
        if ($this->bidsResetChecked) return; // already checked this request
        $this->bidsResetChecked = true;

        if (!$this->bids_reset_at || $this->bids_reset_at->isPast()) {
            $this->update([
                'bids_used_this_month' => 0,
                'bids_reset_at'        => now()->addMonth(),
            ]);
            $this->refresh();
        }
    }
}