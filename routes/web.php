<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    VehicleController,
    VehicleDocumentController,
    MaintenanceController,
    ExpenseController,
    ServiceProviderController,
    ReminderController,
    AlertController,
    ReportController,
    RecallController,
    ProfileController,
    FuelLogController,
    VehicleComparisonController,
    VehicleAIInsightController,
    AdvancedAnalyticsController,
    TripLogController,
    InsuranceController,
    AiAnalyticsController,
    JobPostController,
};

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;


////////////////////////////////////////////// Admin Routes \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

Route::prefix('admin')->name('admin.')->group(function () {

    // Auth Routes (Public)
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // Protected Admin Routes
    Route::middleware('admin')->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('store');
            Route::get('/{user}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('destroy');
            Route::post('/{user}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Vehicle Management
        Route::prefix('vehicles')->name('vehicles.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\VehicleController::class, 'index'])->name('index');
            Route::get('/statistics', [App\Http\Controllers\Admin\VehicleController::class, 'statistics'])->name('statistics');
            Route::get('/{vehicle}', [App\Http\Controllers\Admin\VehicleController::class, 'show'])->name('show');
            Route::delete('/{vehicle}', [App\Http\Controllers\Admin\VehicleController::class, 'destroy'])->name('destroy');
        });

        // Service Provider Management
        Route::prefix('providers')->name('providers.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ServiceProviderController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\ServiceProviderController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\ServiceProviderController::class, 'store'])->name('store');
            Route::get('/{provider}', [App\Http\Controllers\Admin\ServiceProviderController::class, 'show'])->name('show');
            Route::get('/{provider}/edit', [App\Http\Controllers\Admin\ServiceProviderController::class, 'edit'])->name('edit');
            Route::put('/{provider}', [App\Http\Controllers\Admin\ServiceProviderController::class, 'update'])->name('update');
            Route::delete('/{provider}', [App\Http\Controllers\Admin\ServiceProviderController::class, 'destroy'])->name('destroy');
            Route::post('/{provider}/toggle-status', [App\Http\Controllers\Admin\ServiceProviderController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Booking Management
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\BookingController::class, 'index'])->name('index');
            Route::get('/statistics', [App\Http\Controllers\Admin\BookingController::class, 'statistics'])->name('statistics');
            Route::get('/{booking}', [App\Http\Controllers\Admin\BookingController::class, 'show'])->name('show');
            Route::put('/{booking}/status', [App\Http\Controllers\Admin\BookingController::class, 'updateStatus'])->name('update-status');
            Route::delete('/{booking}', [App\Http\Controllers\Admin\BookingController::class, 'destroy'])->name('destroy');
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
            Route::get('/overview', [App\Http\Controllers\Admin\ReportController::class, 'overview'])->name('overview');
            Route::get('/users', [App\Http\Controllers\Admin\ReportController::class, 'users'])->name('users');
            Route::get('/revenue', [App\Http\Controllers\Admin\ReportController::class, 'revenue'])->name('revenue');
            Route::get('/vehicles', [App\Http\Controllers\Admin\ReportController::class, 'vehicles'])->name('vehicles');
            Route::get('/export', [App\Http\Controllers\Admin\ReportController::class, 'export'])->name('export');
        });

        // Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('index');
            Route::post('/update', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('update');
            Route::post('/clear-cache', [App\Http\Controllers\Admin\SettingsController::class, 'clearCache'])->name('clear-cache');
            Route::get('/system-info', [App\Http\Controllers\Admin\SettingsController::class, 'systemInfo'])->name('system-info');
        });
    });

});


///////////////////////////////////////////////////// User Routes \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes
require __DIR__.'/auth.php';

