<?php
// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController,
    VehicleApiController,
    MaintenanceApiController,
    BookingApiController,
    ExpenseApiController,
    ServiceProviderApiController,
    AlertApiController,
    DashboardApiController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| All routes return JSON responses
| Authentication: Sanctum Bearer Token
|--------------------------------------------------------------------------
*/

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

// Public VIN Decoder (no auth required)
Route::get('/vehicles/decode-vin/{vin}', [VehicleApiController::class, 'decodeVin']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::put('/user/password', [AuthController::class, 'updatePassword']);
    
    // Dashboard
    Route::get('/dashboard', [DashboardApiController::class, 'index']);
    Route::get('/dashboard/stats', [DashboardApiController::class, 'stats']);
    Route::get('/dashboard/charts', [DashboardApiController::class, 'charts']);
    
    // Vehicles
    Route::prefix('vehicles')->group(function () {
        Route::get('/', [VehicleApiController::class, 'index']);
        Route::post('/', [VehicleApiController::class, 'store']);
        Route::get('/{vehicle}', [VehicleApiController::class, 'show']);
        Route::put('/{vehicle}', [VehicleApiController::class, 'update']);
        Route::delete('/{vehicle}', [VehicleApiController::class, 'destroy']);
        Route::post('/{vehicle}/set-primary', [VehicleApiController::class, 'setPrimary']);
        Route::post('/{vehicle}/update-mileage', [VehicleApiController::class, 'updateMileage']);
        Route::get('/{vehicle}/statistics', [VehicleApiController::class, 'statistics']);
        Route::get('/decode-vin/{vin}', [VehicleApiController::class, 'decodeVin']);
        
        // Vehicle Documents
        Route::prefix('{vehicle}/documents')->group(function () {
            Route::get('/', [VehicleApiController::class, 'documents']);
            Route::post('/', [VehicleApiController::class, 'storeDocument']);
            Route::get('/{document}', [VehicleApiController::class, 'showDocument']);
            Route::put('/{document}', [VehicleApiController::class, 'updateDocument']);
            Route::delete('/{document}', [VehicleApiController::class, 'deleteDocument']);
        });
    });
    
    // Maintenance
    Route::prefix('maintenance')->group(function () {
        Route::get('/', [MaintenanceApiController::class, 'index']);
        Route::post('/', [MaintenanceApiController::class, 'store']);
        Route::get('/{maintenance}', [MaintenanceApiController::class, 'show']);
        Route::put('/{maintenance}', [MaintenanceApiController::class, 'update']);
        Route::delete('/{maintenance}', [MaintenanceApiController::class, 'destroy']);
        Route::post('/{maintenance}/complete', [MaintenanceApiController::class, 'complete']);
        Route::get('/upcoming', [MaintenanceApiController::class, 'upcoming']);
        Route::get('/overdue', [MaintenanceApiController::class, 'overdue']);
        
        // Service Records
        Route::prefix('records')->group(function () {
            Route::get('/', [MaintenanceApiController::class, 'records']);
            Route::post('/', [MaintenanceApiController::class, 'storeRecord']);
            Route::get('/{record}', [MaintenanceApiController::class, 'showRecord']);
            Route::put('/{record}', [MaintenanceApiController::class, 'updateRecord']);
            Route::delete('/{record}', [MaintenanceApiController::class, 'deleteRecord']);
        });
    });
    
    // Service Bookings
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingApiController::class, 'index']);
        Route::post('/', [BookingApiController::class, 'store']);
        Route::get('/{booking}', [BookingApiController::class, 'show']);
        Route::put('/{booking}', [BookingApiController::class, 'update']);
        Route::delete('/{booking}', [BookingApiController::class, 'destroy']);
        Route::post('/{booking}/cancel', [BookingApiController::class, 'cancel']);
        Route::post('/{booking}/confirm', [BookingApiController::class, 'confirm']);
        Route::post('/{booking}/complete', [BookingApiController::class, 'complete']);
        Route::post('/{booking}/rate', [BookingApiController::class, 'rate']);
    });
    
    // Service Providers
    Route::prefix('providers')->group(function () {
        Route::get('/', [ServiceProviderApiController::class, 'index']);
        Route::get('/{provider}', [ServiceProviderApiController::class, 'show']);
        Route::get('/search/nearby', [ServiceProviderApiController::class, 'nearby']);
        Route::get('/search/by-type/{type}', [ServiceProviderApiController::class, 'byType']);
    });
    
    // Expenses
    Route::prefix('expenses')->group(function () {
        Route::get('/', [ExpenseApiController::class, 'index']);
        Route::post('/', [ExpenseApiController::class, 'store']);
        Route::get('/{expense}', [ExpenseApiController::class, 'show']);
        Route::put('/{expense}', [ExpenseApiController::class, 'update']);
        Route::delete('/{expense}', [ExpenseApiController::class, 'destroy']);
        Route::get('/summary/by-category', [ExpenseApiController::class, 'byCategory']);
        Route::get('/summary/by-vehicle', [ExpenseApiController::class, 'byVehicle']);
        Route::get('/summary/by-month', [ExpenseApiController::class, 'byMonth']);
    });
    
    // Reminders
    Route::prefix('reminders')->group(function () {
        Route::get('/', [AlertApiController::class, 'reminders']);
        Route::post('/', [AlertApiController::class, 'storeReminder']);
        Route::get('/{reminder}', [AlertApiController::class, 'showReminder']);
        Route::put('/{reminder}', [AlertApiController::class, 'updateReminder']);
        Route::delete('/{reminder}', [AlertApiController::class, 'deleteReminder']);
        Route::post('/{reminder}/complete', [AlertApiController::class, 'completeReminder']);
    });
    
    // Alerts
    Route::prefix('alerts')->group(function () {
        Route::get('/', [AlertApiController::class, 'index']);
        Route::get('/unread', [AlertApiController::class, 'unread']);
        Route::post('/{alert}/read', [AlertApiController::class, 'markAsRead']);
        Route::post('/read-all', [AlertApiController::class, 'markAllAsRead']);
        Route::delete('/{alert}', [AlertApiController::class, 'destroy']);
    });
    
    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/expense-summary', [ExpenseApiController::class, 'expenseSummary']);
        Route::get('/maintenance-history', [MaintenanceApiController::class, 'maintenanceHistory']);
        Route::get('/vehicle-analytics/{vehicle}', [VehicleApiController::class, 'analytics']);
    });
});