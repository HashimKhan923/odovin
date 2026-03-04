<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Vehicle, MaintenanceSchedule, ServiceJobPost, Expense, Alert, Reminder, VehicleRecall , FuelLog};
use Carbon\Carbon;
use App\Services\{
    DashboardStatsService,
    MaintenanceService,
    ExpenseAnalyticsService,
    AlertService,
    ReminderService,
    DocumentService,
    FuelInsightsService,
    
};

class DashboardController extends Controller
{
        public function index(
            Request $request,
            DashboardStatsService $statsService,
            MaintenanceService $maintenanceService,
            ExpenseAnalyticsService $expenseService,
            AlertService $alertService,
            ReminderService $reminderService,
            DocumentService $documentService,
            FuelInsightsService $fuelInsightsService
        ) {
            $user = $request->user();

            $vehicles = $user->vehicles()->with(['maintenanceSchedules', 'documents'])->get();
            $vehicleIds = $vehicles->pluck('id');


            return view('dashboard.index', [
                'vehicles'            => $vehicles,
                'primaryVehicle'      => $vehicles->firstWhere('is_primary', true) ?? $vehicles->first(),

                'stats'               => $statsService->get($vehicles, $user->id),
                'upcomingMaintenance' => $maintenanceService->upcoming($vehicleIds),
                'recentJobs'          => ServiceJobPost::where('user_id', $user->id)->with(['acceptedOffer.serviceProvider','vehicle'])->latest()->limit(5)->get(),
                'recentExpenses'      => $expenseService->recent($vehicleIds),

                'alerts'              => $alertService->unread($user->id),
                'upcomingReminders'   => $reminderService->upcoming($vehicleIds),
                'expiringDocuments'   => $documentService->expiring($vehicles),

                'monthlyExpenses'     => $expenseService->monthlyChart($vehicleIds),
                'expensesByCategory'  => $expenseService->byCategory($vehicleIds),
                'fuelInsights'        => $fuelInsightsService->getInsights($vehicleIds),
            ]);
        }

}