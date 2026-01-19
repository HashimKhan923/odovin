<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Expense;
use App\Models\ServiceRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF; // Install barryvdh/laravel-dompdf

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
        
        $startDate = $request->start_date ?? Carbon::now()->subMonths(6)->startOfMonth();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth();

        $expenses = Expense::whereHas('vehicle', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->when($request->vehicle_id, function ($query, $vehicleId) {
                return $query->where('vehicle_id', $vehicleId);
            })
            ->get();

        $totalExpenses = $expenses->sum('amount');
        $byCategory = $expenses->groupBy('category')->map->sum('amount');
        $byMonth = $expenses->groupBy(function ($expense) {
            return $expense->expense_date->format('Y-m');
        })->map->sum('amount');

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

    public function vehicleAnalytics(Request $request, Vehicle $vehicle)
    {
        // $this->authorize('view', $vehicle);

        $totalExpenses = $vehicle->getTotalExpenses();
        $expensesByCategory = $vehicle->expenses()
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        $monthlyExpenses = $vehicle->expenses()
            ->whereBetween('expense_date', [
                Carbon::now()->subMonths(12),
                Carbon::now()
            ])
            ->selectRaw('DATE_FORMAT(expense_date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $maintenanceCount = $vehicle->maintenanceSchedules()->count();
        $serviceCount = $vehicle->serviceRecords()->count();

        return view('reports.vehicle-analytics', compact(
            'vehicle', 'totalExpenses', 'expensesByCategory', 'monthlyExpenses', 
            'maintenanceCount', 'serviceCount'
        ));
    }
}