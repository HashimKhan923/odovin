<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\JobEscrow;
use App\Services\EscrowService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private EscrowService $escrow) {}

    // GET /provider/payments
    public function index(Request $request)
    {
        $provider = $request->user()->serviceProvider;
        abort_unless($provider, 404);

        // All escrows released to this provider via job posts they worked on
        $transactions = JobEscrow::whereHas('jobPost', function ($q) use ($provider) {
                $q->where('assigned_provider_id', $provider->id);
            })
            ->where('status', 'released')
            ->with('jobPost.vehicle')
            ->latest('released_at')
            ->get();

        // Pending (held) escrows — money waiting to be released
        $pending = JobEscrow::whereHas('jobPost', function ($q) use ($provider) {
                $q->where('assigned_provider_id', $provider->id);
            })
            ->where('status', 'held')
            ->with('jobPost.vehicle')
            ->latest('held_at')
            ->get();

        $totalEarned  = $transactions->sum('amount') - $transactions->sum('platform_fee');
        $totalPending = $pending->sum('amount') - $pending->sum('platform_fee');

        return view('provider.payments.index', compact(
            'provider', 'transactions', 'pending', 'totalEarned', 'totalPending'
        ));
    }

    // GET /provider/payments/onboard
    public function onboard(Request $request)
    {
        $provider = $request->user()->serviceProvider;
        abort_unless($provider, 404);

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $url = $this->escrow->getProviderOnboardingUrl($provider);
            return redirect($url);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return back()->with('error', 'Stripe error: ' . $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Could not start onboarding: ' . $e->getMessage());
        }
    }

    // GET /provider/payments/onboard/return
    public function onboardReturn(Request $request)
    {
        $provider = $request->user()->serviceProvider;

        if ($provider && $provider->stripe_account_id) {
            try {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                $account = \Stripe\Account::retrieve($provider->stripe_account_id);
                $provider->update([
                    'payout_enabled'      => $account->payouts_enabled,
                    'stripe_onboarded_at' => $account->payouts_enabled ? now() : null,
                ]);
            } catch (\Exception $e) {
                // Webhook will sync later
            }
        }

        return redirect()->route('provider.payments.index')
            ->with('success', 'Stripe account connected! You can now receive payments.');
    }
}