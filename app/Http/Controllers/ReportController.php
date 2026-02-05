<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Expense;
use App\Models\ServiceRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $vehicles = auth()->user()->vehicles;
        
        return view('reports.index', compact('vehicles'));
    }

    public function expenseSummary(Request $request)
    {
        $vehicles = auth()->user()->vehicles;
        
        // Use Carbon to parse dates or default values
        $startDate = $request->start_date 
            ? Carbon::parse($request->start_date) 
            : Carbon::now()->subMonths(6)->startOfMonth();
            
        $endDate = $request->end_date 
            ? Carbon::parse($request->end_date) 
            : Carbon::now()->endOfMonth();

        // Get expenses with proper eager loading
        $expenses = Expense::whereHas('vehicle', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->when($request->vehicle_id, function ($query, $vehicleId) {
                return $query->where('vehicle_id', $vehicleId);
            })
            ->orderBy('expense_date', 'desc')
            ->get();

        $totalExpenses = $expenses->sum('amount');
        
        // Group by category
        $byCategory = $expenses->groupBy('category')->map(function($group) {
            return $group->sum('amount');
        })->sortDesc();
        
        // Group by month
        $byMonth = $expenses->groupBy(function ($expense) {
            return $expense->expense_date->format('Y-m');
        })->map(function($group) {
            return $group->sum('amount');
        })->sortKeys();

        return view('reports.expense-summary', compact(
            'vehicles', 'expenses', 'totalExpenses', 'byCategory', 'byMonth', 'startDate', 'endDate'
        ));
    }

    public function maintenanceHistory(Request $request)
    {
        $vehicles = auth()->user()->vehicles;

        $records = ServiceRecord::whereHas('vehicle', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->with(['vehicle', 'serviceProvider'])
            ->when($request->vehicle_id, function ($query, $vehicleId) {
                return $query->where('vehicle_id', $vehicleId);
            })
            ->latest('service_date')
            ->get();

        return view('reports.maintenance-history', compact('vehicles', 'records'));
    }

    public function vehicleAnalytics(Request $request, $vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        
        // return $vehicle;
        // Make sure user owns this vehicle
        // if ($vehicle->user_id !== auth()->id()) {
        //     abort(403);
        // }

        // Total expenses
        $totalExpenses = $vehicle->expenses()->sum('amount');
        
        // Expenses by category
        $expensesByCategory = $vehicle->expenses()
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // Monthly expenses for last 12 months
        $monthlyExpenses = $vehicle->expenses()
            ->whereBetween('expense_date', [
                Carbon::now()->subMonths(12),
                Carbon::now()
            ])
            ->selectRaw('DATE_FORMAT(expense_date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Get counts
        $maintenanceCount = $vehicle->maintenanceSchedules()->count();
        $serviceCount = $vehicle->serviceRecords()->count();

        return view('reports.vehicle-analytics', compact(
            'vehicle', 'totalExpenses', 'expensesByCategory', 'monthlyExpenses', 
            'maintenanceCount', 'serviceCount'
        ));
    }
}