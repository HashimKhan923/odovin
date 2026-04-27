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
    ServiceHistoryController,
};

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;


////////////////////////////////////////////// Admin Routes \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::middleware('admin')->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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

        Route::prefix('vehicles')->name('vehicles.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\VehicleController::class, 'index'])->name('index');
            Route::get('/statistics', [App\Http\Controllers\Admin\VehicleController::class, 'statistics'])->name('statistics');
            Route::get('/{vehicle}', [App\Http\Controllers\Admin\VehicleController::class, 'show'])->name('show');
            Route::delete('/{vehicle}', [App\Http\Controllers\Admin\VehicleController::class, 'destroy'])->name('destroy');
        });

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

        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\BookingController::class, 'index'])->name('index');
            Route::get('/statistics', [App\Http\Controllers\Admin\BookingController::class, 'statistics'])->name('statistics');
            Route::get('/{booking}', [App\Http\Controllers\Admin\BookingController::class, 'show'])->name('show');
            Route::put('/{booking}/status', [App\Http\Controllers\Admin\BookingController::class, 'updateStatus'])->name('update-status');
            Route::delete('/{booking}', [App\Http\Controllers\Admin\BookingController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
            Route::get('/overview', [App\Http\Controllers\Admin\ReportController::class, 'overview'])->name('overview');
            Route::get('/users', [App\Http\Controllers\Admin\ReportController::class, 'users'])->name('users');
            Route::get('/revenue', [App\Http\Controllers\Admin\ReportController::class, 'revenue'])->name('revenue');
            Route::get('/vehicles', [App\Http\Controllers\Admin\ReportController::class, 'vehicles'])->name('vehicles');
            Route::get('/export', [App\Http\Controllers\Admin\ReportController::class, 'export'])->name('export');
        });

        Route::prefix('subscription-plans')->name('subscription-plans.')->group(function () {
            Route::get('/',                  [\App\Http\Controllers\Admin\SubscriptionPlanController::class, 'index'])    ->name('index');
            Route::put('/{plan}',            [\App\Http\Controllers\Admin\SubscriptionPlanController::class, 'update'])   ->name('update');
            Route::post('/{plan}/provision', [\App\Http\Controllers\Admin\SubscriptionPlanController::class, 'provision'])->name('provision');
        });

        Route::prefix('jobs')->name('jobs.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\JobPostController::class, 'index'])->name('index');
            Route::get('/{job}', [\App\Http\Controllers\Admin\JobPostController::class, 'show'])->name('show');
            Route::delete('/{job}', [\App\Http\Controllers\Admin\JobPostController::class, 'destroy'])->name('destroy');
            Route::post('/{job}/force-close', [\App\Http\Controllers\Admin\JobPostController::class, 'forceClose'])->name('force-close');
        });

        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/escrow',        [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('escrow');
            Route::get('/subscriptions', [\App\Http\Controllers\Admin\PaymentController::class, 'subscriptions'])->name('subscriptions');
        });

        // ── Admin: Dispute Resolution ─────────────────────────────────────────
        Route::prefix('disputes')->name('disputes.')->group(function () {
            Route::get('/',                    [\App\Http\Controllers\Admin\DisputeController::class, 'index'])  ->name('index');
            Route::get('/{dispute}',           [\App\Http\Controllers\Admin\DisputeController::class, 'show'])   ->name('show');
            Route::post('/{dispute}/assign',   [\App\Http\Controllers\Admin\DisputeController::class, 'assign']) ->name('assign');
            Route::post('/{dispute}/message',  [\App\Http\Controllers\Admin\DisputeController::class, 'message'])->name('message');
            Route::post('/{dispute}/resolve',  [\App\Http\Controllers\Admin\DisputeController::class, 'resolve'])->name('resolve');
        });

        // ── Admin: Certification Reviews ──────────────────────────────────────
        Route::prefix('certifications')->name('certifications.')->group(function () {
            Route::get('/',                         [\App\Http\Controllers\Admin\CertificationController::class, 'index'])  ->name('index');
            Route::get('/{certification}',          [\App\Http\Controllers\Admin\CertificationController::class, 'show'])   ->name('show');
            Route::post('/{certification}/approve', [\App\Http\Controllers\Admin\CertificationController::class, 'approve'])->name('approve');
            Route::post('/{certification}/reject',  [\App\Http\Controllers\Admin\CertificationController::class, 'reject']) ->name('reject');
        });

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/',            [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('index');
            Route::post('/update',     [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('update');
            Route::post('/clear-cache',[App\Http\Controllers\Admin\SettingsController::class, 'clearCache'])->name('clear-cache');
            Route::get('/system-info', [App\Http\Controllers\Admin\SettingsController::class, 'systemInfo'])->name('system-info');
        });
    });
});


