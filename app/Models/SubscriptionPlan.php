<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'slug', 'name', 'description',
        'price_monthly', 'price_yearly',
        'stripe_monthly_price_id', 'stripe_yearly_price_id',
        'job_bids_per_month', 'platform_fee_pct',
        'featured_profile', 'priority_in_job_board',
        'analytics_advanced', 'badge_verified_boost',
        'radius_boost_km', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'featured_profile'      => 'boolean',
        'priority_in_job_board' => 'boolean',
        'analytics_advanced'    => 'boolean',
        'badge_verified_boost'  => 'boolean',
        'is_active'             => 'boolean',
        'platform_fee_pct'      => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function subscriptions(): HasMany
    {
        return $this->hasMany(ProviderSubscription::class, 'plan_id');
    }

    // ── Helpers ────────────────────────────────────────────────────

    public function formattedMonthlyPrice(): string
    {
        if ($this->price_monthly === 0) return 'Free';
        return '$' . number_format($this->price_monthly / 100, 2);
    }

    public function formattedYearlyPrice(): string
    {
        if ($this->price_yearly === 0) return 'Free';
        return '$' . number_format($this->price_yearly / 100, 2);
    }

    public function monthlyEquivalentYearly(): string
    {
        if ($this->price_yearly === 0) return 'Free';
        return '$' . number_format($this->price_yearly / 100 / 12, 2);
    }

    public function yearlyDiscountPct(): int
    {
        if (!$this->price_monthly || !$this->price_yearly) return 0;
        $annualIfMonthly = $this->price_monthly * 12;
        return (int) round((1 - $this->price_yearly / $annualIfMonthly) * 100);
    }

    public function hasUnlimitedBids(): bool
    {
        return $this->job_bids_per_month === -1;
    }

    public function isFree(): bool
    {
        return $this->price_monthly === 0;
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public static function getBasic(): self
    {
        return static::where('slug', 'basic')->firstOrFail();
    }
}