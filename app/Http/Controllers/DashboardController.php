<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Vehicle, MaintenanceSchedule, ServiceBooking, Expense, Alert, Reminder, VehicleRecall};
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get user's vehicles
        $vehicles = $user->vehicles()->with(['maintenanceSchedules', 'documents'])->get();
        $primaryVehicle = $vehicles->firstWhere('is_primary', true) ?? $vehicles->first();
        
        // Get statistics
        $stats = [
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

                'open_recalls' => VehicleRecall::whereIn('vehicle_id', $vehicles->pluck('id'))
                    ->where('is_open', true)
                    ->count()
        ];
        
        // Get upcoming maintenance
        $upcomingMaintenance = MaintenanceSchedule::whereIn('vehicle_id', $vehicles->pluck('id'))
            ->whereIn('status', ['pending', 'overdue'])
            ->with('vehicle')
            ->orderBy('due_date')
            ->limit(5)
            ->get();
        
        // Get recent bookings
        $recentBookings = ServiceBooking::where('user_id', $user->id)
            ->with(['vehicle', 'serviceProvider'])
            ->latest()
            ->limit(5)
            ->get();
        
        // Get recent expenses
        $recentExpenses = Expense::whereIn('vehicle_id', $vehicles->pluck('id'))
            ->with('vehicle')
            ->latest('expense_date')
            ->limit(5)
            ->get();
        
        // Get unread alerts
        $alerts = Alert::where('user_id', $user->id)
            ->where('is_read', false)
            ->latest()
            ->limit(5)
            ->get();
        
        // Get upcoming reminders
        $upcomingReminders = Reminder::whereIn('vehicle_id', $vehicles->pluck('id'))
            ->where('is_completed', false)
            ->where('due_date', '>=', Carbon::now())
            ->orderBy('due_date')
            ->limit(5)
            ->get();
        
        // Get expiring documents
        $expiringDocuments = collect();
        foreach ($vehicles as $vehicle) {
            $expiringDocuments = $expiringDocuments->merge(
                $vehicle->getExpiringDocuments(30)
            );
        }



        // Monthly expense chart data
        $monthlyExpenses = Expense::whereIn('vehicle_id', $vehicles->pluck('id'))
            ->whereBetween('expense_date', [
                Carbon::now()->subMonths(6)->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->selectRaw('DATE_FORMAT(expense_date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Expense by category
        $expensesByCategory = Expense::whereIn('vehicle_id', $vehicles->pluck('id'))
            ->whereBetween('expense_date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();
        
        return view('dashboard.index', compact(
            'vehicles',
            'primaryVehicle',
            'stats',
            'upcomingMaintenance',
            'recentBookings',
            'recentExpenses',
            'alerts',
            'upcomingReminders',
            'expiringDocuments',
            'monthlyExpenses',
            'expensesByCategory',

        ));
    }
}