// Protected Routes
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // AI Analytics Dashboard
    Route::get('/ai-analytics/dashboard', [AiAnalyticsController::class, 'index'])->name('ai-analytics.dashboard'); 
    Route::get('/ai-analytics/export/{vehicle}', [AiAnalyticsController::class, 'exportPDF'])->name('ai-analytics.export');
    
    // Vehicles
    Route::get('/vehicles/decode-vin/{vin}', [\App\Http\Controllers\VehicleController::class, 'decodeVin'])->middleware(['auth'])->name('vehicles.decode-vin');

    Route::prefix('vehicles')->name('vehicles.')->group(function () {
        Route::get('/', [VehicleController::class, 'index'])->name('index');
        Route::get('/create', [VehicleController::class, 'create'])->name('create');
        Route::post('/', [VehicleController::class, 'store'])->name('store');
        Route::get('/{vehicle}', [VehicleController::class, 'show'])->name('show');
        Route::get('/{vehicle}/edit', [VehicleController::class, 'edit'])->name('edit');
        Route::put('/{vehicle}', [VehicleController::class, 'update'])->name('update');
        Route::delete('/{vehicle}', [VehicleController::class, 'destroy'])->name('destroy');
        Route::post('/{vehicle}/set-primary', [VehicleController::class, 'setPrimary'])->name('set-primary');
        Route::post('/{vehicle}/update-mileage', [VehicleController::class, 'updateMileage'])->name('update-mileage');

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

    Route::post('vehicles/{vehicle}/documents/extract', [VehicleDocumentController::class, 'extractData'])
        ->name('vehicles.documents.extract');
    
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

    // ── Job Posts (InDrive-style) ────────────────────────────────────────────
    Route::prefix('jobs')->name('jobs.')->group(function () {
        Route::get('/',                [JobPostController::class, 'index'])->name('index');
        Route::get('/create',          [JobPostController::class, 'create'])->name('create');
        Route::post('/',               [JobPostController::class, 'store'])->name('store');
        Route::get('/{job}',           [JobPostController::class, 'show'])->name('show');
        Route::post('/{job}/cancel',   [JobPostController::class, 'cancel'])->name('cancel');
        Route::post('/{job}/complete', [JobPostController::class, 'complete'])->name('complete');
        Route::post('/{job}/accept-offer/{offer}', [JobPostController::class, 'acceptOffer'])->name('accept-offer');
        Route::post('/{job}/rate', [JobPostController::class, 'rate'])->name('rate');
    });

    // Service Providers
    Route::prefix('providers')->name('providers.')->group(function () {
        Route::get('/', [ServiceProviderController::class, 'index'])->name('index');
        Route::get('/{provider}', [ServiceProviderController::class, 'show'])->name('show');
        Route::get('/nearby', [ServiceProviderController::class, 'searchNearby'])->name('search-nearby');
    });

    // Fuel Tracking
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
    
    // Trip Logs
    Route::prefix('trips')->name('trips.')->group(function () {
        Route::get('/',                  [TripLogController::class, 'index'])->name('index');
        Route::get('/create',            [TripLogController::class, 'create'])->name('create');
        Route::post('/',                 [TripLogController::class, 'store'])->name('store');
        Route::get('/export',            [TripLogController::class, 'export'])->name('export');
        Route::get('/{tripLog}/edit',    [TripLogController::class, 'edit'])->name('edit');
        Route::put('/{tripLog}',         [TripLogController::class, 'update'])->name('update');
        Route::delete('/{tripLog}',      [TripLogController::class, 'destroy'])->name('destroy');
    });
    
    // Insurance Management
    Route::prefix('insurance')->name('insurance.')->group(function () {
        Route::get('/', [InsuranceController::class, 'index'])->name('index');
        Route::get('/create', [InsuranceController::class, 'create'])->name('create');
        Route::post('/', [InsuranceController::class, 'store'])->name('store');
        Route::get('/{insurance}', [InsuranceController::class, 'show'])->name('show');
        Route::delete('/{insurance}', [InsuranceController::class, 'destroy'])->name('destroy');
    });
    
    // Recall Check
    Route::prefix('recalls')->name('recalls.')->group(function () {
        Route::get('/', [RecallController::class, 'index'])->name('index');
        Route::get('/{vehicle}/check', [RecallController::class, 'check'])->name('check');
        Route::post('/{vehicle}/sync', [RecallController::class, 'sync'])->name('sync');
    });
    
    // Vehicle Comparison
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
    
    // Alerts / Notifications
    Route::prefix('alerts')->name('alerts.')->group(function () {
        Route::get('/',          [AlertController::class, 'index'])->name('index');
        Route::get('/fetch',     [AlertController::class, 'fetch'])->name('fetch');
        Route::get('/counts',    [AlertController::class, 'counts'])->name('counts');   // ← NEW: sidebar badge counts
        Route::get('/{alert}/read',  [AlertController::class, 'markAsRead'])->name('mark-read');
        Route::post('/read-all', [AlertController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{alert}',[AlertController::class, 'destroy'])->name('destroy');
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


////////////////////////////////////////////// Provider Portal Routes \\

use App\Http\Controllers\Provider\AuthController    as ProviderAuthController;
use App\Http\Controllers\Provider\DashboardController as ProviderDashboardController;
use App\Http\Controllers\Provider\ServiceController   as ProviderServiceController;

Route::prefix('provider')->name('provider.')->group(function () {

    // Public auth
    Route::get('/login',  [ProviderAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [ProviderAuthController::class, 'login'])->name('login.submit');

    // Protected
    Route::middleware('provider')->group(function () {
        Route::post('/logout', [ProviderAuthController::class, 'logout'])->name('logout');

        // Dashboard
        Route::get('/dashboard', [ProviderDashboardController::class, 'index'])->name('dashboard');

        // Profile & Services
        Route::get('/profile',   [ProviderServiceController::class, 'editProfile'])->name('profile');
        Route::put('/profile',   [ProviderServiceController::class, 'updateProfile'])->name('profile.update');
        Route::get('/hours',     [ProviderServiceController::class, 'editHours'])->name('hours');
        Route::put('/hours',     [ProviderServiceController::class, 'updateHours'])->name('hours.update');
        Route::get('/analytics', [ProviderServiceController::class, 'analytics'])->name('analytics');

        // Job Board (InDrive-style)
        Route::prefix('jobs')->name('jobs.')->group(function () {
            Route::get('/',                    [\App\Http\Controllers\Provider\JobOfferController::class, 'index'])->name('index');
            Route::get('/my-offers',           [\App\Http\Controllers\Provider\JobOfferController::class, 'myOffers'])->name('my-offers');
            Route::get('/{job}',               [\App\Http\Controllers\Provider\JobOfferController::class, 'show'])->name('show');
            Route::post('/{job}/submit-offer', [\App\Http\Controllers\Provider\JobOfferController::class, 'submitOffer'])->name('submit-offer');
        });

        // My Work Queue
        Route::prefix('work')->name('jobs.work.')->group(function () {
            Route::get('/',                     [\App\Http\Controllers\Provider\JobWorkController::class, 'index'])->name('index');
            Route::get('/{job}',                [\App\Http\Controllers\Provider\JobWorkController::class, 'show'])->name('show');
            Route::post('/{job}/update-status', [\App\Http\Controllers\Provider\JobWorkController::class, 'updateStatus'])->name('update-status');
            Route::get('/{job}/complete',       [\App\Http\Controllers\Provider\JobWorkController::class, 'completeForm'])->name('complete-form');
            Route::post('/{job}/complete',      [\App\Http\Controllers\Provider\JobWorkController::class, 'completeSubmit'])->name('complete-submit');
        });

        // Service Records
        Route::prefix('service-records')->name('service-records.')->group(function () {
            Route::get('/',                           [\App\Http\Controllers\Provider\ServiceRecordController::class, 'index'])->name('index');
            Route::get('/create',                     [\App\Http\Controllers\Provider\ServiceRecordController::class, 'create'])->name('create');
            Route::post('/',                          [\App\Http\Controllers\Provider\ServiceRecordController::class, 'store'])->name('store');
            Route::get('/{serviceRecord}/edit',       [\App\Http\Controllers\Provider\ServiceRecordController::class, 'edit'])->name('edit');
            Route::put('/{serviceRecord}',            [\App\Http\Controllers\Provider\ServiceRecordController::class, 'update'])->name('update');
            Route::delete('/{serviceRecord}',         [\App\Http\Controllers\Provider\ServiceRecordController::class, 'destroy'])->name('destroy');
        });
    });
});

// ── Real-Time Polling Endpoints ──────────────────────────────────────────────
// Uses web middleware so Laravel session auth works with fetch() calls from Blade.
Route::middleware(['web', 'auth'])->prefix('api/realtime')->name('api.realtime.')->group(function () {
    Route::get('/jobs/live',              [\App\Http\Controllers\Api\JobRealTimeController::class, 'liveJobs'])->name('jobs.live');
    Route::get('/jobs/{job}/offers/live', [\App\Http\Controllers\Api\JobRealTimeController::class, 'liveOffers'])->name('offers.live');
    Route::get('/provider/offers/live',   [\App\Http\Controllers\Api\JobRealTimeController::class, 'liveProviderOffers'])->name('provider.offers.live');
});