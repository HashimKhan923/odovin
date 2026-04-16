<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\JobEscrow;
use App\Models\ServiceJobPost;
use App\Services\EscrowService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private EscrowService $escrow) {}

    // GET /jobs/{job}/payment
    public function show(ServiceJobPost $job)
    {
        abort_unless($job->user_id === auth()->id(), 403);

        $escrow = $job->escrow;

        if ($escrow && in_array($escrow->status, ['held', 'released'])) {
            return redirect()->route('jobs.show', $job)
                ->with('info', 'Payment already completed for this job.');
        }

        $acceptedOffer = $job->acceptedOffer;
        abort_unless($acceptedOffer, 404);

        return view('jobs.payment', compact('job', 'acceptedOffer', 'escrow'));
    }

    // POST /jobs/{job}/payment/intent
    public function createIntent(ServiceJobPost $job)
    {
        abort_unless($job->user_id === auth()->id(), 403);

        $offer = $job->acceptedOffer;
        abort_unless($offer, 422);

        if ($job->escrow && $job->escrow->status === 'held') {
            return response()->json(['error' => 'Already paid.'], 422);
        }

        try {
            $data = $this->escrow->createPaymentIntent($job, $offer);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // POST /jobs/{job}/payment/sync
    // Called by JS after Stripe confirms — syncs DB immediately without waiting for webhook
    public function sync(Request $request, ServiceJobPost $job)
    {
        abort_unless($job->user_id === auth()->id(), 403);

        $escrow = $job->escrow;

        if (!$escrow) {
            return response()->json(['status' => 'no_escrow'], 422);
        }

        // Already held — idempotent, but still send notification if not already sent
        if ($escrow->status === 'held') {
            return response()->json(['status' => 'held']);
        }

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $intent = \Stripe\PaymentIntent::retrieve($escrow->stripe_payment_intent_id);

            if ($intent->status === 'succeeded') {
                app(EscrowService::class)->markHeld($escrow->stripe_payment_intent_id);

                // ── Reload job after markHeld updates it ──────────────
                $job->refresh()->load(['acceptedOffer.serviceProvider', 'assignedProvider', 'vehicle']);

                // Resolve the provider from either relationship
                $provider = $job->assignedProvider
                    ?? $job->acceptedOffer?->serviceProvider;

                // ── Notify provider: payment confirmed, job in work queue ──
                if ($provider && $provider->user_id) {
                    Alert::create([
                        'user_id'      => $provider->user_id,
                        'vehicle_id'   => $job->vehicle_id,
                        'type'         => 'booking',
                        'title'        => '💳 Payment Confirmed — Job Ready!',
                        'message'      => "Payment of {$escrow->formattedAmount()} has been held in escrow for job #{$job->job_number} ({$job->service_type}). The job is now in your Work Queue.",
                        'action_url'   => route('provider.jobs.work.show', $job),
                        'priority'     => 'success',
                        'for_provider' => true,
                    ]);
                }

                return response()->json(['status' => 'held']);
            }

            return response()->json(['status' => $intent->status]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // POST /jobs/{job}/payment/release
    public function release(ServiceJobPost $job)
    {
        abort_unless($job->user_id === auth()->id(), 403);

        $escrow = $job->escrow;

        if (!$escrow || $escrow->status !== 'held') {
            return back()->with('error', 'No held payment found for this job.');
        }

        try {
            $this->escrow->release($escrow);

            // Resolve provider from either relationship
            $job->refresh()->load(['assignedProvider', 'acceptedOffer.serviceProvider']);
            $provider = $job->assignedProvider
                ?? $job->acceptedOffer?->serviceProvider;

            // Notify consumer
            auth()->user()->alerts()->create([
                'user_id'      => auth()->id(),
                'vehicle_id'   => $job->vehicle_id,
                'type'         => 'booking',
                'title'        => 'Payment Released',
                'message'      => "You released payment of {$escrow->formattedAmount()} for job #{$job->job_number}.",
                'priority'     => 'success',
                'for_provider' => false,
            ]);

            // Notify provider
            if ($provider && $provider->user_id) {
                Alert::create([
                    'user_id'      => $provider->user_id,
                    'vehicle_id'   => $job->vehicle_id,
                    'type'         => 'booking',
                    'title'        => '💸 Payment Received!',
                    'message'      => "Payment of {$escrow->formattedAmount()} for job #{$job->job_number} has been released to your account.",
                    'action_url'   => route('provider.payments.index'),
                    'priority'     => 'success',
                    'for_provider' => true,
                ]);
            }

            return redirect()->route('jobs.show', $job)
                ->with('success', 'Payment released! The provider will receive their funds within 1–2 business days.');

        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'not connected a Stripe account')) {
                return back()->with('error',
                    '⚠ The provider hasn\'t set up their payout account yet. Your funds remain safely held in escrow.'
                );
            }
            return back()->with('error', 'Could not release payment: ' . $e->getMessage());
        }
    }

    // POST /jobs/{job}/payment/refund
    public function refund(ServiceJobPost $job)
    {
        abort_unless($job->user_id === auth()->id(), 403);

        if (in_array($job->work_status, ['in_progress', 'completed'])) {
            return back()->with('error', 'Cannot refund after work has begun.');
        }

        $escrow = $job->escrow;

        if (!$escrow || !in_array($escrow->status, ['held', 'pending'])) {
            return back()->with('error', 'No refundable payment found.');
        }

        try {
            $this->escrow->refund($escrow);
            return redirect()->route('jobs.index')
                ->with('success', 'Refund initiated. Funds return to your card in 5–10 business days.');
        } catch (\Exception $e) {
            return back()->with('error', 'Refund failed: ' . $e->getMessage());
        }
    }
}