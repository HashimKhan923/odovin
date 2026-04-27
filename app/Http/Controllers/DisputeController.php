<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Dispute;
use App\Models\DisputeMessage;
use App\Models\ServiceJobPost;
use App\Services\EscrowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DisputeController extends Controller
{
    // ── GET /jobs/{job}/dispute/create ────────────────────────────
    // Consumer opens a dispute form

    public function create(ServiceJobPost $job)
    {
        abort_unless($job->user_id === auth()->id(), 403);
        abort_unless($job->escrow && $job->escrow->status === 'held', 422,
            'A dispute can only be raised while payment is held in escrow.');
        abort_unless(!$job->disputes()->where('status', '!=', 'closed')->exists(), 422,
            'An active dispute already exists for this job.');

        $reasons = [
            'work_not_done'  => 'Work was not completed',
            'poor_quality'   => 'Work quality is unsatisfactory',
            'no_show'        => 'Provider did not show up',
            'overcharged'    => 'I was charged more than agreed',
            'damage'         => 'Provider damaged my vehicle',
            'other'          => 'Other issue',
        ];

        return view('disputes.create', compact('job', 'reasons'));
    }

    // ── POST /jobs/{job}/dispute ───────────────────────────────────
    // Consumer submits a dispute

    public function store(Request $request, ServiceJobPost $job)
    {
        abort_unless($job->user_id === auth()->id(), 403);

        // Check escrow exists and is in a disputable state
        if (!$job->escrow) {
            return back()->with('error', 'No payment found for this job. A dispute requires a held payment.');
        }

        if (!in_array($job->escrow->status, ['held', 'pending'])) {
            return back()->with('error',
                'Cannot raise a dispute — escrow status is "' . $job->escrow->status . '". ' .
                'Disputes can only be raised while payment is held in escrow.'
            );
        }

        if ($job->disputes()->whereNotIn('status', ['closed', 'resolved_consumer', 'resolved_provider', 'resolved_split'])->exists()) {
            return back()->with('error', 'An active dispute already exists for this job.');
        }

        $validated = $request->validate([
            'reason_code'  => 'required|in:work_not_done,poor_quality,no_show,overcharged,damage,other',
            'description'  => 'required|string|min:30|max:3000',
            'evidence.*'   => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
        ]);

        // Upload consumer evidence
        $evidencePaths = [];
        if ($request->hasFile('evidence')) {
            foreach ($request->file('evidence') as $file) {
                $evidencePaths[] = $file->store("disputes/{$job->id}/consumer", 'public');
            }
        }

        $dispute = Dispute::create([
            'job_post_id'       => $job->id,
            'job_escrow_id'     => $job->escrow->id,
            'raised_by_user_id' => auth()->id(),
            'raised_by_role'    => 'consumer',
            'reason_code'       => $validated['reason_code'],
            'description'       => $validated['description'],
            'status'            => 'open',
            'consumer_evidence' => $evidencePaths ?: null,
        ]);

        // Freeze escrow — block auto-release
        $job->escrow->update([
            'status'      => 'disputed',
            'release_at'  => null,           // cancel auto-release timer
            'disputed_at' => now(),
        ]);

        // Add opening message to thread
        $this->addMessage($dispute, auth()->id(), 'consumer', $validated['description']);

        // Notify provider
        $provider = $job->acceptedOffer?->serviceProvider ?? $job->assignedProvider;
        if ($provider) {
            Alert::create([
                'user_id'      => $provider->user_id,
                'type'         => 'booking',
                'title'        => '⚠ Dispute Raised — Job #' . $job->job_number,
                'message'      => auth()->user()->name . " has raised a dispute: \"{$dispute->reasonLabel()}\". Payment is frozen until resolved.",
                'action_url'   => route('disputes.show', $dispute),
                'priority'     => 'critical',
                'for_provider' => true,
            ]);
        }

        // Notify all admins
        foreach (\App\Models\User::where('role', 'admin')->pluck('id') as $adminId) {
            Alert::create([
                'user_id'      => $adminId,
                'type'         => 'booking',
                'title'        => '⚖ New Dispute — ' . $dispute->reference,
                'message'      => "Dispute raised for job #{$job->job_number}: {$dispute->reasonLabel()}. Escrow frozen.",
                'action_url'   => route('admin.disputes.show', $dispute),
                'priority'     => 'critical',
                'for_provider' => false,
            ]);
        }

        return redirect()->route('disputes.show', $dispute)
            ->with('success', 'Dispute raised. Payment is frozen and our team will review within 24–48 hours.');
    }

    // ── GET /disputes ─────────────────────────────────────────────
    // Consumer: disputes they raised
    // Provider: disputes on their jobs

    public function index()
    {
        $user     = auth()->user();
        $provider = $user->serviceProvider;
        $query    = Dispute::with(['job', 'messages', 'raisedBy', 'escrow'])->latest();

        if ($provider) {
            // Provider sees disputes on jobs they accepted
            $query->whereHas('job', function ($q) use ($provider) {
                $q->where('assigned_provider_id', $provider->id)
                  ->orWhereHas('offers', fn($o) =>
                      $o->where('service_provider_id', $provider->id)
                        ->where('status', 'accepted')
                  );
            });
        } else {
            // Consumer sees disputes they raised
            $query->where('raised_by_user_id', $user->id);
        }

        $disputes = $query->paginate(15);

        return view('disputes.index', compact('disputes'));
    }

    // ── GET /disputes/{dispute} ───────────────────────────────────
    // Consumer: view dispute thread

    public function show(Dispute $dispute)
    {
        $this->authorizeView($dispute);
        $dispute->load(['job.vehicle', 'job.escrow', 'messages.user', 'raisedBy']);
        return view('disputes.show', compact('dispute'));
    }

    // ── POST /disputes/{dispute}/message ──────────────────────────
    // Consumer or provider adds a message to the thread

    public function message(Request $request, Dispute $dispute)
    {
        $this->authorizeView($dispute);
        abort_unless($dispute->isActive(), 422, 'This dispute is closed.');

        $request->validate([
            'message'      => 'required|string|min:5|max:2000',
            'attachments.*'=> 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
        ]);

        $paths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $paths[] = $file->store("disputes/{$dispute->job_post_id}/messages", 'public');
            }
        }

        $role = $this->senderRole($dispute);
        $this->addMessage($dispute, auth()->id(), $role, $request->message, $paths);

        // Notify the other party
        $this->notifyOtherParty($dispute, $role, $request->message);

        return back()->with('success', 'Message sent.');
    }

    // ── POST /disputes/{dispute}/provider-response ────────────────
    // Provider uploads their evidence and responds

    public function providerResponse(Request $request, Dispute $dispute)
    {
        $provider = auth()->user()->serviceProvider;
        abort_unless($provider, 403);

        $job = $dispute->job;
        $isProvider = ($job->acceptedOffer?->serviceProvider?->id === $provider->id)
            || ($job->assigned_provider_id === $provider->id);
        abort_unless($isProvider, 403);
        abort_unless($dispute->isActive(), 422);

        $request->validate([
            'message'       => 'required|string|min:5|max:2000',
            'evidence.*'    => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
        ]);

        $paths = [];
        if ($request->hasFile('evidence')) {
            foreach ($request->file('evidence') as $file) {
                $paths[] = $file->store("disputes/{$dispute->job_post_id}/provider", 'public');
            }
        }

        if ($paths) {
            $existing = $dispute->provider_evidence ?? [];
            $dispute->update(['provider_evidence' => array_merge($existing, $paths)]);
        }

        $this->addMessage($dispute, auth()->id(), 'provider', $request->message, $paths);
        $this->notifyOtherParty($dispute, 'provider', $request->message);

        return back()->with('success', 'Response submitted. Admin will review both sides.');
    }

    // ── Helpers ───────────────────────────────────────────────────

    private function addMessage(Dispute $dispute, int $userId, string $role, string $message, array $paths = []): void
    {
        DisputeMessage::create([
            'dispute_id'  => $dispute->id,
            'user_id'     => $userId,
            'sender_role' => $role,
            'message'     => $message,
            'attachments' => $paths ?: null,
        ]);

        $dispute->update([
            'message_count'   => $dispute->message_count + 1,
            'last_message_at' => now(),
        ]);
    }

    private function senderRole(Dispute $dispute): string
    {
        $user     = auth()->user();
        $job      = $dispute->job;
        $provider = $user->serviceProvider;

        if ($provider) {
            $isProvider = ($job->acceptedOffer?->serviceProvider?->id === $provider->id)
                || ($job->assigned_provider_id === $provider->id);
            if ($isProvider) return 'provider';
        }

        return 'consumer';
    }

    private function authorizeView(Dispute $dispute): void
    {
        $user     = auth()->user();
        $job      = $dispute->job;
        $provider = $user->serviceProvider;

        $isConsumer = $job->user_id === $user->id;
        $isProvider = $provider && (
            $job->acceptedOffer?->serviceProvider?->id === $provider->id
            || $job->assigned_provider_id === $provider->id
        );

        abort_unless($isConsumer || $isProvider, 403);
    }

    private function notifyOtherParty(Dispute $dispute, string $senderRole, string $message): void
    {
        $job     = $dispute->job;
        $preview = \Str::limit($message, 80);

        if ($senderRole === 'consumer') {
            // Notify provider
            $provider = $job->acceptedOffer?->serviceProvider ?? $job->assignedProvider;
            if ($provider) {
                Alert::create([
                    'user_id'      => $provider->user_id,
                    'type'         => 'booking',
                    'title'        => '💬 New message in dispute ' . $dispute->reference,
                    'message'      => $preview,
                    'action_url'   => route('disputes.show', $dispute),
                    'priority'     => 'info',
                    'for_provider' => true,
                ]);
            }
        } else {
            // Notify consumer
            Alert::create([
                'user_id'      => $job->user_id,
                'type'         => 'booking',
                'title'        => '💬 New message in dispute ' . $dispute->reference,
                'message'      => $preview,
                'action_url'   => route('disputes.show', $dispute),
                'priority'     => 'info',
                'for_provider' => false,
            ]);
        }
    }
}