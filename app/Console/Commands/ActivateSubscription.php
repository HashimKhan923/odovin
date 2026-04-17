<?php

namespace App\Console\Commands;

use App\Models\ProviderSubscription;
use App\Models\SubscriptionPlan;
use App\Models\ServiceProvider;
use Illuminate\Console\Command;
use Stripe\Stripe;
use Stripe\Subscription as StripeSubscription;

class ActivateSubscription extends Command
{
    protected $signature   = 'subscription:activate {provider_id} {plan_slug}';
    protected $description = 'Manually activate a subscription for a provider';

    public function handle()
    {
        $providerId = $this->argument('provider_id');
        $planSlug   = $this->argument('plan_slug');

        $provider = ServiceProvider::find($providerId);
        $plan     = SubscriptionPlan::where('slug', $planSlug)->first();

        if (!$provider) { $this->error("Provider {$providerId} not found"); return; }
        if (!$plan)     { $this->error("Plan {$planSlug} not found"); return; }

        // Cancel ALL existing subscriptions for this provider
        ProviderSubscription::where('service_provider_id', $providerId)
            ->update(['status' => 'canceled', 'canceled_at' => now()]);

        // Find the most recent Stripe subscription ID for this provider
        $stripeSubId = ProviderSubscription::where('service_provider_id', $providerId)
            ->whereNotNull('stripe_subscription_id')
            ->latest()
            ->value('stripe_subscription_id');

        // Create clean active subscription
        ProviderSubscription::create([
            'service_provider_id'    => $providerId,
            'plan_id'                => $plan->id,
            'stripe_subscription_id' => $stripeSubId,
            'billing_interval'       => 'monthly',
            'status'                 => 'active',
            'current_period_start'   => now(),
            'current_period_end'     => now()->addMonth(),
            'bids_reset_at'          => now()->addMonth(),
        ]);

        // Update provider record
        $provider->update([
            'plan_slug'           => $planSlug,
            'subscription_active' => true,
        ]);

        $this->info("✅ Provider {$provider->business_name} activated on {$plan->name} plan.");
    }
}