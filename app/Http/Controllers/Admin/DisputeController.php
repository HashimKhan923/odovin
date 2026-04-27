<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Dispute;
use App\Models\DisputeMessage;
use App\Services\EscrowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DisputeController extends Controller
{
    // GET /admin/disputes
    public function index(Request $request)
    {
        $query = Dispute::with(['job.user', 'job.escrow', 'raisedBy', 'assignee'])->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('reference', 'like', "%{$s}%")
                  ->orWhereHas('job', fn($jq) => $jq->where('job_number', 'like', "%{$s}%"))
                  ->orWhereHas('raisedBy', fn($uq) => $uq->where('name', 'like', "%{$s}%"));
            });
        }

        $disputes = $query->paginate(20)->withQueryString();

        $stats = [
            'open'         => Dispute::where('status', 'open')->count(),
            'under_review' => Dispute::where('status', 'under_review')->count(),
            'resolved'     => Dispute::whereIn('status', ['resolved_consumer','resolved_provider','resolved_split'])->count(),
            'total'        => Dispute::count(),
            'frozen_amount'=> \App\Models\JobEscrow::where('status', 'disputed')->sum('amount'),
        ];

        return view('admin.disputes.index', compact('disputes', 'stats'));
    }

    // GET /admin/disputes/{dispute}
    public function show(Dispute $dispute)
    {
        $dispute->load([
            'job.user', 'job.vehicle', 'job.escrow',
            'job.acceptedOffer.serviceProvider',
            'messages.user',
            'raisedBy', 'assignee', 'resolver',
        ]);

        return view('admin.disputes.show', compact('dispute'));
    }

    // POST /admin/disputes/{dispute}/assign
    // Assign dispute to an admin
    public function assign(Dispute $dispute)
    {
        $dispute->update([
            'assigned_to' => Auth::id(),
            'status'      => 'under_review',
        ]);

        // Notify both parties
        $this->notifyBothParties($dispute,
            '🔍 Dispute Under Review',
            "Dispute {$dispute->reference} is now being reviewed by our team. We'll update you within 24 hours.",
        );

        return back()->with('success', 'Dispute assigned to you and marked Under Review.');
    }

    // POST /admin/disputes/{dispute}/message
    // Admin adds a message visible to both parties
    public function message(Request $request, Dispute $dispute)
    {
        $request->validate([
            'message'     => 'required|string|min:5|max:2000',
            'is_internal' => 'nullable|boolean',
        ]);

        $isInternal = $request->boolean('is_internal');

        DisputeMessage::create([
            'dispute_id'  => $dispute->id,
            'user_id'     => Auth::id(),
            'sender_role' => 'admin',
            'message'     => $request->message,
            'is_internal' => $isInternal,
        ]);

        $dispute->update([
            'message_count'   => $dispute->message_count + 1,
            'last_message_at' => now(),
        ]);

        if (!$isInternal) {
            $this->notifyBothParties($dispute,
                '⚖ Admin message on dispute ' . $dispute->reference,
                \Str::limit($request->message, 100)
            );
        }

        return back()->with('success', 'Message sent.');
    }

    // POST /admin/disputes/{dispute}/resolve
    // Admin resolves the dispute and executes the financial action

    public function resolve(Request $request, Dispute $dispute)
    {
        abort_unless($dispute->isActive(), 422, 'Dispute is already resolved.');

        $request->validate([
            'resolution'       => 'required|in:full_refund,partial_refund,release_to_provider,no_action',
            'resolution_notes' => 'required|string|min:10|max:2000',
            'resolution_amount'=> 'required_if:resolution,partial_refund|nullable|numeric|min:1',
        ]);

        $escrow     = $dispute->escrow;
        $escrowSvc  = app(EscrowService::class);
        $resolution = $request->resolution;

        DB::transaction(function () use ($dispute, $escrow, $escrowSvc, $resolution, $request) {

            // ── Execute financial action ───────────────────────────
            match ($resolution) {
                'full_refund'         => $this->executeFullRefund($escrowSvc, $escrow),
                'partial_refund'      => $this->executePartialRefund($escrowSvc, $escrow, (int) round($request->resolution_amount * 100)),
                'release_to_provider' => $this->executeRelease($escrowSvc, $escrow),
                'no_action'           => null,
            };

            // ── Update dispute record ──────────────────────────────
            $statusMap = [
                'full_refund'         => 'resolved_consumer',
                'partial_refund'      => 'resolved_split',
                'release_to_provider' => 'resolved_provider',
                'no_action'           => 'closed',
            ];

            $dispute->update([
                'status'            => $statusMap[$resolution],
                'resolution'        => $resolution,
                'resolution_notes'  => $request->resolution_notes,
                'resolution_amount' => $resolution === 'partial_refund'
                                        ? (int) round($request->resolution_amount * 100)
                                        : null,
                'resolved_by'       => Auth::id(),
                'resolved_at'       => now(),
            ]);

            // ── Admin message on resolution ────────────────────────
            DisputeMessage::create([
                'dispute_id'  => $dispute->id,
                'user_id'     => Auth::id(),
                'sender_role' => 'admin',
                'message'     => "**Resolution: {$dispute->fresh()->resolutionLabel()}**\n\n{$request->resolution_notes}",
                'is_internal' => false,
            ]);

            // ── Notify both parties ────────────────────────────────
            $this->notifyBothParties($dispute,
                '✅ Dispute ' . $dispute->reference . ' Resolved',
                "Resolution: {$dispute->fresh()->resolutionLabel()}. " . \Str::limit($request->resolution_notes, 100),
            );
        });

        return redirect()->route('admin.disputes.index')
            ->with('success', "Dispute {$dispute->reference} resolved: {$request->resolution}.");
    }

    // ── Private financial helpers ─────────────────────────────────

    private function executeFullRefund(EscrowService $svc, $escrow): void
    {
        if ($escrow && $escrow->status === 'disputed') {
            $svc->refund($escrow, 'fraudulent');
        }
    }

    private function executePartialRefund(EscrowService $svc, $escrow, int $refundCents): void
    {
        if (!$escrow) return;

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        \Stripe\Refund::create([
            'payment_intent' => $escrow->stripe_payment_intent_id,
            'amount'         => $refundCents,
            'reason'         => 'fraudulent',
        ]);

        // Remaining goes to provider
        $remainingCents = $escrow->amount - $refundCents;
        if ($remainingCents > 0) {
            $job      = $escrow->jobPost;
            $provider = $job->assignedProvider ?? $job->acceptedOffer?->serviceProvider;
            if ($provider?->stripe_account_id) {
                $feePct  = $escrow->platform_fee / $escrow->amount;
                $fee     = (int) round($remainingCents * $feePct);
                $payout  = $remainingCents - $fee;

                \Stripe\Transfer::create([
                    'amount'      => $payout,
                    'currency'    => $escrow->currency,
                    'destination' => $provider->stripe_account_id,
                    'description' => "Partial payout — dispute {$escrow->jobPost->job_number}",
                ]);
            }
        }

        $escrow->update(['status' => 'released', 'released_at' => now()]);
        $escrow->jobPost->update(['payment_status' => 'released']);
    }

    private function executeRelease(EscrowService $svc, $escrow): void
    {
        if ($escrow && $escrow->status === 'disputed') {
            $escrow->update(['status' => 'held']); // restore to held so release() works
            $svc->release($escrow);
        }
    }

    private function notifyBothParties(Dispute $dispute, string $title, string $message): void
    {
        $job      = $dispute->job;
        $provider = $job->acceptedOffer?->serviceProvider ?? $job->assignedProvider;

        // Consumer
        Alert::create([
            'user_id'      => $job->user_id,
            'type'         => 'booking',
            'title'        => $title,
            'message'      => $message,
            'action_url'   => route('disputes.show', $dispute),
            'priority'     => 'info',
            'for_provider' => false,
        ]);

        // Provider
        if ($provider) {
            Alert::create([
                'user_id'      => $provider->user_id,
                'type'         => 'booking',
                'title'        => $title,
                'message'      => $message,
                'action_url'   => route('disputes.show', $dispute),
                'priority'     => 'info',
                'for_provider' => true,
            ]);
        }
    }
}