<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Vehicle, MaintenanceSchedule, ServiceBooking, Expense, Alert};
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $vehicles = $user->vehicles;

        $stats = $this->getStats($user, $vehicles);
        $upcomingMaintenance = $this->getUpcomingMaintenance($vehicles);
        $recentBookings = $this->getRecentBookings($user);
        $recentExpenses = $this->getRecentExpenses($vehicles);
        $alerts = $this->getAlerts($user);

        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => $stats,
                'upcoming_maintenance' => $upcomingMaintenance,
                'recent_bookings' => $recentBookings,
                'recent_expenses' => $recentExpenses,
                'alerts' => $alerts,
            ],
        ]);
    }

    public function stats(Request $request)
    {
        $user = $request->user();
        $vehicles = $user->vehicles;

        return response()->json([
            'success' => true,
            'data' => $this->getStats($user, $vehicles),
        ]);
    }

    public function charts(Request $request)
    {
        $user = $request->user();
        $vehicles = $user->vehicles;

        $monthlyExpenses = Expense::whereIn('vehicle_id', $vehicles->pluck('id'))
            ->whereBetween('expense_date', [
                Carbon::now()->subMonths(6)->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->selectRaw('DATE_FORMAT(expense_date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $expensesByCategory = Expense::whereIn('vehicle_id', $vehicles->pluck('id'))
            ->whereBetween('expense_date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'monthly_expenses' => $monthlyExpenses,
                'expenses_by_category' => $expensesByCategory,
            ],
        ]);
    }

    private function getStats($user, $vehicles)
    {
        return [
            'total_vehicles' => $vehicles->count(),
            'active_bookings' => ServiceBooking::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
                ->count(),
            'pending_maintenance' => MaintenanceSchedule::whereIn('vehicle_id', $vehicles->pluck('id'))
                ->whereIn('status', ['pending', 'overdue'])
                ->count(),
            'month_expenses' => Expense::whereIn('vehicle_id', $vehicles->pluck('id'))
                ->whereMonth('expense_date', Carbon::now()->month)
                ->whereYear('expense_date', Carbon::now()->year)
                ->sum('amount'),
        ];
    }

    private function getUpcomingMaintenance($vehicles)
    {
        return MaintenanceSchedule::whereIn('vehicle_id', $vehicles->pluck('id'))
            ->whereIn('status', ['pending', 'overdue'])
            ->with('vehicle')
            ->orderBy('due_date')
            ->limit(5)
            ->get();
    }

    private function getRecentBookings($user)
    {
        return ServiceBooking::where('user_id', $user->id)
            ->with(['vehicle', 'serviceProvider'])
            ->latest()
            ->limit(5)
            ->get();
    }

    private function getRecentExpenses($vehicles)
    {
        return Expense::whereIn('vehicle_id', $vehicles->pluck('id'))
            ->with('vehicle')
            ->latest('expense_date')
            ->limit(5)
            ->get();
    }

    private function getAlerts($user)
    {
        return Alert::where('user_id', $user->id)
            ->where('is_read', false)
            ->latest()
            ->limit(5)
            ->get();
    }
}