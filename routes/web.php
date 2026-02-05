<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    VehicleController,
    VehicleDocumentController,
    MaintenanceController,
    ServiceBookingController,
    ExpenseController,
    ServiceProviderController,
    ReminderController,
    AlertController,
    ReportController,
    RecallController,
    ProfileController,
    FuelLogController,
    VehicleComparisonController,
    VehicleAIInsightController
};

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;


////////////////////////////////////////////// Admin Routes \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    Route::prefix('admin')->name('admin.')->group(function () {

        // Auth
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // Protected
        Route::middleware('admin')->group(function () {
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        });

    });


















///////////////////////////////////////////////////// User Routes \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes (using Laravel Breeze or Jetstream)
require __DIR__.'/auth.php';

// Protected Routes
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Vehicles
    Route::get(
    '/vehicles/decode-vin/{vin}',
    [\App\Http\Controllers\VehicleController::class, 'decodeVin']
)->middleware(['auth'])
 ->name('vehicles.decode-vin');

    Route::prefix('vehicles')->name('vehicles.')->group(function () {
        // Route::get('/decode-vin', [VehicleController::class, 'decodeVin'])->name('decode-vin');
        Route::get('/', [VehicleController::class, 'index'])->name('index');
        Route::get('/create', [VehicleController::class, 'create'])->name('create');
        Route::post('/', [VehicleController::class, 'store'])->name('store');
        Route::get('/{vehicle}', [VehicleController::class, 'show'])->name('show');
        Route::get('/{vehicle}/edit', [VehicleController::class, 'edit'])->name('edit');
        Route::put('/{vehicle}', [VehicleController::class, 'update'])->name('update');
        Route::delete('/{vehicle}', [VehicleController::class, 'destroy'])->name('destroy');
        Route::post('/{vehicle}/set-primary', [VehicleController::class, 'setPrimary'])->name('set-primary');
        Route::post('/{vehicle}/update-mileage', [VehicleController::class, 'updateMileage'])->name('update-mileage');

    // Vehicle Recalls
        // Route::get('/recalls', [RecallController::class, 'index'])->name('recalls.index');
        // Route::get('/recalls/{vehicle}', [RecallController::class, 'check'])->name('recalls.check');
        // Route::post('/recalls/sync/{vehicle}', [RecallController::class, 'sync'])->name('recalls.sync');

        // Vehicle Documents
        Route::prefix('{vehicle}/documents')->name('documents.')->group(function () {
            Route::get('/', [VehicleDocumentController::class, 'index'])->name('index');
            Route::get('/create', [VehicleDocumentController::class, 'create'])->name('create');
            Route::post('/', [VehicleDocumentController::class, 'store'])->name('store');
            Route::get('/{document}', [VehicleDocumentController::class, 'show'])->name('show');
            Route::get('/{document}/edit', [VehicleDocumentController::class, 'edit'])->name('edit');
            Route::put('/{document}', [VehicleDocumentController::class, 'update'])->name('update');
            Route::delete('/{document}', [VehicleDocumentController::class, 'destroy'])->name('destroy');
            Route::get('/{document}/download', [VehicleDocumentController::class, 'download'])->name('download');
        });
    });
    
    // Maintenance
    Route::prefix('maintenance')->name('maintenance.')->group(function () {
        Route::get('/', [MaintenanceController::class, 'index'])->name('index');
        Route::get('/create', [MaintenanceController::class, 'create'])->name('create');
        Route::post('/', [MaintenanceController::class, 'store'])->name('store');
        Route::get('/{maintenance}', [MaintenanceController::class, 'show'])->name('show');
        Route::get('/{maintenance}/edit', [MaintenanceController::class, 'edit'])->name('edit');
        Route::put('/{maintenance}', [MaintenanceController::class, 'update'])->name('update');
        Route::delete('/{maintenance}', [MaintenanceController::class, 'destroy'])->name('destroy');
        Route::post('/{maintenance}/complete', [MaintenanceController::class, 'markComplete'])->name('complete');
        
        // Service Records
        Route::prefix('records')->name('records.')->group(function () {
            Route::get('/', [MaintenanceController::class, 'records'])->name('index');
            Route::get('/create', [MaintenanceController::class, 'createRecord'])->name('create');
            Route::post('/', [MaintenanceController::class, 'storeRecord'])->name('store');
            Route::get('/{record}', [MaintenanceController::class, 'showRecord'])->name('show');
            Route::get('/{record}/edit', [MaintenanceController::class, 'editRecord'])->name('edit');
            Route::put('/{record}', [MaintenanceController::class, 'updateRecord'])->name('update');
            Route::delete('/{record}', [MaintenanceController::class, 'destroyRecord'])->name('destroy');
        });
    });
    
    // Service Bookings
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [ServiceBookingController::class, 'index'])->name('index');
        Route::get('/create', [ServiceBookingController::class, 'create'])->name('create');
        Route::post('/', [ServiceBookingController::class, 'store'])->name('store');
        Route::get('/{booking}', [ServiceBookingController::class, 'show'])->name('show');
        Route::get('/{booking}/edit', [ServiceBookingController::class, 'edit'])->name('edit');
        Route::put('/{booking}', [ServiceBookingController::class, 'update'])->name('update');
        Route::delete('/{booking}', [ServiceBookingController::class, 'destroy'])->name('destroy');
        Route::post('/{booking}/cancel', [ServiceBookingController::class, 'cancel'])->name('cancel');
        Route::post('/{booking}/rate', [ServiceBookingController::class, 'rate'])->name('rate');
    });
    
    // Service Providers
    Route::prefix('providers')->name('providers.')->group(function () {
        Route::get('/', [ServiceProviderController::class, 'index'])->name('index');
        Route::get('/{provider}', [ServiceProviderController::class, 'show'])->name('show');
        Route::get('/search/nearby', [ServiceProviderController::class, 'searchNearby'])->name('search-nearby');
    });

    // Comparison (NEW)
    Route::prefix('comparison')->name('comparison.')->group(function () {
        Route::get('/', [VehicleComparisonController::class, 'index'])->name('index');
        Route::post('/comparison', [VehicleComparisonController::class, 'compare'])->name('compare');
    });

    // Fuel Tracking (NEW)
    Route::prefix('fuel')->name('fuel.')->group(function () {
        Route::get('/', [FuelLogController::class, 'index'])->name('index');
        Route::get('/create', [FuelLogController::class, 'create'])->name('create');
        Route::post('/', [FuelLogController::class, 'store'])->name('store');
        Route::delete('/{fuelLog}', [FuelLogController::class, 'destroy'])->name('destroy');
        Route::get('/{fuelLog}/edit', [FuelLogController::class, 'edit'])->name('edit');
        Route::put('/{fuelLog}', [FuelLogController::class, 'update'])->name('update');
        Route::get('/import', [FuelLogController::class, 'importForm'])->name('import.form');
        Route::post('/import', [FuelLogController::class, 'import'])->name('import');
        Route::get('/export/csv/{vehicle_id?}', [FuelLogController::class, 'exportCsv'])->name('export.csv');
        Route::get('/export/pdf/{vehicle_id?}', [FuelLogController::class, 'exportPdf'])->name('export.pdf');
    });
    
    // Trip Logs (NEW)
    Route::prefix('trips')->name('trips.')->group(function () {
        Route::get('/', [TripLogController::class, 'index'])->name('index');
        Route::get('/create', [TripLogController::class, 'create'])->name('create');
        Route::post('/', [TripLogController::class, 'store'])->name('store');
        Route::get('/export', [TripLogController::class, 'export'])->name('export');
        Route::delete('/{tripLog}', [TripLogController::class, 'destroy'])->name('destroy');
    });
    
    // Insurance Management (NEW)
    Route::prefix('insurance')->name('insurance.')->group(function () {
        Route::get('/', [InsuranceController::class, 'index'])->name('index');
        Route::get('/create', [InsuranceController::class, 'create'])->name('create');
        Route::post('/', [InsuranceController::class, 'store'])->name('store');
        Route::get('/{insurance}', [InsuranceController::class, 'show'])->name('show');
        Route::delete('/{insurance}', [InsuranceController::class, 'destroy'])->name('destroy');
    });
    
    // Recall Check (NEW)
    Route::prefix('recalls')->name('recalls.')->group(function () {
        Route::get('/', [RecallController::class, 'index'])->name('index');
        Route::get('/{vehicle}/check', [RecallController::class, 'check'])->name('check');
        Route::post('/{vehicle}/sync', [RecallController::class, 'sync'])->name('sync');
    });
    
    // Vehicle Comparison (NEW)
    Route::prefix('comparison')->name('comparison.')->group(function () {
        Route::get('/', [VehicleComparisonController::class, 'index'])->name('index');
        Route::post('/compare', [VehicleComparisonController::class, 'compare'])->name('compare');
    });
    
    // Expenses
    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::get('/create', [ExpenseController::class, 'create'])->name('create');
        Route::post('/', [ExpenseController::class, 'store'])->name('store');
        Route::get('/{expense}', [ExpenseController::class, 'show'])->name('show');
        Route::get('/{expense}/edit', [ExpenseController::class, 'edit'])->name('edit');
        Route::put('/{expense}', [ExpenseController::class, 'update'])->name('update');
        Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->name('destroy');
        Route::get('/category/{category}', [ExpenseController::class, 'byCategory'])->name('by-category');
    });
    
    // Reminders
    Route::prefix('reminders')->name('reminders.')->group(function () {
        Route::get('/', [ReminderController::class, 'index'])->name('index');
        Route::get('/create', [ReminderController::class, 'create'])->name('create');
        Route::post('/', [ReminderController::class, 'store'])->name('store');
        Route::get('/{reminder}', [ReminderController::class, 'show'])->name('show');
        Route::get('/{reminder}/edit', [ReminderController::class, 'edit'])->name('edit');
        Route::put('/{reminder}', [ReminderController::class, 'update'])->name('update');
        Route::delete('/{reminder}', [ReminderController::class, 'destroy'])->name('destroy');
        Route::post('/{reminder}/complete', [ReminderController::class, 'markComplete'])->name('complete');
    });
    
    // Alerts/Notifications
    Route::prefix('alerts')->name('alerts.')->group(function () {
        Route::get('/', [AlertController::class, 'index'])->name('index');
        Route::get('/{alert}/read', [AlertController::class, 'markAsRead'])->name('mark-read');
        Route::post('/read-all', [AlertController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{alert}', [AlertController::class, 'destroy'])->name('destroy');
    });
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/expense-summary', [ReportController::class, 'expenseSummary'])->name('expense-summary');
        Route::get('/maintenance-history', [ReportController::class, 'maintenanceHistory'])->name('maintenance-history');
        Route::get('/vehicle-analytics/{vehicleId}', [ReportController::class, 'vehicleAnalytics'])->name('vehicle-analytics');
        Route::get('/export/{type}', [ReportController::class, 'export'])->name('export');
    });

    // Vehicle AI Insights
    Route::prefix('vehicles/{vehicle}/ai-insights')->name('vehicles.ai-insights.')->group(function () {
        Route::post('/generate', [VehicleAIInsightController::class, 'generate'])->name('generate');
    });
    
    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
});