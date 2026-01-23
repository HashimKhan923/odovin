<?php

namespace App\Services;

use App\Models\Expense;
use Illuminate\Support\Collection;

class MaintenanceAnalyticsService
{
    protected array $categories = ['maintenance', 'service', 'repair'];

    public function summary(Collection $vehicleIds): array
    {
        if ($vehicleIds->isEmpty()) {
            return $this->empty();
        }

        $expenses = Expense::whereIn('vehicle_id', $vehicleIds)
            ->whereIn('category', $this->categories)
            ->get(['vehicle_id', 'amount', 'expense_date']);

        return [
            'total_cost'        => $expenses->sum('amount'),
            'avg_per_vehicle'   => $this->averagePerVehicle($expenses, $vehicleIds),
            'highest_vehicle'   => $this->highestCostVehicle($expenses),
            'monthly_trend'     => $this->monthlyTrend($expenses),
        ];
    }

    private function averagePerVehicle($expenses, $vehicleIds): float
    {
        return $vehicleIds->count() > 0
            ? round($expenses->sum('amount') / $vehicleIds->count(), 2)
            : 0;
    }

    private function highestCostVehicle($expenses): ?array
    {
        $grouped = $expenses->groupBy('vehicle_id')
            ->map(fn ($items) => $items->sum('amount'));

        if ($grouped->isEmpty()) {
            return null;
        }

        $vehicleId = $grouped->sortDesc()->keys()->first();

        return [
            'vehicle_id' => $vehicleId,
            'amount'     => round($grouped[$vehicleId], 2),
        ];
    }

    private function monthlyTrend($expenses)
    {
        return $expenses
            ->groupBy(fn ($e) => $e->expense_date->format('Y-m'))
            ->map(fn ($items) => round($items->sum('amount'), 2))
            ->sortKeys();
    }

    private function empty(): array
    {
        return [
            'total_cost'      => 0,
            'avg_per_vehicle' => 0,
            'highest_vehicle' => null,
            'monthly_trend'   => collect(),
        ];
    }
}
