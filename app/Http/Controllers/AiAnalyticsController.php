<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Expense;
use App\Models\FuelLog;
use App\Models\MaintenanceSchedule;
use App\Models\ServiceRecord;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AiAnalyticsController extends Controller
{
     public function index(Request $request)
    {
        $user = $request->user();
        $vehicleId = $request->vehicle_id;

        // Get vehicles
        $vehicles = $user->vehicles()->with(['fuelLogs', 'expenses', 'maintenanceSchedules'])->get();

        if ($vehicles->isEmpty()) {
            return view('analytics.index', [
                'vehicles' => $vehicles,
                'hasVehicles' => false,
            ]);
        }

        // Select vehicle (default to primary or first)
        $selectedVehicle = $vehicleId 
            ? $vehicles->firstWhere('id', $vehicleId)
            : ($vehicles->firstWhere('is_primary', true) ?? $vehicles->first());

        // Calculate all analytics
        $predictiveMaintenance = $this->calculatePredictiveMaintenance($selectedVehicle);
        $costForecast = $this->calculateCostForecast($selectedVehicle);
        $fuelAnalytics = $this->calculateFuelAnalytics($selectedVehicle);
        $roiAnalysis = $this->calculateROI($selectedVehicle);
        $fleetComparison = $this->calculateFleetComparison($vehicles);

        return view('analytics.index', compact(
            'vehicles',
            'selectedVehicle',
            'predictiveMaintenance',
            'costForecast',
            'fuelAnalytics',
            'roiAnalysis',
            'fleetComparison'
        ));
    }

    /**
     * AI-Powered Predictive Maintenance
     * Predicts when parts will fail based on usage patterns
     */
    protected function calculatePredictiveMaintenance(Vehicle $vehicle)
    {
        $currentMileage = $vehicle->current_mileage;
        $vehicleAge = $vehicle->purchase_date 
            ? Carbon::parse($vehicle->purchase_date)->diffInMonths(now()) 
            : 0;

        // Get historical maintenance data
        $maintenanceHistory = ServiceRecord::where('vehicle_id', $vehicle->id)
            ->orderBy('service_date', 'desc')
            ->get();

        // Calculate average miles driven per month
        $milesDrivenPerMonth = $vehicleAge > 0 
            ? $currentMileage / $vehicleAge 
            : 1000; // Default estimate

        // Predictive rules based on industry standards and ML patterns
        $predictions = [];

        // 1. OIL CHANGE PREDICTION
        $lastOilChange = $maintenanceHistory->first(function ($record) {
            return stripos($record->service_type, 'oil') !== false;
        });

        $milesSinceOilChange = $lastOilChange 
            ? $currentMileage - $lastOilChange->mileage_at_service 
            : $currentMileage;

        $oilChangeInterval = 5000; // Standard interval
        $oilChangeRemaining = max(0, $oilChangeInterval - $milesSinceOilChange);
        $oilChangeDaysRemaining = (int)($oilChangeRemaining / ($milesDrivenPerMonth / 30));

        $predictions[] = [
            'component' => 'Engine Oil',
            'prediction' => 'Due Soon',
            'confidence' => $this->calculateConfidence($oilChangeRemaining, 0, 5000),
            'miles_remaining' => $oilChangeRemaining,
            'days_remaining' => max(0, $oilChangeDaysRemaining),
            'estimated_cost' => 50,
            'severity' => $oilChangeRemaining < 500 ? 'critical' : ($oilChangeRemaining < 1500 ? 'high' : 'medium'),
            'recommendation' => $oilChangeRemaining < 500 
                ? 'Schedule immediately to prevent engine damage' 
                : 'Schedule within the next few weeks',
        ];

        // 2. BRAKE PAD PREDICTION
        $lastBrakeService = $maintenanceHistory->first(function ($record) {
            return stripos($record->service_type, 'brake') !== false;
        });

        $milesSinceBrakeService = $lastBrakeService 
            ? $currentMileage - $lastBrakeService->mileage_at_service 
            : $currentMileage;

        $brakeInterval = 40000; // Typical brake pad life
        $brakeRemaining = max(0, $brakeInterval - $milesSinceBrakeService);
        $brakeDaysRemaining = (int)($brakeRemaining / ($milesDrivenPerMonth / 30));

        if ($milesSinceBrakeService > 30000) {
            $predictions[] = [
                'component' => 'Brake Pads',
                'prediction' => $brakeRemaining < 5000 ? 'Replace Soon' : 'Monitor',
                'confidence' => $this->calculateConfidence($brakeRemaining, 0, 40000),
                'miles_remaining' => $brakeRemaining,
                'days_remaining' => max(0, $brakeDaysRemaining),
                'estimated_cost' => 300,
                'severity' => $brakeRemaining < 5000 ? 'high' : 'medium',
                'recommendation' => $brakeRemaining < 5000 
                    ? 'Inspect immediately - safety critical' 
                    : 'Schedule inspection within 6 months',
            ];
        }

        // 3. TIRE REPLACEMENT PREDICTION
        $lastTireService = $maintenanceHistory->first(function ($record) {
            return stripos($record->service_type, 'tire') !== false;
        });

        $milesSinceTireService = $lastTireService 
            ? $currentMileage - $lastTireService->mileage_at_service 
            : $currentMileage;

        $tireInterval = 50000; // Typical tire life
        $tireRemaining = max(0, $tireInterval - $milesSinceTireService);
        $tireDaysRemaining = (int)($tireRemaining / ($milesDrivenPerMonth / 30));

        if ($milesSinceTireService > 35000) {
            $predictions[] = [
                'component' => 'Tires',
                'prediction' => $tireRemaining < 10000 ? 'Replace Soon' : 'Monitor Tread',
                'confidence' => $this->calculateConfidence($tireRemaining, 0, 50000),
                'miles_remaining' => $tireRemaining,
                'days_remaining' => max(0, $tireDaysRemaining),
                'estimated_cost' => 600,
                'severity' => $tireRemaining < 5000 ? 'high' : 'medium',
                'recommendation' => 'Inspect tread depth and replace if below 3/32"',
            ];
        }

        // 4. BATTERY PREDICTION (age-based)
        if ($vehicleAge > 36) { // 3 years
            $batteryMonthsRemaining = max(0, 60 - $vehicleAge); // Typical 5-year life
            $predictions[] = [
                'component' => 'Battery',
                'prediction' => $batteryMonthsRemaining < 12 ? 'Replace Soon' : 'Monitor',
                'confidence' => $this->calculateConfidence($batteryMonthsRemaining, 0, 60),
                'miles_remaining' => null,
                'days_remaining' => $batteryMonthsRemaining * 30,
                'estimated_cost' => 150,
                'severity' => $batteryMonthsRemaining < 6 ? 'high' : 'medium',
                'recommendation' => 'Have battery tested at next service',
            ];
        }

        // 5. TRANSMISSION FLUID
        $lastTransmissionService = $maintenanceHistory->first(function ($record) {
            return stripos($record->service_type, 'transmission') !== false;
        });

        $milesSinceTransmission = $lastTransmissionService 
            ? $currentMileage - $lastTransmissionService->mileage_at_service 
            : $currentMileage;

        if ($milesSinceTransmission > 50000) {
            $transmissionInterval = 60000;
            $transmissionRemaining = max(0, $transmissionInterval - $milesSinceTransmission);
            
            $predictions[] = [
                'component' => 'Transmission Fluid',
                'prediction' => 'Service Due',
                'confidence' => 85,
                'miles_remaining' => $transmissionRemaining,
                'days_remaining' => (int)($transmissionRemaining / ($milesDrivenPerMonth / 30)),
                'estimated_cost' => 200,
                'severity' => 'medium',
                'recommendation' => 'Transmission fluid service recommended',
            ];
        }

        // Sort by severity and miles remaining
        usort($predictions, function ($a, $b) {
            $severityOrder = ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3];
            return $severityOrder[$a['severity']] <=> $severityOrder[$b['severity']];
        });

        return [
            'predictions' => $predictions,
            'total_estimated_cost' => array_sum(array_column($predictions, 'estimated_cost')),
            'critical_items' => count(array_filter($predictions, fn($p) => $p['severity'] === 'critical')),
            'avg_miles_per_month' => round($milesDrivenPerMonth),
        ];
    }

    /**
     * Calculate prediction confidence (0-100%)
     */
    protected function calculateConfidence($remaining, $min, $max)
    {
        if ($remaining <= 0) return 95;
        
        $percentage = ($remaining / $max) * 100;
        
        if ($percentage < 10) return 90;
        if ($percentage < 25) return 80;
        if ($percentage < 50) return 70;
        
        return 60;
    }

    /**
     * Cost Forecasting
     * Predicts future expenses based on historical patterns
     */
    protected function calculateCostForecast(Vehicle $vehicle)
    {
        // Get last 12 months of expenses
        $expenses = Expense::where('vehicle_id', $vehicle->id)
            ->where('expense_date', '>=', now()->subYear())
            ->orderBy('expense_date')
            ->get();

        if ($expenses->isEmpty()) {
            return [
                'has_data' => false,
                'message' => 'Not enough data for forecasting. Add more expenses to see predictions.',
            ];
        }

        // Calculate monthly averages by category
        $monthlyByCategory = $expenses->groupBy('category')
            ->map(function ($categoryExpenses) {
                return $categoryExpenses->avg('amount');
            });

        // Calculate trends
        $recentExpenses = $expenses->where('expense_date', '>=', now()->subMonths(3))->sum('amount');
        $olderExpenses = $expenses->where('expense_date', '<', now()->subMonths(3))
            ->where('expense_date', '>=', now()->subMonths(6))
            ->sum('amount');

        $trend = $olderExpenses > 0 
            ? (($recentExpenses - $olderExpenses) / $olderExpenses) * 100 
            : 0;

        // Forecast next 12 months
        $monthlyAverage = $expenses->avg('amount');
        $trendMultiplier = 1 + ($trend / 100 * 0.5); // Dampen the trend effect

        $forecast = [];
        for ($i = 1; $i <= 12; $i++) {
            $baseAmount = $monthlyAverage * $trendMultiplier;
            
            // Add seasonal variations
            $month = now()->addMonths($i)->month;
            $seasonalFactor = $this->getSeasonalFactor($month);
            
            $forecast[] = [
                'month' => now()->addMonths($i)->format('M Y'),
                'predicted_amount' => round($baseAmount * $seasonalFactor, 2),
                'confidence' => max(50, 85 - ($i * 3)), // Confidence decreases over time
            ];
        }

        return [
            'has_data' => true,
            'monthly_average' => round($monthlyAverage, 2),
            'trend_percentage' => round($trend, 1),
            'trend_direction' => $trend > 5 ? 'increasing' : ($trend < -5 ? 'decreasing' : 'stable'),
            'forecast' => $forecast,
            'next_month_prediction' => round($forecast[0]['predicted_amount'], 2),
            'next_year_prediction' => round(array_sum(array_column($forecast, 'predicted_amount')), 2),
            'category_breakdown' => $monthlyByCategory,
            'recommendation' => $this->getCostRecommendation($trend, $monthlyAverage),
        ];
    }

    /**
     * Get seasonal cost factor
     */
    protected function getSeasonalFactor($month)
    {
        // Winter months (Dec, Jan, Feb) = higher costs
        if (in_array($month, [12, 1, 2])) return 1.15;
        
        // Summer months (Jun, Jul, Aug) = moderate increase
        if (in_array($month, [6, 7, 8])) return 1.08;
        
        // Spring/Fall = baseline
        return 1.0;
    }

    /**
     * Get cost recommendation
     */
    protected function getCostRecommendation($trend, $average)
    {
        if ($trend > 15) {
            return 'Costs are rising significantly. Review recent expenses and consider preventive maintenance.';
        } elseif ($trend > 5) {
            return 'Slight increase in costs. Monitor maintenance schedule to avoid expensive repairs.';
        } elseif ($trend < -10) {
            return 'Great job! Your vehicle costs are decreasing. Keep up the maintenance routine.';
        } else {
            return 'Costs are stable. Continue current maintenance practices.';
        }
    }

    /**
     * Advanced Fuel Analytics
     */
    protected function calculateFuelAnalytics(Vehicle $vehicle)
    {
        $fuelLogs = FuelLog::where('vehicle_id', $vehicle->id)
            ->orderBy('fill_date', 'desc')
            ->get();

        if ($fuelLogs->count() < 2) {
            return [
                'has_data' => false,
                'message' => 'Add at least 2 fuel logs to see analytics.',
            ];
        }

        // Calculate MPG for each fill-up
        $mpgData = [];
        $costData = [];
        
        for ($i = 1; $i < $fuelLogs->count(); $i++) {
            $current = $fuelLogs[$i - 1];
            $previous = $fuelLogs[$i];
            
            $milesDriven = $previous->odometer - $current->odometer;
            $gallons = $current->gallons;
            
            if ($gallons > 0 && $milesDriven > 0) {
                $mpg = $milesDriven / $gallons;
                $costPerMile = $current->total_cost / $milesDriven;
                
                $mpgData[] = [
                    'date' => $current->fill_date->format('M d'),
                    'mpg' => round($mpg, 2),
                    'miles' => $milesDriven,
                ];
                
                $costData[] = [
                    'date' => $current->fill_date->format('M d'),
                    'cost_per_mile' => round($costPerMile, 3),
                    'total_cost' => round($current->total_cost, 2),
                ];
            }
        }

        // Reverse to show oldest first
        $mpgData = array_reverse($mpgData);
        $costData = array_reverse($costData);

        // Calculate statistics
        $avgMpg = collect($mpgData)->avg('mpg');
        $bestMpg = collect($mpgData)->max('mpg');
        $worstMpg = collect($mpgData)->min('mpg');
        
        $avgCostPerMile = collect($costData)->avg('cost_per_mile');
        $totalFuelCost = $fuelLogs->sum('total_cost');
        $totalGallons = $fuelLogs->sum('gallons');
        $totalMiles = $fuelLogs->first()->odometer - $fuelLogs->last()->odometer;

        // Calculate trend
        $recentMpg = collect($mpgData)->slice(-5)->avg('mpg');
        $olderMpg = collect($mpgData)->slice(0, 5)->avg('mpg');
        $mpgTrend = $olderMpg > 0 ? (($recentMpg - $olderMpg) / $olderMpg) * 100 : 0;

        return [
            'has_data' => true,
            'chart_data' => [
                'mpg' => $mpgData,
                'cost' => $costData,
            ],
            'statistics' => [
                'avg_mpg' => round($avgMpg, 2),
                'best_mpg' => round($bestMpg, 2),
                'worst_mpg' => round($worstMpg, 2),
                'avg_cost_per_mile' => round($avgCostPerMile, 3),
                'total_fuel_cost' => round($totalFuelCost, 2),
                'total_gallons' => round($totalGallons, 2),
                'total_miles' => $totalMiles,
                'mpg_trend' => round($mpgTrend, 1),
                'trend_direction' => $mpgTrend > 2 ? 'improving' : ($mpgTrend < -2 ? 'declining' : 'stable'),
            ],
            'recommendation' => $this->getFuelRecommendation($mpgTrend, $avgMpg),
        ];
    }

    /**
     * Get fuel efficiency recommendation
     */
    protected function getFuelRecommendation($trend, $avgMpg)
    {
        if ($trend < -5) {
            return 'Fuel efficiency is declining. Check tire pressure, air filter, and driving habits.';
        } elseif ($trend > 5) {
            return 'Excellent! Your fuel efficiency is improving. Keep up the good driving habits.';
        } else {
            return 'Fuel efficiency is stable. Regular maintenance helps maintain performance.';
        }
    }

    /**
     * ROI Analysis (Return on Investment / Total Cost of Ownership)
     */
    protected function calculateROI(Vehicle $vehicle)
    {
        $purchasePrice = $vehicle->purchase_price ?? 0;
        $purchaseDate = $vehicle->purchase_date 
            ? Carbon::parse($vehicle->purchase_date) 
            : now();
        
        $monthsOwned = max(1, $purchaseDate->diffInMonths(now()));
        $yearsOwned = $monthsOwned / 12;

        // Total expenses
        $totalExpenses = Expense::where('vehicle_id', $vehicle->id)->sum('amount');
        
        // Expenses by category
        $expensesByCategory = Expense::where('vehicle_id', $vehicle->id)
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get()
            ->pluck('total', 'category');

        // Calculate depreciation (simplified model)
        $currentValue = $this->estimateVehicleValue($vehicle, $purchasePrice);
        $depreciation = $purchasePrice - $currentValue;

        // Total cost of ownership
        $totalCost = $purchasePrice + $totalExpenses;
        $totalCostPerMonth = $totalCost / $monthsOwned;
        $totalCostPerYear = $totalCostPerMonth * 12;

        // Miles driven
        $totalMiles = $vehicle->current_mileage;
        $costPerMile = $totalMiles > 0 ? $totalCost / $totalMiles : 0;

        // Projected costs
        $projectedAnnualCost = $totalCostPerYear;
        $projected5YearCost = $projectedAnnualCost * 5;

        return [
            'purchase_info' => [
                'purchase_price' => $purchasePrice,
                'purchase_date' => $purchaseDate->format('M d, Y'),
                'months_owned' => $monthsOwned,
                'years_owned' => round($yearsOwned, 1),
            ],
            'current_status' => [
                'estimated_value' => round($currentValue, 2),
                'depreciation' => round($depreciation, 2),
                'depreciation_percentage' => $purchasePrice > 0 ? round(($depreciation / $purchasePrice) * 100, 1) : 0,
            ],
            'cost_analysis' => [
                'total_expenses' => round($totalExpenses, 2),
                'total_cost' => round($totalCost, 2),
                'cost_per_month' => round($totalCostPerMonth, 2),
                'cost_per_year' => round($totalCostPerYear, 2),
                'cost_per_mile' => round($costPerMile, 3),
            ],
            'expense_breakdown' => $expensesByCategory,
            'projections' => [
                'annual_cost' => round($projectedAnnualCost, 2),
                'five_year_cost' => round($projected5YearCost, 2),
            ],
            'recommendation' => $this->getROIRecommendation($costPerMile, $depreciation, $purchasePrice),
        ];
    }

    /**
     * Estimate vehicle current value (simplified depreciation model)
     */
    protected function estimateVehicleValue(Vehicle $vehicle, $purchasePrice)
    {
        if ($purchasePrice == 0) return 0;

        $age = $vehicle->purchase_date 
            ? Carbon::parse($vehicle->purchase_date)->diffInYears(now()) 
            : 0;

        // Typical depreciation: 20% first year, 15% per year after
        $depreciationRate = $age == 0 ? 0.20 : (0.20 + (($age - 1) * 0.15));
        $depreciationRate = min($depreciationRate, 0.80); // Cap at 80% depreciation

        return $purchasePrice * (1 - $depreciationRate);
    }

    /**
     * Get ROI recommendation
     */
    protected function getROIRecommendation($costPerMile, $depreciation, $purchasePrice)
    {
        if ($costPerMile > 0.75) {
            return 'Cost per mile is high. Consider reducing unnecessary trips or checking for maintenance issues.';
        } elseif ($depreciation / $purchasePrice > 0.60) {
            return 'Vehicle has depreciated significantly. If planning to sell, consider timing the market.';
        } else {
            return 'Your ownership costs are reasonable. Continue regular maintenance to preserve value.';
        }
    }

    /**
     * Fleet-wide comparison
     */
    protected function calculateFleetComparison($vehicles)
    {
        if ($vehicles->count() < 2) {
            return ['has_multiple' => false];
        }

        $comparison = $vehicles->map(function ($vehicle) {
            $totalExpenses = Expense::where('vehicle_id', $vehicle->id)->sum('amount');
            $fuelCost = Expense::where('vehicle_id', $vehicle->id)
                ->where('category', 'fuel')
                ->sum('amount');
            
            return [
                'id' => $vehicle->id,
                'name' => $vehicle->full_name,
                'total_expenses' => round($totalExpenses, 2),
                'fuel_cost' => round($fuelCost, 2),
                'mileage' => $vehicle->current_mileage,
            ];
        });

        return [
            'has_multiple' => true,
            'vehicles' => $comparison,
            'most_expensive' => $comparison->sortByDesc('total_expenses')->first(),
            'most_efficient' => $comparison->sortBy('fuel_cost')->first(),
        ];
    }

    /**
     * Export analytics data as PDF
     */
    public function exportPDF(Request $request, Vehicle $vehicle)
    {
        // TODO: Implement PDF export
        return back()->with('info', 'PDF export coming soon!');
    }
}
