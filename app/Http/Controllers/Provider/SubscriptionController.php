<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

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
    // Redirects to Stripe Checkout
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

    // GET /provider/subscription/success
    // Stripe redirects here after successful checkout
    public function success(Request $request)
    {
        return redirect()->route('provider.subscription.index')
            ->with('success', '🎉 Subscription activated! Your new plan is now live.');
    }

    // GET /provider/subscription/billing-portal
    // Redirects to Stripe's hosted billing portal (cancel, update card, download invoices)
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