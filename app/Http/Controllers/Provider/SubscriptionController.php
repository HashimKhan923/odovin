<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\ProviderSubscription;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Subscription as StripeSubscription;
use Stripe\Stripe;

class SubscriptionController extends Controller
{
    public function __construct(private SubscriptionService $service) {}

    // GET /provider/subscription
    public function index(Request $request)
    {
        $provider     = $request->user()->serviceProvider;
        $plans        = SubscriptionPlan::active()->get();
        $subscription = $this->service->getOrCreateSubscription($provider);
        $invoices     = $subscription->invoices()->latest()->limit(12)->get();

        // Self-heal: sync plan_slug from the actual active subscription
        $bestSub = ProviderSubscription::where('service_provider_id', $provider->id)
            ->whereNotNull('stripe_subscription_id')
            ->where('status', 'active')
            ->latest()
            ->first();

        if ($bestSub && $provider->plan_slug !== $bestSub->plan->slug) {
            $provider->update([
                'plan_slug'           => $bestSub->plan->slug,
                'subscription_active' => true,
            ]);
            $provider->refresh();
            $subscription = $bestSub;
        }

        return view('provider.subscription.index', compact(
            'provider', 'plans', 'subscription', 'invoices'
        ));
    }

    // POST /provider/subscription/checkout
    public function checkout(Request $request)
    {
        $request->validate([
            'plan_slug' => 'required|exists:subscription_plans,slug',
            'interval'  => 'required|in:monthly,yearly',
        ]);

        $provider = $request->user()->serviceProvider;
        $plan     = SubscriptionPlan::where('slug', $request->plan_slug)->firstOrFail();

        if ($plan->isFree()) {
            return back()->with('info', 'You are already on the free Basic plan.');
        }

        try {
            $url = $this->service->createCheckoutSession($provider, $plan, $request->interval);
            return redirect($url);
        } catch (\Exception $e) {
            Log::error('[Subscription] Checkout failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Could not start checkout: ' . $e->getMessage());
        }
    }

    // GET /provider/subscription/success?session_id=xxx
    public function success(Request $request)
    {
        $provider  = $request->user()->serviceProvider;
        $sessionId = $request->query('session_id');

        Log::info('[Subscription] Success page hit', [
            'provider'   => $provider->id,
            'session_id' => $sessionId,
        ]);

        if ($sessionId) {
            try {
                Stripe::setApiKey(config('services.stripe.secret'));

                $session = Session::retrieve($sessionId);

                Log::info('[Subscription] Session retrieved', [
                    'session_id'     => $sessionId,
                    'payment_status' => $session->payment_status,
                    'status'         => $session->status,
                    'subscription'   => $session->subscription,
                    'metadata'       => json_encode($session->metadata),
                ]);

                if (in_array($session->payment_status, ['paid', 'no_payment_required'])
                    || $session->status === 'complete') {

                    // ── Get plan from metadata ─────────────────────────────
                    $planSlug = $session->metadata->plan_slug ?? null;

                    // If no metadata, derive plan from the price ID
                    if (!$planSlug && $session->subscription) {
                        $stripeSub = StripeSubscription::retrieve($session->subscription);
                        $planSlug  = $stripeSub->metadata->plan_slug ?? null;
                    }

                    Log::info('[Subscription] Plan slug resolved', ['plan_slug' => $planSlug]);

                    if ($planSlug) {
                        $plan = SubscriptionPlan::where('slug', $planSlug)->first();

                        if ($plan) {
                            // Cancel all existing non-Stripe subscriptions
                            ProviderSubscription::where('service_provider_id', $provider->id)
                                ->whereNull('stripe_subscription_id')
                                ->update(['status' => 'canceled', 'canceled_at' => now()]);

                            // Cancel previous paid subscriptions
                            ProviderSubscription::where('service_provider_id', $provider->id)
                                ->whereNotNull('stripe_subscription_id')
                                ->where('stripe_subscription_id', '!=', $session->subscription)
                                ->whereIn('status', ['active', 'trialing'])
                                ->update(['status' => 'canceled', 'canceled_at' => now()]);

                            // Upsert the subscription row
                            ProviderSubscription::updateOrCreate(
                                [
                                    'service_provider_id'    => $provider->id,
                                    'stripe_subscription_id' => $session->subscription,
                                ],
                                [
                                    'plan_id'              => $plan->id,
                                    'stripe_customer_id'   => $session->customer,
                                    'billing_interval'     => 'monthly',
                                    'status'               => 'active',
                                    'current_period_start' => now(),
                                    'current_period_end'   => now()->addMonth(),
                                    'bids_reset_at'        => now()->addMonth(),
                                ]
                            );

                            // Update provider record
                            $provider->update([
                                'plan_slug'           => $planSlug,
                                'subscription_active' => true,
                            ]);

                            Log::info('[Subscription] Activated successfully', [
                                'provider' => $provider->id,
                                'plan'     => $planSlug,
                            ]);
                        }
                    }
                }

            } catch (\Exception $e) {
                Log::error('[Subscription] Success activation failed', [
                    'session'   => $sessionId,
                    'error'     => $e->getMessage(),
                    'file'      => $e->getFile(),
                    'line'      => $e->getLine(),
                ]);
            }
        }

        return redirect()->route('provider.subscription.index')
            ->with('success', '🎉 Payment received! Your plan has been upgraded.');
    }

    // GET /provider/subscription/billing-portal
    public function billingPortal(Request $request)
    {
        $provider = $request->user()->serviceProvider;

        try {
            $url = $this->service->getBillingPortalUrl($provider);
            return redirect($url);
        } catch (\Exception $e) {
            return back()->with('error', 'Could not open billing portal: ' . $e->getMessage());
        }
    }
}