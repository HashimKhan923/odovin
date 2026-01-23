<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\FuelLog;
use App\Models\Expense;
use Carbon\Carbon;
use App\Models\ServiceBooking;
use App\Models\ServiceProvider;
use App\Models\VehicleRecall;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard.index', [
            'stats' => [
                'users_total'        => User::where('user_type', 'user')->count(),
                // 'users_active'       => User::where('user_type', 'user')->where('is_active', 1)->count(),

                'vehicles_total'     => Vehicle::count(),

                'providers_total'    => ServiceProvider::count(),
                // 'providers_active'   => ServiceProvider::where('status', 'active')->count(),

                'bookings_total'     => ServiceBooking::count(),

                'recalls_open'       => VehicleRecall::count(),
            ]
        ]);
    }
}