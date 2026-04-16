<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Run: php artisan migrate
// This seeds the 3 default plans automatically as part of migration.
// Change prices / Stripe price IDs here once you create them in Stripe Dashboard.

return new class extends Migration
{
    public function up(): void
    {
        DB::table('subscription_plans')->insert([
            [
                'slug'                  => 'basic',
                'name'                  => 'Basic',
                'description'           => 'Get started. Browse jobs and submit up to 10 bids per month.',
                'price_monthly'         => 0,       // Free
                'price_yearly'          => 0,
                'stripe_monthly_price_id' => null,
                'stripe_yearly_price_id'  => null,
                'job_bids_per_month'    => 10,
                'platform_fee_pct'      => 12.00,   // higher fee on free tier
                'featured_profile'      => false,
                'priority_in_job_board' => false,
                'analytics_advanced'    => false,
                'badge_verified_boost'  => false,
                'radius_boost_km'       => 0,
                'is_active'             => true,
                'sort_order'            => 1,
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            [
                'slug'                  => 'pro',
                'name'                  => 'Pro',
                'description'           => 'Grow your business. Unlimited bids, lower fees, priority listing.',
                'price_monthly'         => 2999,    // $29.99 / month
                'price_yearly'          => 28799,   // $287.99 / year (~20% off)
                'stripe_monthly_price_id' => 'price_REPLACE_PRO_MONTHLY',
                'stripe_yearly_price_id'  => 'price_REPLACE_PRO_YEARLY',
                'job_bids_per_month'    => -1,       // unlimited
                'platform_fee_pct'      => 8.00,    // reduced fee
                'featured_profile'      => false,
                'priority_in_job_board' => true,    // shown before Basic providers
                'analytics_advanced'    => true,
                'badge_verified_boost'  => false,
                'radius_boost_km'       => 10,      // see jobs 10km further
                'is_active'             => true,
                'sort_order'            => 2,
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            [
                'slug'                  => 'premium',
                'name'                  => 'Premium',
                'description'           => 'Maximum visibility. Lowest fees, featured profile, verified badge boost.',
                'price_monthly'         => 5999,    // $59.99 / month
                'price_yearly'          => 57599,   // $575.99 / year (~20% off)
                'stripe_monthly_price_id' => 'price_REPLACE_PREMIUM_MONTHLY',
                'stripe_yearly_price_id'  => 'price_REPLACE_PREMIUM_YEARLY',
                'job_bids_per_month'    => -1,       // unlimited
                'platform_fee_pct'      => 5.00,    // lowest fee
                'featured_profile'      => true,    // pinned at top of provider directory
                'priority_in_job_board' => true,
                'analytics_advanced'    => true,
                'badge_verified_boost'  => true,    // premium badge on profile
                'radius_boost_km'       => 25,      // see jobs 25km further
                'is_active'             => true,
                'sort_order'            => 3,
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('subscription_plans')->whereIn('slug', ['basic', 'pro', 'premium'])->delete();
    }
};