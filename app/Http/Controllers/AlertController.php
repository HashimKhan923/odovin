<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\ServiceJobPost;
use App\Models\ServiceJobOffer;
use App\Models\ServiceDiagnostic;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index(Request $request)
    {
        $alerts = Alert::where('user_id', $request->user()->id)
            ->with('vehicle')
            ->when($request->type, fn($q, $t) => $q->where('type', $t))
            ->when($request->priority, fn($q, $p) => $q->where('priority', $p))
            ->latest()
            ->paginate(20);

        return view('alerts.index', compact('alerts'));
    }

    public function fetch(Request $request)
    {
        $userId     = $request->user()->id;
        $isProvider = $request->boolean('provider');

        $query = Alert::where('user_id', $userId);

        if ($isProvider) {
            $query->where('for_provider', true);
        } else {
            $query->where(fn($q) => $q->where('for_provider', false)->orWhereNull('for_provider'));
        }

        $notifications = $query->latest()->limit(20)->get()
            ->map(fn($a) => [
                'id'         => $a->id,
                'title'      => $a->title,
                'message'    => $a->message,
                'type'       => $a->type,
                'priority'   => $a->priority,
                'color'      => $a->color,
                'icon'       => $a->icon,
                'action_url' => $a->action_url ?? null,
                'is_read'    => $a->is_read,
                'time'       => $a->created_at->diffForHumans(),
            ]);

        $unreadCount = Alert::where('user_id', $userId)
            ->where('is_read', false)
            ->when($isProvider,
                fn($q) => $q->where('for_provider', true),
                fn($q) => $q->where(fn($q2) => $q2->where('for_provider', false)->orWhereNull('for_provider'))
            )
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // counts() — called every 5s by both layouts to update nav badges
    // GET /alerts/counts          → consumer
    // GET /alerts/counts?provider=1 → provider
    // ─────────────────────────────────────────────────────────────────

    public function counts(Request $request)
    {
        $userId     = $request->user()->id;
        $isProvider = $request->boolean('provider');

        // Unread alert count
        $unreadCount = Alert::where('user_id', $userId)
            ->where('is_read', false)
            ->when($isProvider,
                fn($q) => $q->where('for_provider', true),
                fn($q) => $q->where(fn($q2) => $q2->where('for_provider', false)->orWhereNull('for_provider'))
            )
            ->count();

        $response = ['unread_count' => $unreadCount];

        if ($isProvider) {
            $providerId = $request->user()->serviceProvider?->id ?? 0;

            // Browse Open Jobs badge
            $response['open_jobs_count'] = ServiceJobPost::open()->count();

            // My Work Queue badge — accepted jobs ready to work on
            // work_status is NULL when just accepted, then becomes confirmed/in_progress
            $response['active_work_count'] = ServiceJobOffer::where('service_provider_id', $providerId)
                ->where('status', 'accepted')
                ->whereHas('jobPost', fn($q) => $q->whereIn('status', ['accepted'])
                    ->where(fn($q2) => $q2
                        ->whereNull('work_status')
                        ->orWhereIn('work_status', ['pending', 'confirmed', 'in_progress'])
                    )
                )
                ->count();

            // My Offers badge — pending counter-offers waiting for provider response
            $response['pending_counters_count'] = ServiceJobOffer::where('service_provider_id', $providerId)
                ->where('negotiation_status', 'countered')
                ->count();

            // Service Diagnostics badge
            $response['open_issues_count'] = ServiceDiagnostic::where('service_provider_id', $providerId)
                ->whereIn('status', ['open', 'acknowledged', 'in_progress'])
                ->count();

            // Quote Requests badge — pending quotes needing a response
            $response['pending_quotes_count'] = \App\Models\QuoteRequest::where('service_provider_id', $providerId)
                ->where('status', 'pending')
                ->count();

        } else {
            // Consumer: active accepted jobs
            $response['active_jobs_count'] = ServiceJobPost::where('user_id', $userId)
                ->where('status', 'accepted')
                ->count();

            // Consumer: jobs waiting for payment
            $response['unpaid_jobs_count'] = ServiceJobPost::where('user_id', $userId)
                ->whereIn('payment_status', ['unpaid', null])
                ->whereNotNull('accepted_offer_id')
                ->whereNotExists(function ($q) {
                    $q->select('id')->from('job_escrows')
                      ->whereColumn('job_escrows.job_post_id', 'service_job_posts.id');
                })
                ->count();

            // Consumer: quotes received needing accept/decline decision
            $response['quotes_action_count'] = \App\Models\QuoteRequest::where('user_id', $userId)
                ->where('status', 'quoted')
                ->whereNull('consumer_action')
                ->count();
        }

        return response()->json($response);
    }

    public function markAsRead(Alert $alert)
    {
        $alert->markAsRead();
        return back();
    }

    public function markAllAsRead(Request $request)
    {
        $isProvider = $request->boolean('provider');

        $query = Alert::where('user_id', $request->user()->id)->where('is_read', false);

        if ($isProvider) {
            $query->where('for_provider', true);
        } else {
            $query->where(fn($q) => $q->where('for_provider', false)->orWhereNull('for_provider'));
        }

        $query->update(['is_read' => true, 'read_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('success', 'All alerts marked as read!');
    }

    public function destroy(Alert $alert)
    {
        $alert->delete();
        return back()->with('success', 'Alert deleted!');
    }
}