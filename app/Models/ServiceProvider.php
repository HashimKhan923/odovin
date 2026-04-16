<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'type', 'phone', 'address', 'city', 'state', 'zip_code',
        'latitude', 'longitude', 'services_offered', 'description',
        'rating', 'total_reviews', 'is_verified', 'is_active', 'working_hours',
        // Stripe Connect (payment)
        'stripe_account_id', 'stripe_onboarded_at', 'payout_enabled',
        // Subscription (denormalised fast-read fields)
        'plan_slug', 'subscription_active',
    ];

    protected $casts = [
        'rating'              => 'decimal:2',
        'is_verified'         => 'boolean',
        'is_active'           => 'boolean',
        'payout_enabled'      => 'boolean',
        'subscription_active' => 'boolean',
        'latitude'            => 'decimal:8',
        'longitude'           => 'decimal:8',
        'services_offered'    => 'array',
        'working_hours'       => 'array',
        'stripe_onboarded_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function serviceRecords(): HasMany
    {
        return $this->hasMany(ServiceRecord::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ServiceBooking::class);
    }

    /** The current active subscription (null = free Basic tier) */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(ProviderSubscription::class, 'service_provider_id')
            ->whereIn('status', ['active', 'trialing'])
            ->latest();
    }

    /** Full billing history */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(ProviderSubscription::class, 'service_provider_id');
    }

    // ── Convenience accessors delegating to the linked User ───────

    public function getNameAttribute(): string
    {
        return $this->user?->name ?? '';
    }

    public function getEmailAttribute(): string
    {
        return $this->user?->email ?? '';
    }

    // ── Subscription helpers ───────────────────────────────────────

    /** Get the current plan, falling back to Basic if none */
    public function currentPlan(): SubscriptionPlan
    {
        if ($this->plan_slug && $this->plan_slug !== 'basic') {
            $plan = SubscriptionPlan::where('slug', $this->plan_slug)->first();
            if ($plan) return $plan;
        }
        return SubscriptionPlan::getBasic();
    }

    public function isProOrAbove(): bool
    {
        return in_array($this->plan_slug, ['pro', 'premium']) && $this->subscription_active;
    }

    public function isPremium(): bool
    {
        return $this->plan_slug === 'premium' && $this->subscription_active;
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Order providers by plan tier for job board priority.
     * Premium > Pro > Basic
     */
    public function scopePriorityOrdered($query)
    {
        return $query->orderByRaw("
            CASE plan_slug
                WHEN 'premium' THEN 1
                WHEN 'pro'     THEN 2
                ELSE                3
            END ASC
        ");
    }

    // ── Other helpers ─────────────────────────────────────────────

    public function updateRating()
    {
        $avgRating    = $this->bookings()->whereNotNull('rating')->avg('rating');
        $totalReviews = $this->bookings()->whereNotNull('rating')->count();
        $this->update(['rating' => $avgRating ?? 0, 'total_reviews' => $totalReviews]);
    }
}