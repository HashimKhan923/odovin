<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\ServiceBooking;
use App\Models\ServiceProvider;
use App\Models\VehicleRecall;
use App\Models\ServiceJobPost;
use App\Models\JobEscrow;
use App\Models\ProviderSubscription;
use App\Models\SubscriptionPlan;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users_total'        => User::where('user_type', 'user')->count(),
            'users_new_week'     => User::where('user_type', 'user')->where('created_at', '>=', now()->subWeek())->count(),
            'vehicles_total'     => Vehicle::count(),
            'providers_total'    => ServiceProvider::count(),
            'providers_verified' => ServiceProvider::where('is_verified', true)->count(),
            'bookings_total'     => ServiceBooking::count(),
            'recalls_open'       => VehicleRecall::count(),
            'jobs_total'         => ServiceJobPost::count(),
            'jobs_open'          => ServiceJobPost::where('status', 'open')->count(),
            'jobs_in_progress'   => ServiceJobPost::where('work_status', 'in_progress')->count(),
            'jobs_completed'     => ServiceJobPost::where('work_status', 'completed')->count(),
            'escrow_held'        => JobEscrow::where('status', 'held')->sum('amount'),
            'escrow_held_count'  => JobEscrow::where('status', 'held')->count(),
            'escrow_overdue'     => JobEscrow::overdue()->count(),
            'platform_fees'      => JobEscrow::where('status', 'released')->sum('platform_fee'),
            'subs_active'        => ProviderSubscription::where('status', 'active')->count(),
            'subs_trialing'      => ProviderSubscription::where('status', 'trialing')->count(),
            'subs_past_due'      => ProviderSubscription::where('status', 'past_due')->count(),
        ];

        $revenueChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenueChart[] = [
                'month'   => $month->format('M'),
                'revenue' => JobEscrow::where('status', 'released')
                    ->whereYear('released_at', $month->year)
                    ->whereMonth('released_at', $month->month)
                    ->sum('amount'),
            ];
        }

        $recentJobs = ServiceJobPost::with(['user', 'assignedProvider'])
            ->latest()->limit(8)->get();

        $planBreakdown = SubscriptionPlan::withCount([
            'subscriptions as active_count' => fn($q) => $q->where('status', 'active'),
        ])->orderBy('sort_order')->get();

        return view('admin.dashboard.index', compact(
            'stats', 'revenueChart', 'recentJobs', 'planBreakdown'
        ));
    }
}