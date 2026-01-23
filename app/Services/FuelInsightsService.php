<?php

namespace App\Services;

use App\Models\FuelLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FuelInsightsService
{
    public function getInsights(Collection $vehicleIds): array
    {
        if ($vehicleIds->isEmpty()) {
            return $this->empty();
        }

        $now = now();
        $last30Days = $now->copy()->subDays(30);

        // ONE query for most data
        $logs = FuelLog::whereIn('vehicle_id', $vehicleIds)
            ->where('fill_date', '>=', $last30Days)
            ->orderBy('fill_date')
            ->get(['vehicle_id', 'fill_date', 'odometer', 'total_cost', 'mpg']);

        if ($logs->count() < 2) {
            return $this->empty();
        }

        $fuelSpentMonth = $logs
            ->whereBetween('fill_date', [$now->startOfMonth(), $now->endOfMonth()])
            ->sum('total_cost');

        $avgMpg30 = $logs
            ->whereNotNull('mpg')
            ->where('mpg', '>', 0)
            ->avg('mpg');

        [$costPerMile, $totalMiles] = $this->calculateCostPerMile($logs);

        $mpgTrend = $this->calculateMpgTrend($logs);

        return [
            'fuel_spent_month' => round($fuelSpentMonth, 2),
            'avg_mpg_30'       => $avgMpg30 ? round($avgMpg30, 1) : null,
            'fuel_cost_mile'  => $costPerMile,
            'mpg_trend'       => $mpgTrend,
            'total_miles'     => $totalMiles,
        ];
    }

    private function calculateCostPerMile(Collection $logs): array
    {
        $miles = 0;
        $cost = 0;

        for ($i = 1; $i < $logs->count(); $i++) {
            $delta = $logs[$i]->odometer - $logs[$i - 1]->odometer;

            if ($delta > 0) {
                $miles += $delta;
                $cost += $logs[$i]->total_cost;
            }
        }

        return [
            $miles > 0 ? round($cost / $miles, 3) : null,
            $miles
        ];
    }
private function calculateMpgTrend(Collection $logs): ?string
{
    // Extract MPG values as a clean numeric array
    $mpgValues = array_values(
        $logs->whereNotNull('mpg')->pluck('mpg')->toArray()
    );

    // We need at least 4 MPG values
    if (count($mpgValues) < 4) {
        return null;
    }

    // Take last 4 MPG readings
    $lastFour = array_slice($mpgValues, -4);

    // Now indexing is 100% safe
    $previousAvg = ($lastFour[0] + $lastFour[1]) / 2;
    $recentAvg   = ($lastFour[2] + $lastFour[3]) / 2;

    if ($recentAvg > $previousAvg * 1.05) {
        return 'up';
    }

    if ($recentAvg < $previousAvg * 0.95) {
        return 'down';
    }

    return 'stable';
}


    private function empty(): array
    {
        return [
            'fuel_spent_month' => 0,
            'avg_mpg_30'       => null,
            'fuel_cost_mile'   => null,
            'mpg_trend'        => null,
            'total_miles'      => 0,
        ];
    }
}
