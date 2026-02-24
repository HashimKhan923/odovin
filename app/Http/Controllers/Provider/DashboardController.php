<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected function provider()
    {
        return Auth::user()->serviceProvider;
    }

    // rest of the index() method stays identical — just the provider() helper changed
    public function index()
    {
        $provider = $this->provider();
        $month    = Carbon::now()->startOfMonth();

        $stats = [
            'total_bookings'  => $provider->bookings()->count(),
            'pending'         => $provider->bookings()->where('status', 'pending')->count(),
            'confirmed'       => $provider->bookings()->where('status', 'confirmed')->count(),
            'in_progress'     => $provider->bookings()->where('status', 'in_progress')->count(),
            'completed'       => $provider->bookings()->where('status', 'completed')->count(),
            'today'           => $provider->bookings()->whereDate('scheduled_date', today())->count(),
            'this_week'       => $provider->bookings()
                ->whereBetween('scheduled_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month'      => $provider->bookings()->where('scheduled_date', '>=', $month)->count(),
            'monthly_revenue' => $provider->bookings()
                ->where('status', 'completed')->where('scheduled_date', '>=', $month)->sum('final_cost'),
            'total_revenue'   => $provider->bookings()->where('status', 'completed')->sum('final_cost'),
            'avg_rating'      => $provider->rating,
            'total_reviews'   => $provider->total_reviews,
            'cancelled'       => $provider->bookings()->where('status', 'cancelled')->count(),
        ];

        $recentBookings = $provider->bookings()
            ->with(['vehicle', 'user'])->latest('scheduled_date')->limit(8)->get();

        $todayBookings = $provider->bookings()
            ->with(['vehicle', 'user'])->whereDate('scheduled_date', today())->orderBy('scheduled_date')->get();

        $revenueChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $revenueChart[] = [
                'month'   => $m->format('M'),
                'revenue' => $provider->bookings()
                    ->where('status', 'completed')
                    ->whereYear('scheduled_date', $m->year)
                    ->whereMonth('scheduled_date', $m->month)
                    ->sum('final_cost'),
                'count' => $provider->bookings()
                    ->whereYear('scheduled_date', $m->year)
                    ->whereMonth('scheduled_date', $m->month)
                    ->count(),
            ];
        }

        $topServices = $provider->bookings()
            ->selectRaw('service_type, count(*) as count')
            ->groupBy('service_type')
            ->orderByDesc('count')
            ->limit(5)
            ->pluck('count', 'service_type')
            ->toArray();

        $recentReviews = $provider->bookings()
            ->with(['user', 'vehicle'])
            ->where('status', 'completed')
            ->whereNotNull('rating')
            ->latest()
            ->limit(5)
            ->get();

        $upcomingBookings = $provider->bookings()
            ->with(['vehicle', 'user'])
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('scheduled_date', '>=', now())
            ->orderBy('scheduled_date')
            ->limit(5)
            ->get();

        return view('provider.dashboard.index', compact(
            'provider', 'stats', 'recentBookings', 'todayBookings',
            'revenueChart', 'topServices', 'recentReviews', 'upcomingBookings'
        ));
    }
}