///////////////////////////////////////////////////// User Routes \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

Route::get('/', function () {
    return view('welcome');
})->name('home');

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // AI Analytics
    Route::get('/ai-analytics/dashboard', [AiAnalyticsController::class, 'index'])->name('ai-analytics.dashboard');
    Route::get('/ai-analytics/export/{vehicle}', [AiAnalyticsController::class, 'exportPDF'])->name('ai-analytics.export');

    // Vehicles
    Route::get('/vehicles/decode-vin/{vin}', [VehicleController::class, 'decodeVin'])->name('vehicles.decode-vin');

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

    // ── Service History (consumer read-only view) ────────────────────────────
    Route::prefix('service-history')->name('service-history.')->group(function () {
        Route::get('/',            [ServiceHistoryController::class, 'index'])->name('index');
        Route::get('/diagnostics', [ServiceHistoryController::class, 'diagnostics'])->name('diagnostics');
 
        // ── Shareable Report Links — MUST be before /{record} wildcard ────────
        Route::prefix('report')->name('report.')->group(function () {
            Route::get('/',          [\App\Http\Controllers\ServiceReportController::class, 'index'])  ->name('index');
            Route::get('/create',    [\App\Http\Controllers\ServiceReportController::class, 'create']) ->name('create');
            Route::post('/',         [\App\Http\Controllers\ServiceReportController::class, 'store'])   ->name('store');
            Route::get('/{share}',   [\App\Http\Controllers\ServiceReportController::class, 'show'])   ->name('show');
            Route::delete('/{share}',[\App\Http\Controllers\ServiceReportController::class, 'destroy'])->name('destroy');
        });
 
        // Wildcard last — so it doesn't swallow /report, /diagnostics etc.
        Route::get('/{record}', [ServiceHistoryController::class, 'show'])->name('show');
    });

    // ── Quote Requests ────────────────────────────────────────────────────────
    Route::get('/providers/{provider}/quote',  [\App\Http\Controllers\QuoteRequestController::class, 'create'])->name('quotes.create');
    Route::post('/providers/{provider}/quote', [\App\Http\Controllers\QuoteRequestController::class, 'store']) ->name('quotes.store');

    Route::prefix('quotes')->name('quotes.')->group(function () {
        Route::get('/',                 [\App\Http\Controllers\QuoteRequestController::class, 'index'])  ->name('index');
        Route::get('/{quote}',          [\App\Http\Controllers\QuoteRequestController::class, 'show'])   ->name('show');
        Route::post('/{quote}/accept',  [\App\Http\Controllers\QuoteRequestController::class, 'accept']) ->name('accept');
        Route::post('/{quote}/decline', [\App\Http\Controllers\QuoteRequestController::class, 'decline'])->name('decline');
        Route::delete('/{quote}',       [\App\Http\Controllers\QuoteRequestController::class, 'destroy'])->name('destroy');
    });

    // ── Dispute Resolution ────────────────────────────────────────────────────
    Route::get('/jobs/{job}/dispute/create',             [\App\Http\Controllers\DisputeController::class, 'create'])          ->name('disputes.create');
    Route::post('/jobs/{job}/dispute',                   [\App\Http\Controllers\DisputeController::class, 'store'])           ->name('disputes.store');
    Route::get('/disputes',                              [\App\Http\Controllers\DisputeController::class, 'index'])           ->name('disputes.index');
    Route::get('/disputes/{dispute}',                    [\App\Http\Controllers\DisputeController::class, 'show'])            ->name('disputes.show');
    Route::post('/disputes/{dispute}/message',           [\App\Http\Controllers\DisputeController::class, 'message'])         ->name('disputes.message');
    Route::post('/disputes/{dispute}/provider-response', [\App\Http\Controllers\DisputeController::class, 'providerResponse'])->name('disputes.provider-response');

    // ── Job Posts ────────────────────────────────────────────────────────────
    Route::prefix('jobs')->name('jobs.')->group(function () {
        Route::get('/',                              [JobPostController::class, 'index'])->name('index');
        Route::get('/create',                        [JobPostController::class, 'create'])->name('create');
        Route::post('/',                             [JobPostController::class, 'store'])->name('store');
        Route::get('/vehicle-diagnostics',           [JobPostController::class, 'vehicleDiagnostics'])->name('vehicle-diagnostics');
        Route::get('/{job}',                         [JobPostController::class, 'show'])->name('show');
        Route::post('/{job}/cancel',                 [JobPostController::class, 'cancel'])->name('cancel');
        Route::post('/{job}/complete',               [JobPostController::class, 'complete'])->name('complete');
        Route::post('/{job}/accept-offer/{offer}',   [JobPostController::class, 'acceptOffer'])->name('accept-offer');
        Route::post('/{job}/rate',                   [JobPostController::class, 'rate'])->name('rate');
    });

    // ── Counter-Offers ────────────────────────────────────────────────────────
    Route::prefix('jobs/{job}/offers/{offer}/counter')->name('jobs.offers.counter.')->group(function () {
        Route::post('/',   [App\Http\Controllers\CounterOfferController::class, 'store'])  ->name('store');
        Route::delete('/', [App\Http\Controllers\CounterOfferController::class, 'destroy'])->name('destroy');
    });

    // ── Payment & Escrow ──────────────────────────────────────────────────────
    Route::prefix('jobs/{job}/payment')->name('jobs.payment.')->group(function () {
        Route::get('/',        [App\Http\Controllers\PaymentController::class, 'show'])        ->name('show');
        Route::post('/intent', [App\Http\Controllers\PaymentController::class, 'createIntent'])->name('intent');
        Route::post('/release',[App\Http\Controllers\PaymentController::class, 'release'])     ->name('release');
        Route::post('/refund', [App\Http\Controllers\PaymentController::class, 'refund'])      ->name('refund');
        Route::post('/sync',   [App\Http\Controllers\PaymentController::class, 'sync'])        ->name('sync');
    });

    // ── Service Providers (public listing) ───────────────────────────────────
    Route::prefix('providers')->name('providers.')->group(function () {
        Route::get('/',          [ServiceProviderController::class, 'index'])->name('index');
        Route::get('/{provider}',[ServiceProviderController::class, 'show'])->name('show');
        Route::get('/nearby',    [ServiceProviderController::class, 'searchNearby'])->name('search-nearby');
    });

    // Fuel Tracking
    Route::prefix('fuel')->name('fuel.')->group(function () {
        Route::get('/',                        [FuelLogController::class, 'index'])->name('index');
        Route::get('/create',                  [FuelLogController::class, 'create'])->name('create');
        Route::post('/',                       [FuelLogController::class, 'store'])->name('store');
        Route::delete('/{fuelLog}',            [FuelLogController::class, 'destroy'])->name('destroy');
        Route::get('/{fuelLog}/edit',          [FuelLogController::class, 'edit'])->name('edit');
        Route::put('/{fuelLog}',               [FuelLogController::class, 'update'])->name('update');
        Route::get('/import',                  [FuelLogController::class, 'importForm'])->name('import.form');
        Route::post('/import',                 [FuelLogController::class, 'import'])->name('import');
        Route::get('/export/csv/{vehicle_id?}',[FuelLogController::class, 'exportCsv'])->name('export.csv');
        Route::get('/export/pdf/{vehicle_id?}',[FuelLogController::class, 'exportPdf'])->name('export.pdf');
    });

    // Trip Logs
    Route::prefix('trips')->name('trips.')->group(function () {
        Route::get('/',               [TripLogController::class, 'index'])->name('index');
        Route::get('/create',         [TripLogController::class, 'create'])->name('create');
        Route::post('/',              [TripLogController::class, 'store'])->name('store');
        Route::get('/export',         [TripLogController::class, 'export'])->name('export');
        Route::get('/{tripLog}/edit', [TripLogController::class, 'edit'])->name('edit');
        Route::put('/{tripLog}',      [TripLogController::class, 'update'])->name('update');
        Route::delete('/{tripLog}',   [TripLogController::class, 'destroy'])->name('destroy');
    });

    // Insurance
    Route::prefix('insurance')->name('insurance.')->group(function () {
        Route::get('/',               [InsuranceController::class, 'index'])->name('index');
        Route::get('/create',         [InsuranceController::class, 'create'])->name('create');
        Route::post('/',              [InsuranceController::class, 'store'])->name('store');
        Route::get('/{insurance}',    [InsuranceController::class, 'show'])->name('show');
        Route::delete('/{insurance}', [InsuranceController::class, 'destroy'])->name('destroy');
    });

    // Recalls
    Route::prefix('recalls')->name('recalls.')->group(function () {
        Route::get('/',                    [RecallController::class, 'index'])->name('index');
        Route::get('/{vehicle}/check',     [RecallController::class, 'check'])->name('check');
        Route::post('/{vehicle}/sync',     [RecallController::class, 'sync'])->name('sync');
    });

    // Vehicle Comparison
    Route::prefix('comparison')->name('comparison.')->group(function () {
        Route::get('/',          [VehicleComparisonController::class, 'index'])->name('index');
        Route::post('/compare',  [VehicleComparisonController::class, 'compare'])->name('compare');
    });

    // Expenses
    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/',                        [ExpenseController::class, 'index'])->name('index');
        Route::get('/create',                  [ExpenseController::class, 'create'])->name('create');
        Route::post('/',                       [ExpenseController::class, 'store'])->name('store');
        Route::get('/{expense}',               [ExpenseController::class, 'show'])->name('show');
        Route::get('/{expense}/edit',          [ExpenseController::class, 'edit'])->name('edit');
        Route::put('/{expense}',               [ExpenseController::class, 'update'])->name('update');
        Route::delete('/{expense}',            [ExpenseController::class, 'destroy'])->name('destroy');
        Route::get('/category/{category}',     [ExpenseController::class, 'byCategory'])->name('by-category');
    });

    // Reminders
    Route::prefix('reminders')->name('reminders.')->group(function () {
        Route::get('/',                    [ReminderController::class, 'index'])->name('index');
        Route::get('/create',              [ReminderController::class, 'create'])->name('create');
        Route::post('/',                   [ReminderController::class, 'store'])->name('store');
        Route::get('/{reminder}',          [ReminderController::class, 'show'])->name('show');
        Route::get('/{reminder}/edit',     [ReminderController::class, 'edit'])->name('edit');
        Route::put('/{reminder}',          [ReminderController::class, 'update'])->name('update');
        Route::delete('/{reminder}',       [ReminderController::class, 'destroy'])->name('destroy');
        Route::post('/{reminder}/complete',[ReminderController::class, 'markComplete'])->name('complete');
    });

    // Alerts / Notifications
    Route::prefix('alerts')->name('alerts.')->group(function () {
        Route::get('/',              [AlertController::class, 'index'])->name('index');
        Route::get('/fetch',         [AlertController::class, 'fetch'])->name('fetch');
        Route::get('/counts',        [AlertController::class, 'counts'])->name('counts');
        Route::post('/{alert}/read', [AlertController::class, 'markAsRead'])->name('mark-read');
        Route::post('/read-all',     [AlertController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{alert}',    [AlertController::class, 'destroy'])->name('destroy');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/',                                    [ReportController::class, 'index'])->name('index');
        Route::get('/expense-summary',                     [ReportController::class, 'expenseSummary'])->name('expense-summary');
        Route::get('/maintenance-history',                 [ReportController::class, 'maintenanceHistory'])->name('maintenance-history');
        Route::get('/vehicle-analytics/{vehicleId}',       [ReportController::class, 'vehicleAnalytics'])->name('vehicle-analytics');
        Route::get('/export/{type}',                       [ReportController::class, 'export'])->name('export');
    });

    // Vehicle AI Insights
    Route::prefix('vehicles/{vehicle}/ai-insights')->name('vehicles.ai-insights.')->group(function () {
        Route::post('/generate', [VehicleAIInsightController::class, 'generate'])->name('generate');
    });

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',    [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/',  [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
});


