<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;

/**
 * Gate on the provider's submitOffer route.
 * Add to routes/web.php on the submit-offer POST route.
 */
class CheckBidLimit
{
    public function handle(Request $request, Closure $next)
    {
        $provider = $request->user()->serviceProvider;
        if (!$provider) return $next($request);

        $subscription = app(SubscriptionService::class)->getOrCreateSubscription($provider);

        if (!$subscription->canBid()) {
            $remaining = $subscription->bidsRemaining();
            $planName  = $subscription->plan->name;

            if ($request->expectsJson()) {
                return response()->json([
                    'error'   => "Bid limit reached for your {$planName} plan.",
                    'upgrade' => route('provider.subscription.index'),
                ], 403);
            }

            return back()->with('error',
                "You've reached your monthly bid limit on the {$planName} plan. " .
                '<a href="' . route('provider.subscription.index') . '" style="text-decoration:underline;">Upgrade to Pro or Premium</a> for unlimited bids.'
            );
        }

        // Increment usage after the offer is stored (done in controller)
        $request->attributes->set('subscription', $subscription);

        return $next($request);
    }
}