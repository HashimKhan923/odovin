<?php

namespace App\Services;

use App\Models\JobEscrow;
use App\Models\ServiceJobOffer;
use App\Models\ServiceJobPost;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Transfer;

class EscrowService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    // ──────────────────────────────────────────────────────────────────────
    // Resolve the platform fee % for a given provider based on their plan
    // Falls back to Basic (12%) if no active subscription found
    // ──────────────────────────────────────────────────────────────────────

    private function platformFeePct(ServiceJobPost $job): float
    {
        $provider = $job->acceptedOffer?->serviceProvider
            ?? $job->assignedProvider;

        if (!$provider) {
            return SubscriptionPlan::getBasic()->platform_fee_pct / 100;
        }

        $subscription = \App\Models\ProviderSubscription::where('service_provider_id', $provider->id)
            ->whereIn('status', ['active', 'trialing'])
            ->with('plan')
            ->latest()
            ->first();

        $pct = $subscription?->plan?->platform_fee_pct
            ?? SubscriptionPlan::getBasic()->platform_fee_pct;

        return (float) $pct / 100;
    }

    // ──────────────────────────────────────────────────────────────────────
    // STEP 1 — Consumer accepts offer → create PaymentIntent + escrow row
    // ──────────────────────────────────────────────────────────────────────

    public function createPaymentIntent(ServiceJobPost $job, ServiceJobOffer $offer): array
    {
        $amountCents  = (int) round($offer->offered_price * 100);
        $feePct       = $this->platformFeePct($job);
        $feeCents     = (int) round($amountCents * $feePct);
        $consumer     = $job->user;

        $stripeCustomerId = $this->ensureStripeCustomer($consumer);

        $intent = PaymentIntent::create([
            'amount'             => $amountCents,
            'currency'           => config('services.stripe.currency', 'usd'),
            'customer'           => $stripeCustomerId,
            'setup_future_usage' => 'off_session',
            'description'        => "Odovin job #{$job->job_number} — {$job->service_type}",
            'metadata'           => [
                'job_id'   => $job->id,
                'offer_id' => $offer->id,
                'user_id'  => $consumer->id,
            ],
        ]);

        JobEscrow::create([
            'job_post_id'              => $job->id,
            'stripe_payment_intent_id' => $intent->id,
            'amount'                   => $amountCents,
            'platform_fee'             => $feeCents,
            'currency'                 => $intent->currency,
            'status'                   => 'pending',
            'release_at'               => null,
        ]);

        Log::info('[Escrow] PaymentIntent created', [
            'job'         => $job->id,
            'intent'      => $intent->id,
            'amount'      => $amountCents,
            'fee_cents'   => $feeCents,
            'fee_pct'     => ($feePct * 100) . '%',
        ]);

        return ['client_secret' => $intent->client_secret];
    }

    // ──────────────────────────────────────────────────────────────────────
    // STEP 2 — payment_intent.succeeded webhook → escrow = held
    // ──────────────────────────────────────────────────────────────────────

    public function markHeld(string $paymentIntentId): void
    {
        $escrow = JobEscrow::where('stripe_payment_intent_id', $paymentIntentId)->firstOrFail();

        if ($escrow->status === 'held') return; // idempotent

        $escrow->update([
            'status'  => 'held',
            'held_at' => now(),
        ]);

        $escrow->jobPost->update([
            'payment_status' => 'held',
            'status'         => 'accepted',
            'work_status'    => 'pending', // immediately shows in Work Queue badge
        ]);

        Log::info('[Escrow] Funds held', ['escrow' => $escrow->id, 'job' => $escrow->job_post_id]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // STEP 3 — Provider marks work complete → start 72h auto-release clock
    // ──────────────────────────────────────────────────────────────────────

    public function startReleaseWindow(ServiceJobPost $job): void
    {
        $escrow = $job->escrow;
        if (!$escrow || $escrow->status !== 'held') return;

        $escrow->update(['release_at' => now()->addHours(\App\Models\AppSetting::int('escrow_auto_release_hours', 72))]);

        Log::info('[Escrow] Release window started', ['escrow' => $escrow->id]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // STEP 4 — Release funds to provider
    // ──────────────────────────────────────────────────────────────────────

    public function release(JobEscrow $escrow): void
    {
        if ($escrow->status !== 'held') {
            throw new \Exception("Cannot release escrow #{$escrow->id} — status is {$escrow->status}");
        }

        $job      = $escrow->jobPost;
        $provider = $job->assignedProvider
            ?? $job->acceptedOffer?->serviceProvider;

        if (!$provider || !$provider->stripe_account_id) {
            throw new \Exception("Provider hasn't connected a Stripe account. Cannot release funds.");
        }

        $transfer = Transfer::create([
            'amount'      => $escrow->providerAmount(),
            'currency'    => $escrow->currency,
            'destination' => $provider->stripe_account_id,
            'description' => "Payout for Odovin job #{$job->job_number}",
            'metadata'    => [
                'job_id'    => $escrow->job_post_id,
                'escrow_id' => $escrow->id,
            ],
        ]);

        $escrow->update([
            'status'             => 'released',
            'stripe_transfer_id' => $transfer->id,
            'released_at'        => now(),
        ]);

        $job->update([
            'payment_status' => 'released',
            'status'         => 'completed',
        ]);

        Log::info('[Escrow] Released', [
            'escrow'   => $escrow->id,
            'transfer' => $transfer->id,
            'amount'   => $escrow->providerAmount(),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // REFUND
    // ──────────────────────────────────────────────────────────────────────

    public function refund(JobEscrow $escrow, string $reason = 'requested_by_customer'): void
    {
        if (!in_array($escrow->status, ['held', 'pending'])) {
            throw new \Exception("Cannot refund escrow #{$escrow->id} — status is {$escrow->status}");
        }

        \Stripe\Refund::create([
            'payment_intent' => $escrow->stripe_payment_intent_id,
            'reason'         => $reason,
        ]);

        $escrow->update(['status' => 'refunded', 'refunded_at' => now()]);
        $escrow->jobPost->update(['payment_status' => 'refunded', 'status' => 'cancelled']);

        Log::info('[Escrow] Refunded', ['escrow' => $escrow->id]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // Provider Stripe Connect onboarding
    // ──────────────────────────────────────────────────────────────────────

    public function getProviderOnboardingUrl(\App\Models\ServiceProvider $provider): string
    {
        if (!$provider->stripe_account_id) {
            $account = \Stripe\Account::create([
                'type'         => 'express',
                'country'      => 'US',
                'email'        => $provider->email,
                'capabilities' => ['transfers' => ['requested' => true]],
                'metadata'     => ['provider_id' => $provider->id],
            ]);
            $provider->update(['stripe_account_id' => $account->id]);
        }

        $link = \Stripe\AccountLink::create([
            'account'     => $provider->stripe_account_id,
            'refresh_url' => route('provider.payments.onboard'),
            'return_url'  => route('provider.payments.onboard.return'),
            'type'        => 'account_onboarding',
        ]);

        return $link->url;
    }

    // ──────────────────────────────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────────────────────────────

    private function ensureStripeCustomer(User $user): string
    {
        if ($user->stripe_customer_id) return $user->stripe_customer_id;

        $customer = \Stripe\Customer::create([
            'email'    => $user->email,
            'name'     => $user->name,
            'metadata' => ['user_id' => $user->id],
        ]);

        $user->update(['stripe_customer_id' => $customer->id]);

        return $customer->id;
    }
}