////////////////////////////////////////////// Provider Portal Routes \\

use App\Http\Controllers\Provider\AuthController      as ProviderAuthController;
use App\Http\Controllers\Provider\DashboardController as ProviderDashboardController;
use App\Http\Controllers\Provider\ServiceController   as ProviderServiceController;
use App\Http\Controllers\Provider\ServiceRecordController;

Route::prefix('provider')->name('provider.')->group(function () {

    Route::get('/login',  [ProviderAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [ProviderAuthController::class, 'login'])->name('login.submit');

    Route::middleware('provider')->group(function () {
        Route::post('/logout', [ProviderAuthController::class, 'logout'])->name('logout');

        Route::get('/dashboard', [ProviderDashboardController::class, 'index'])->name('dashboard');

        Route::get('/profile',   [ProviderServiceController::class, 'editProfile'])->name('profile');
        Route::put('/profile',   [ProviderServiceController::class, 'updateProfile'])->name('profile.update');
        Route::get('/hours',     [ProviderServiceController::class, 'editHours'])->name('hours');
        Route::put('/hours',     [ProviderServiceController::class, 'updateHours'])->name('hours.update');
        Route::get('/analytics', [ProviderServiceController::class, 'analytics'])->name('analytics');

        // Job Board
        Route::prefix('jobs')->name('jobs.')->group(function () {
            Route::get('/',                    [\App\Http\Controllers\Provider\JobOfferController::class, 'index'])->name('index');
            Route::get('/my-offers',           [\App\Http\Controllers\Provider\JobOfferController::class, 'myOffers'])->name('my-offers');
            Route::get('/{job}',               [\App\Http\Controllers\Provider\JobOfferController::class, 'show'])->name('show');
            Route::post('/{job}/submit-offer', [\App\Http\Controllers\Provider\JobOfferController::class, 'submitOffer'])->name('submit-offer');
        });

        // Counter-Offers
        Route::prefix('offers/{offer}/counter')->name('counter.')->group(function () {
            Route::post('/accept', [App\Http\Controllers\Provider\CounterOfferController::class, 'accept'])->name('accept');
            Route::post('/reject', [App\Http\Controllers\Provider\CounterOfferController::class, 'reject'])->name('reject');
        });

        // Quote Management
        Route::prefix('quotes')->name('quotes.')->group(function () {
            Route::get('/',                 [\App\Http\Controllers\Provider\QuoteController::class, 'index'])  ->name('index');
            Route::get('/{quote}',          [\App\Http\Controllers\Provider\QuoteController::class, 'show'])   ->name('show');
            Route::post('/{quote}/respond', [\App\Http\Controllers\Provider\QuoteController::class, 'respond'])->name('respond');
            Route::post('/{quote}/decline', [\App\Http\Controllers\Provider\QuoteController::class, 'decline'])->name('decline');
        });

        // Payments & Stripe Onboarding
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/',               [App\Http\Controllers\Provider\PaymentController::class, 'index'])        ->name('index');
            Route::get('/onboard',        [App\Http\Controllers\Provider\PaymentController::class, 'onboard'])      ->name('onboard');
            Route::get('/onboard/return', [App\Http\Controllers\Provider\PaymentController::class, 'onboardReturn'])->name('onboard.return');
        });

        // Subscription
        Route::prefix('subscription')->name('subscription.')->group(function () {
            Route::get('/',               [\App\Http\Controllers\Provider\SubscriptionController::class, 'index'])        ->name('index');
            Route::post('/checkout',      [\App\Http\Controllers\Provider\SubscriptionController::class, 'checkout'])     ->name('checkout');
            Route::get('/success',        [\App\Http\Controllers\Provider\SubscriptionController::class, 'success'])      ->name('success');
            Route::get('/billing-portal', [\App\Http\Controllers\Provider\SubscriptionController::class, 'billingPortal'])->name('billing-portal');
        });

        // My Work Queue
        Route::prefix('work')->name('jobs.work.')->group(function () {
            Route::get('/',                     [\App\Http\Controllers\Provider\JobWorkController::class, 'index'])->name('index');
            Route::get('/{job}',                [\App\Http\Controllers\Provider\JobWorkController::class, 'show'])->name('show');
            Route::post('/{job}/update-status', [\App\Http\Controllers\Provider\JobWorkController::class, 'updateStatus'])->name('update-status');
            Route::get('/{job}/complete',       [\App\Http\Controllers\Provider\JobWorkController::class, 'completeForm'])->name('complete-form');
            Route::post('/{job}/complete',      [\App\Http\Controllers\Provider\JobWorkController::class, 'completeSubmit'])->name('complete-submit');
        });

        // Certifications
        Route::prefix('certifications')->name('certifications.')->group(function () {
            Route::get('/',                             [\App\Http\Controllers\Provider\CertificationController::class, 'index'])  ->name('index');
            Route::get('/create',                       [\App\Http\Controllers\Provider\CertificationController::class, 'create']) ->name('create');
            Route::post('/',                            [\App\Http\Controllers\Provider\CertificationController::class, 'store'])  ->name('store');
            Route::delete('/{certification}',           [\App\Http\Controllers\Provider\CertificationController::class, 'destroy'])->name('destroy');
            Route::patch('/{certification}/toggle',     [\App\Http\Controllers\Provider\CertificationController::class, 'toggle']) ->name('toggle');
        });

        // Service Records
        Route::prefix('service-records')->name('service-records.')->group(function () {
            Route::get('/',                     [ServiceRecordController::class, 'index'])->name('index');
            Route::get('/create',               [ServiceRecordController::class, 'create'])->name('create');
            Route::post('/',                    [ServiceRecordController::class, 'store'])->name('store');
            Route::get('/{serviceRecord}',      [ServiceRecordController::class, 'show'])->name('show');
            Route::get('/{serviceRecord}/edit', [ServiceRecordController::class, 'edit'])->name('edit');
            Route::put('/{serviceRecord}',      [ServiceRecordController::class, 'update'])->name('update');
            Route::delete('/{serviceRecord}',   [ServiceRecordController::class, 'destroy'])->name('destroy');
        });

        // Service Diagnostics
        Route::prefix('service-diagnostics')->name('service-diagnostics.')->group(function () {
            Route::get('/',                 [\App\Http\Controllers\Provider\ServiceDiagnosticController::class, 'index'])->name('index');
            Route::patch('/{issue}/status', [\App\Http\Controllers\Provider\ServiceDiagnosticController::class, 'updateStatus'])->name('updateStatus');
        });
    });
});

