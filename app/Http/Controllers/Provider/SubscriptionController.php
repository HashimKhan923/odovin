<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\ProviderSubscription;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
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
            return back()->with('error', 'Could not start checkout: ' . $e->getMessage());
        }
    }

    // GET /provider/subscription/success?session_id=xxx
    // Stripe redirects here after payment. We activate the plan directly
    // from the Checkout Session — this is the PRIMARY activation path.
    // The webhook handles edge cases (delayed redirect, browser closed, etc).

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if ($sessionId) {
            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                $session = Session::retrieve([
                    'id'     => $sessionId,
                    'expand' => ['subscription', 'subscription.items.data.price'],
                ]);

                if ($session->payment_status === 'paid' || $session->status === 'complete') {
                    $this->service->handleCheckoutCompleted($session);
                    Log::info('[Subscription] Activated via success redirect', [
                        'session' => $sessionId,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('[Subscription] Success activation failed', [
                    'session' => $sessionId,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        return redirect()->route('provider.subscription.index')
            ->with('success', '🎉 Subscription activated! Your new plan is now live.');
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