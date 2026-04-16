<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobEscrow;
use App\Models\ProviderSubscription;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = JobEscrow::with(['jobPost.user', 'jobPost.assignedProvider']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('jobPost', function ($q) use ($s) {
                $q->where('job_number', 'like', "%$s%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%$s%"));
            });
        }

        $escrows = $query->latest()->paginate(25)->withQueryString();

        $stats = [
            'held_amount'     => JobEscrow::where('status', 'held')->sum('amount'),
            'held_count'      => JobEscrow::where('status', 'held')->count(),
            'released_amount' => JobEscrow::where('status', 'released')->sum('amount'),
            'refunded_amount' => JobEscrow::where('status', 'refunded')->sum('amount'),
            'overdue_count'   => JobEscrow::overdue()->count(),
            'platform_fees'   => JobEscrow::where('status', 'released')->sum('platform_fee'),
        ];

        return view('admin.payments.escrow', compact('escrows', 'stats'));
    }

    public function subscriptions(Request $request)
    {
        $query = ProviderSubscription::with(['provider', 'plan']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('plan')) {
            $query->whereHas('plan', fn($q) => $q->where('slug', $request->plan));
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('provider', fn($q) => $q->where('name', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%"));
        }

        $subscriptions = $query->latest()->paginate(25)->withQueryString();

        $stats = [
            'active'   => ProviderSubscription::where('status', 'active')->count(),
            'trialing' => ProviderSubscription::where('status', 'trialing')->count(),
            'past_due' => ProviderSubscription::where('status', 'past_due')->count(),
            'canceled' => ProviderSubscription::where('status', 'canceled')->count(),
            'mrr'      => $this->calculateMRR(),
        ];

        return view('admin.payments.subscriptions', compact('subscriptions', 'stats'));
    }

    private function calculateMRR(): int
    {
        $mrr = 0;
        foreach (ProviderSubscription::where('status', 'active')->with('plan')->get() as $sub) {
            if (!$sub->plan) continue;
            $mrr += $sub->billing_interval === 'yearly'
                ? (int) ($sub->plan->price_yearly / 12)
                : (int) $sub->plan->price_monthly;
        }
        return $mrr;
    }
}