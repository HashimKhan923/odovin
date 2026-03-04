<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\ServiceJobOffer;
use App\Models\ServiceJobPost;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected function provider()
    {
        return Auth::user()->serviceProvider;
    }

    public function index()
    {
        $provider = $this->provider();
        $month    = Carbon::now()->startOfMonth();

        // All accepted offers for this provider
        $acceptedOfferIds = ServiceJobOffer::where('service_provider_id', $provider->id)
            ->where('status', 'accepted')
            ->pluck('job_post_id');

        $stats = [
            'total_jobs'      => $acceptedOfferIds->count(),
            'pending'         => ServiceJobPost::whereIn('id', $acceptedOfferIds)->where('work_status', 'pending')->count(),
            'confirmed'       => ServiceJobPost::whereIn('id', $acceptedOfferIds)->where('work_status', 'confirmed')->count(),
            'in_progress'     => ServiceJobPost::whereIn('id', $acceptedOfferIds)->where('work_status', 'in_progress')->count(),
            'completed'       => ServiceJobPost::whereIn('id', $acceptedOfferIds)->where('work_status', 'completed')->count(),
            'cancelled'       => ServiceJobPost::whereIn('id', $acceptedOfferIds)->where('work_status', 'cancelled')->count(),
            'monthly_revenue' => ServiceJobPost::whereIn('id', $acceptedOfferIds)
                ->where('work_status', 'completed')->where('work_completed_at', '>=', $month)->sum('final_cost'),
            'total_revenue'   => ServiceJobPost::whereIn('id', $acceptedOfferIds)
                ->where('work_status', 'completed')->sum('final_cost'),
            'avg_rating'      => $provider->rating,
            'total_reviews'   => $provider->total_reviews,
            'open_offers'     => ServiceJobOffer::where('service_provider_id', $provider->id)
                ->where('status', 'pending')->count(),
        ];

        // Recent work queue items
        $recentJobs = ServiceJobPost::whereIn('id', $acceptedOfferIds)
            ->with(['vehicle', 'user',
                'offers' => fn($q) => $q->where('service_provider_id', $provider->id)])
            ->latest()->limit(8)->get();

        // Active jobs (in queue right now)
        $activeJobs = ServiceJobPost::whereIn('id', $acceptedOfferIds)
            ->whereIn('work_status', ['pending', 'confirmed', 'in_progress'])
            ->with(['vehicle', 'user'])
            ->latest()->limit(5)->get();

        // Revenue chart (last 6 months)
        $revenueChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $monthIds = ServiceJobPost::whereIn('id', $acceptedOfferIds)
                ->where('work_status', 'completed')
                ->whereYear('work_completed_at', $m->year)
                ->whereMonth('work_completed_at', $m->month)
                ->pluck('id');

            $revenueChart[] = [
                'month'   => $m->format('M'),
                'revenue' => ServiceJobPost::whereIn('id', $monthIds)->sum('final_cost'),
                'count'   => $monthIds->count(),
            ];
        }

        // Top services
        $topServices = ServiceJobPost::whereIn('id', $acceptedOfferIds)
            ->selectRaw('service_type, count(*) as count')
            ->groupBy('service_type')
            ->orderByDesc('count')
            ->limit(5)
            ->pluck('count', 'service_type')
            ->toArray();

        // Recent reviews
        $recentReviews = ServiceJobPost::whereIn('id', $acceptedOfferIds)
            ->with(['user', 'vehicle'])
            ->where('work_status', 'completed')
            ->whereNotNull('rating')
            ->latest()
            ->limit(5)
            ->get();

        return view('provider.dashboard.index', compact(
            'provider', 'stats', 'recentJobs', 'activeJobs',
            'revenueChart', 'topServices', 'recentReviews'
        ));
    }
}