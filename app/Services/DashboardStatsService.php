<?php

namespace App\Services;

use App\Models\{
    ServiceBooking,
    MaintenanceSchedule,
    Expense,
    FuelLog,
    VehicleRecall
};
use Illuminate\Support\Collection;
use Carbon\Carbon;

class DashboardStatsService
{
    public function get(Collection $vehicles, int $userId): array
    {
        $vehicleIds = $vehicles->pluck('id');
        $now = Carbon::now();

        return [
            'total_vehicles' => $vehicles->count(),

            'active_bookings' => ServiceBooking::where('user_id', $userId)
                ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
                ->count(),

            'pending_maintenance' => MaintenanceSchedule::whereIn('vehicle_id', $vehicleIds)
                ->whereIn('status', ['pending', 'overdue'])
                ->count(),

            'month_expenses' => Expense::whereIn('vehicle_id', $vehicleIds)
                ->whereMonth('expense_date', $now->month)
                ->whereYear('expense_date', $now->year)
                ->sum('amount'),

            'fuel_cost_month' => FuelLog::whereIn('vehicle_id', $vehicleIds)
                ->whereMonth('fill_date', $now->month)
                ->whereYear('fill_date', $now->year)
                ->sum('total_cost'),

            'open_recalls' => VehicleRecall::whereIn('vehicle_id', $vehicleIds)
                ->where('is_open', true)
                ->count(),
        ];
    }
}
