<?php

namespace App\Http\Controllers;

use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class SubscriptionWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.subscription_webhook_secret');

        // Verify signature (skip in dev if secret is placeholder)
        if (!empty($secret) && !str_starts_with($secret, 'whsec_placeholder')) {
            try {
                $event = Webhook::constructEvent($payload, $sigHeader, $secret);
            } catch (SignatureVerificationException|\UnexpectedValueException $e) {
                Log::warning('[SubWebhook] Invalid signature', ['error' => $e->getMessage()]);
                return response()->json(['error' => 'Invalid request'], 400);
            }
        } else {
            try {
                $event = \Stripe\Event::constructFrom(json_decode($payload, true));
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid payload'], 400);
            }
        }

        Log::info('[SubWebhook] Event', ['type' => $event->type]);

        $service = app(SubscriptionService::class);

        try {
            match ($event->type) {
                'checkout.session.completed'      => $service->handleCheckoutCompleted($event->data->object),
                'customer.subscription.updated'   => $service->handleSubscriptionUpdated($event->data->object),
                'customer.subscription.deleted'   => $service->handleSubscriptionUpdated($event->data->object),
                'invoice.paid'                    => $service->handleInvoicePaid($event->data->object),
                default                           => null,
            };
        } catch (\Throwable $e) {
            Log::error('[SubWebhook] Error', ['type' => $event->type, 'error' => $e->getMessage()]);
        }

        return response()->json(['received' => true]);
    }
}