// ── Public API: Nearby Providers Map ────────────────────────────────────────
Route::middleware(['web', 'auth'])->get(
    '/api/providers/nearby-map',
    \App\Http\Controllers\Api\NearbyMapController::class
)->name('api.providers.nearby-map');

// ── Real-Time Polling Endpoints ──────────────────────────────────────────────
Route::middleware(['web', 'auth'])->prefix('api/realtime')->name('api.realtime.')->group(function () {
    Route::get('/jobs/live',              [\App\Http\Controllers\Api\JobRealTimeController::class, 'liveJobs'])->name('jobs.live');
    Route::get('/jobs/{job}/offers/live', [\App\Http\Controllers\Api\JobRealTimeController::class, 'liveOffers'])->name('offers.live');
    Route::get('/provider/offers/live',   [\App\Http\Controllers\Api\JobRealTimeController::class, 'liveProviderOffers'])->name('provider.offers.live');
});

// ── Stripe Webhook Endpoints ──────────────────────────────────────────────────
Route::post('/stripe/webhook',              App\Http\Controllers\StripeWebhookController::class)       ->name('stripe.webhook');
Route::post('/stripe/subscription-webhook', App\Http\Controllers\SubscriptionWebhookController::class)->name('stripe.subscription.webhook');

// ── Public: Shareable Service Report (no auth required) ──────────────────────
Route::get('/report/{token}',     [\App\Http\Controllers\ServiceReportController::class, 'public'])->name('service-history.report.public');
Route::get('/report/{token}/pdf', [\App\Http\Controllers\ServiceReportController::class, 'pdf'])   ->name('service-history.report.pdf');