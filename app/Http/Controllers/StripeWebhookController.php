<?php

namespace App\Http\Controllers;

use App\Models\JobEscrow;
use App\Models\ServiceProvider;
use App\Services\EscrowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $secret = config('services.stripe.webhook_secret');

        // ── In test/dev mode without a real webhook secret, skip signature check ──
        if (empty($secret) || str_starts_with($secret, 'whsec_placeholder')) {
            Log::warning('[Webhook] No webhook secret configured — skipping signature verification. Set up a real secret before going live.');

            try {
                $payload = json_decode($request->getContent(), true);
                $event   = \Stripe\Event::constructFrom($payload);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid payload'], 400);
            }

        } else {
            // ── Production: verify Stripe signature ──────────────────────────────
            $payload   = $request->getContent();
            $sigHeader = $request->header('Stripe-Signature');

            try {
                $event = Webhook::constructEvent($payload, $sigHeader, $secret);
            } catch (SignatureVerificationException | \UnexpectedValueException $e) {
                Log::warning('[Webhook] Bad request', ['error' => $e->getMessage()]);
                return response()->json(['error' => 'Invalid request'], 400);
            }
        }

        Log::info('[Webhook] Event received', ['type' => $event->type]);

        try {
            match ($event->type) {
                'payment_intent.succeeded'      => $this->handlePaymentSucceeded($event->data->object),
                'payment_intent.payment_failed' => $this->handlePaymentFailed($event->data->object),
                'account.updated'               => $this->handleAccountUpdated($event->data->object),
                default                         => null,
            };
        } catch (\Throwable $e) {
            Log::error('[Webhook] Handler error', ['type' => $event->type, 'error' => $e->getMessage()]);
        }

        return response()->json(['received' => true]);
    }

    private function handlePaymentSucceeded(object $intent): void
    {
        $escrow = JobEscrow::where('stripe_payment_intent_id', $intent->id)->first();
        if (!$escrow || $escrow->status === 'held') return;

        app(EscrowService::class)->markHeld($intent->id);
    }

    private function handlePaymentFailed(object $intent): void
    {
        $escrow = JobEscrow::where('stripe_payment_intent_id', $intent->id)->first();
        if (!$escrow) return;

        $escrow->delete();

        $jobId = $intent->metadata->job_id ?? null;
        $job   = $jobId ? \App\Models\ServiceJobPost::find($jobId) : null;

        if ($job) {
            $job->update(['payment_status' => 'unpaid']);

            $job->user->alerts()->create([
                'user_id'      => $job->user_id,
                'vehicle_id'   => $job->vehicle_id,
                'type'         => 'booking',
                'title'        => 'Payment Failed',
                'message'      => "Payment for job #{$job->job_number} failed. Please try again with a different card.",
                'priority'     => 'danger',
                'for_provider' => false,
            ]);
        }
    }

    private function handleAccountUpdated(object $account): void
    {
        $provider = ServiceProvider::where('stripe_account_id', $account->id)->first();
        if (!$provider) return;

        $payoutsEnabled = $account->payouts_enabled ?? false;

        $provider->update([
            'payout_enabled'      => $payoutsEnabled,
            'stripe_onboarded_at' => $payoutsEnabled && !$provider->stripe_onboarded_at
                                        ? now()
                                        : $provider->stripe_onboarded_at,
        ]);
    }
}