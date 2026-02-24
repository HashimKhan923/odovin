<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\ServiceBooking;
use App\Models\ServiceProvider;
use App\Models\Expense;
use App\Models\FuelLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function overview(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year
        $startDate = $this->getStartDate($period);

        $stats = [
            'new_users' => User::where('user_type', 'user')
                ->where('created_at', '>=', $startDate)
                ->count(),
            'new_vehicles' => Vehicle::where('created_at', '>=', $startDate)->count(),
            'new_bookings' => ServiceBooking::where('created_at', '>=', $startDate)->count(),
            'total_revenue' => ServiceBooking::where('status', 'completed')
                ->where('created_at', '>=', $startDate)
                ->sum('price'),
            'total_expenses' => Expense::where('created_at', '>=', $startDate)->sum('amount'),
            'total_fuel_cost' => FuelLog::where('created_at', '>=', $startDate)->sum('total_cost'),
        ];

        // User growth chart data
        $userGrowth = User::where('user_type', 'user')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Booking status distribution
        $bookingsByStatus = ServiceBooking::where('created_at', '>=', $startDate)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Revenue chart data
        $revenueByDay = ServiceBooking::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(price) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.reports.overview', compact(
            'stats', 
            'userGrowth', 
            'bookingsByStatus', 
            'revenueByDay',
            'period'
        ));
    }

    public function users(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);

        $stats = [
            'total_users' => User::where('user_type', 'user')->count(),
            'new_users' => User::where('user_type', 'user')
                ->where('created_at', '>=', $startDate)
                ->count(),
            'active_users' => User::where('user_type', 'user')
                ->whereNotNull('email_verified_at')
                ->count(),
            'users_with_vehicles' => User::where('user_type', 'user')
                ->has('vehicles')
                ->count(),
        ];

        // User registrations over time
        $registrations = User::where('user_type', 'user')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top users by vehicles
        $topUsersByVehicles = User::where('user_type', 'user')
            ->withCount('vehicles')
            ->orderBy('vehicles_count', 'desc')
            ->take(10)
            ->get();

        // Top users by expenses
        $topUsersByExpenses = User::where('user_type', 'user')
            ->withSum('expenses', 'amount')
            ->orderBy('expenses_sum_amount', 'desc')
            ->take(10)
            ->get();

        return view('admin.reports.users', compact(
            'stats',
            'registrations',
            'topUsersByVehicles',
            'topUsersByExpenses',
            'period'
        ));
    }

    public function revenue(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);

        $stats = [
            'total_revenue' => ServiceBooking::where('status', 'completed')->sum('price'),
            'period_revenue' => ServiceBooking::where('status', 'completed')
                ->where('created_at', '>=', $startDate)
                ->sum('price'),
            'average_booking_value' => ServiceBooking::where('status', 'completed')
                ->where('created_at', '>=', $startDate)
                ->avg('price'),
            'completed_bookings' => ServiceBooking::where('status', 'completed')
                ->where('created_at', '>=', $startDate)
                ->count(),
        ];

        // Revenue by service type
        $revenueByServiceType = ServiceBooking::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('service_type, SUM(price) as total')
            ->groupBy('service_type')
            ->orderBy('total', 'desc')
            ->get();

        // Daily revenue
        $dailyRevenue = ServiceBooking::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(price) as total, COUNT(*) as bookings')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top service providers by revenue
        $topProviders = ServiceProvider::withSum([
            'bookings' => function($query) use ($startDate) {
                $query->where('status', 'completed')
                      ->where('created_at', '>=', $startDate);
            }
        ], 'price')
        ->orderBy('bookings_sum_price', 'desc')
        ->take(10)
        ->get();

        return view('admin.reports.revenue', compact(
            'stats',
            'revenueByServiceType',
            'dailyRevenue',
            'topProviders',
            'period'
        ));
    }

    public function vehicles(Request $request)
    {
        $stats = [
            'total_vehicles' => Vehicle::count(),
            'vehicles_by_type' => Vehicle::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type'),
            'vehicles_by_fuel_type' => Vehicle::selectRaw('fuel_type, COUNT(*) as count')
                ->groupBy('fuel_type')
                ->pluck('count', 'fuel_type'),
            'total_mileage' => Vehicle::sum('mileage'),
            'average_year' => round(Vehicle::avg('year')),
        ];

        // Popular makes and models
        $popularMakes = Vehicle::selectRaw('make, COUNT(*) as count')
            ->groupBy('make')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();

        $popularModels = Vehicle::selectRaw('CONCAT(make, " ", model) as vehicle, COUNT(*) as count')
            ->groupBy('make', 'model')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();

        return view('admin.reports.vehicles', compact(
            'stats',
            'popularMakes',
            'popularModels'
        ));
    }

    private function getStartDate($period)
    {
        return match($period) {
            'day' => Carbon::today(),
            'week' => Carbon::now()->subWeek(),
            'month' => Carbon::now()->subMonth(),
            'year' => Carbon::now()->subYear(),
            default => Carbon::now()->subMonth(),
        };
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'users');
        $format = $request->get('format', 'csv');

        // Implementation for CSV/PDF export
        // This is a placeholder - implement based on your needs
        
        return back()->with('success', 'Report export functionality coming soon.');
    }
}