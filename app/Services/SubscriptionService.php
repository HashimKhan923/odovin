<?php

namespace App\Services;

use App\Models\ProviderSubscription;
use App\Models\ServiceProvider;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Log;
use Stripe\BillingPortal\Session as BillingPortalSession;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;
use Stripe\Subscription;

class SubscriptionService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    // ──────────────────────────────────────────────────────────────────────
    // Get or auto-create a Basic subscription for any provider
    // ──────────────────────────────────────────────────────────────────────

    public function getOrCreateSubscription(ServiceProvider $provider): ProviderSubscription
    {
        $sub = ProviderSubscription::where('service_provider_id', $provider->id)
            ->whereIn('status', ['active', 'trialing', 'past_due'])
            ->latest()
            ->first();

        if ($sub) return $sub;

        $basic = SubscriptionPlan::getBasic();

        return ProviderSubscription::create([
            'service_provider_id' => $provider->id,
            'plan_id'             => $basic->id,
            'billing_interval'    => 'monthly',
            'status'              => 'active',
            'bids_reset_at'       => now()->addMonth(),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // Create Stripe Checkout Session
    // Auto-provisions Stripe product/price if not set yet
    // ──────────────────────────────────────────────────────────────────────

    public function createCheckoutSession(
        ServiceProvider $provider,
        SubscriptionPlan $plan,
        string $interval = 'monthly'
    ): string {
        $priceId = $interval === 'yearly'
            ? $plan->stripe_yearly_price_id
            : $plan->stripe_monthly_price_id;

        if (empty($priceId) || str_starts_with($priceId, 'price_REPLACE')) {
            $priceId = $this->provisionStripePrice($plan, $interval);
        }

        $customerId = $this->ensureStripeCustomer($provider);

        $session = \Stripe\Checkout\Session::create([
            'customer'               => $customerId,
            'mode'                   => 'subscription',
            'line_items'             => [[
                'price'    => $priceId,
                'quantity' => 1,
            ]],
            'success_url'            => route('provider.subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'             => route('provider.subscription.index'),
            'subscription_data'      => [
                'metadata' => [
                    'provider_id' => $provider->id,
                    'plan_slug'   => $plan->slug,
                ],
            ],
            'allow_promotion_codes'      => true,
            'billing_address_collection' => 'auto',
        ]);

        Log::info('[Subscription] Checkout session created', [
            'provider' => $provider->id,
            'plan'     => $plan->slug,
            'interval' => $interval,
            'price_id' => $priceId,
        ]);

        return $session->url;
    }

    // ──────────────────────────────────────────────────────────────────────
    // Auto-provision: create Stripe product + price, save IDs to DB
    // ──────────────────────────────────────────────────────────────────────

    public function provisionStripePrice(SubscriptionPlan $plan, string $interval): string
    {
        $product = $this->ensureStripeProduct($plan);

        $stripeInterval = $interval === 'yearly' ? 'year' : 'month';
        $amount         = $interval === 'yearly' ? $plan->price_yearly : $plan->price_monthly;

        $price = Price::create([
            'product'     => $product->id,
            'unit_amount' => $amount,
            'currency'    => config('services.stripe.currency', 'usd'),
            'recurring'   => ['interval' => $stripeInterval],
            'metadata'    => [
                'plan_slug' => $plan->slug,
                'interval'  => $interval,
            ],
        ]);

        // Persist price ID so we never create duplicates
        if ($interval === 'yearly') {
            $plan->update(['stripe_yearly_price_id' => $price->id]);
        } else {
            $plan->update(['stripe_monthly_price_id' => $price->id]);
        }

        Log::info('[Subscription] Stripe price provisioned', [
            'plan'       => $plan->slug,
            'interval'   => $interval,
            'price_id'   => $price->id,
            'product_id' => $product->id,
        ]);

        return $price->id;
    }

    // ──────────────────────────────────────────────────────────────────────
    // Find or create the Stripe Product for a plan.
    //
    // Strategy (most reliable → least reliable):
    //   1. Use plan->stripe_product_id if already stored (fastest, no API call)
    //   2. List products and match by metadata["plan_slug"] (correct query syntax)
    //   3. Create a new product as fallback
    //
    // We store the product ID on the plan after creation so step 1 always
    // wins on subsequent calls — no search needed.
    // ──────────────────────────────────────────────────────────────────────

    private function ensureStripeProduct(SubscriptionPlan $plan): Product
    {
        // ── Step 1: already have the product ID stored ─────────────────
        if (!empty($plan->stripe_product_id)) {
            try {
                $product = Product::retrieve($plan->stripe_product_id);
                if ($product && !$product->deleted) {
                    return $product;
                }
            } catch (\Exception $e) {
                // Product was deleted in Stripe, fall through to create a new one
                Log::warning('[Subscription] Stored product ID invalid, recreating', [
                    'plan'       => $plan->slug,
                    'product_id' => $plan->stripe_product_id,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        // ── Step 2: search Stripe for existing product by metadata ──────
        // Correct Stripe search query syntax: metadata["key"]:"value"
        try {
            $results = Product::search([
                'query' => 'metadata["plan_slug"]:"' . $plan->slug . '"',
            ]);

            if (!empty($results->data)) {
                $product = $results->data[0];
                // Cache the product ID so we skip search next time
                $plan->update(['stripe_product_id' => $product->id]);
                return $product;
            }
        } catch (\Exception $e) {
            // Search API not available or query failed — fall through to create
            Log::warning('[Subscription] Stripe product search failed, creating new product', [
                'plan'  => $plan->slug,
                'error' => $e->getMessage(),
            ]);
        }

        // ── Step 3: create a fresh product ─────────────────────────────
        $product = Product::create([
            'name'        => 'Odovin ' . $plan->name,
            'description' => $plan->description,
            'metadata'    => ['plan_slug' => $plan->slug],
        ]);

        // Always persist so we never hit this path twice for the same plan
        $plan->update(['stripe_product_id' => $product->id]);

        Log::info('[Subscription] Stripe product created', [
            'plan'       => $plan->slug,
            'product_id' => $product->id,
        ]);

        return $product;
    }

    // ──────────────────────────────────────────────────────────────────────
    // Billing Portal
    // ──────────────────────────────────────────────────────────────────────

    public function getBillingPortalUrl(ServiceProvider $provider): string
    {
        $customerId = $this->ensureStripeCustomer($provider);

        $session = BillingPortalSession::create([
            'customer'   => $customerId,
            'return_url' => route('provider.subscription.index'),
        ]);

        return $session->url;
    }

    // ──────────────────────────────────────────────────────────────────────
    // Webhook: checkout.session.completed
    // ──────────────────────────────────────────────────────────────────────

    public function handleCheckoutCompleted(object $session): void
    {
        if ($session->mode !== 'subscription') return;

        // $session->subscription may be a full object (when expanded) or just an ID string
        if (is_string($session->subscription)) {
            $stripeSubscription = Subscription::retrieve($session->subscription);
        } else {
            // Already expanded — retrieve fresh to ensure latest data
            $stripeSubscription = Subscription::retrieve($session->subscription->id);
        }

        $metadata = $stripeSubscription->metadata;
        $providerId         = $metadata->provider_id ?? null;
        $planSlug           = $metadata->plan_slug   ?? null;

        if (!$providerId || !$planSlug) {
            Log::warning('[Subscription] Missing metadata on checkout', ['session' => $session->id]);
            return;
        }

        $provider = ServiceProvider::find($providerId);
        $plan     = SubscriptionPlan::where('slug', $planSlug)->first();
        if (!$provider || !$plan) return;

        // Cancel any existing active subscription
        ProviderSubscription::where('service_provider_id', $providerId)
            ->whereIn('status', ['active', 'trialing'])
            ->update(['status' => 'canceled', 'canceled_at' => now()]);

        $billingInterval = $stripeSubscription->items->data[0]->plan->interval ?? 'month';

        ProviderSubscription::create([
            'service_provider_id'    => $providerId,
            'plan_id'                => $plan->id,
            'stripe_subscription_id' => $stripeSubscription->id,
            'stripe_customer_id'     => $session->customer,
            'billing_interval'       => $billingInterval === 'year' ? 'yearly' : 'monthly',
            'status'                 => $stripeSubscription->status,
            'current_period_start'   => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_start),
            'current_period_end'     => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end),
            'bids_reset_at'          => now()->addMonth(),
        ]);

        $provider->update([
            'plan_slug'           => $planSlug,
            'subscription_active' => true,
        ]);

        Log::info('[Subscription] Activated', ['provider' => $providerId, 'plan' => $planSlug]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // Webhook: customer.subscription.updated / deleted
    // ──────────────────────────────────────────────────────────────────────

    public function handleSubscriptionUpdated(object $stripeSubscription): void
    {
        $sub = ProviderSubscription::where('stripe_subscription_id', $stripeSubscription->id)->first();
        if (!$sub) return;

        $sub->update([
            'status'               => $stripeSubscription->status,
            'current_period_start' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_start),
            'current_period_end'   => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end),
            'canceled_at'          => $stripeSubscription->canceled_at
                                        ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->canceled_at)
                                        : null,
            'ends_at'              => $stripeSubscription->cancel_at
                                        ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->cancel_at)
                                        : null,
        ]);

        if (in_array($stripeSubscription->status, ['canceled', 'incomplete_expired'])) {
            $sub->provider->update([
                'plan_slug'           => 'basic',
                'subscription_active' => false,
            ]);
        }
    }

    // ──────────────────────────────────────────────────────────────────────
    // Webhook: invoice.paid
    // ──────────────────────────────────────────────────────────────────────

    public function handleInvoicePaid(object $invoice): void
    {
        $sub = ProviderSubscription::where('stripe_subscription_id', $invoice->subscription)->first();
        if (!$sub) return;

        SubscriptionInvoice::updateOrCreate(
            ['stripe_invoice_id' => $invoice->id],
            [
                'service_provider_id' => $sub->service_provider_id,
                'plan_id'             => $sub->plan_id,
                'amount'              => $invoice->amount_paid,
                'currency'            => $invoice->currency,
                'status'              => 'paid',
                'hosted_invoice_url'  => $invoice->hosted_invoice_url,
                'paid_at'             => \Carbon\Carbon::createFromTimestamp(
                    $invoice->status_transitions->paid_at ?? now()->timestamp
                ),
            ]
        );
    }

    // ──────────────────────────────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────────────────────────────

    private function ensureStripeCustomer(ServiceProvider $provider): string
    {
        $existing = ProviderSubscription::where('service_provider_id', $provider->id)
            ->whereNotNull('stripe_customer_id')
            ->value('stripe_customer_id');

        if ($existing) return $existing;

        $customer = \Stripe\Customer::create([
            'email'    => $provider->user?->email ?? '',
            'name'     => $provider->business_name ?? $provider->name,
            'metadata' => ['provider_id' => $provider->id],
        ]);

        return $customer->id;
    }
}