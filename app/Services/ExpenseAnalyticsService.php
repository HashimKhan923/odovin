<?php

namespace App\Services;

use App\Models\Expense;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ExpenseAnalyticsService
{
    public function recent(Collection $vehicleIds, int $limit = 5)
    {
        return Expense::whereIn('vehicle_id', $vehicleIds)
            ->with('vehicle')
            ->latest('expense_date')
            ->limit($limit)
            ->get();
    }

    public function monthlyChart(Collection $vehicleIds)
    {
        return Expense::whereIn('vehicle_id', $vehicleIds)
            ->whereBetween('expense_date', [
                now()->subMonths(6)->startOfMonth(),
                now()->endOfMonth()
            ])
            ->selectRaw('DATE_FORMAT(expense_date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    public function byCategory(Collection $vehicleIds)
    {
        return Expense::whereIn('vehicle_id', $vehicleIds)
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();
    }
}
