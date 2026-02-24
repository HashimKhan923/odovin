<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    protected function provider()
    {
        return Auth::user()->serviceProvider;
    }

    public function editProfile()
    {
        $provider = $this->provider();
        $types = [
            'ev_specialist' => 'EV Specialist',
            'mechanic'      => 'Mechanic',
            'dealership'    => 'Dealership',
            'body_shop'     => 'Body Shop',
            'detailing'     => 'Detailing',
            'towing'        => 'Towing',
            'other'         => 'Other',
        ];
        return view('provider.services.profile', compact('provider', 'types'));
    }

    public function updateProfile(Request $request)
    {
        $provider  = $this->provider();
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|max:255',
            'type'             => 'required|string',
            'phone'            => 'required|string|max:30',
            'address'          => 'required|string|max:500',
            'city'             => 'required|string|max:100',
            'state'            => 'required|string|max:100',
            'zip_code'         => 'required|string|max:20',
            'services_offered' => 'nullable|array',
            'description'      => 'nullable|string|max:2000',
        ]);

        // Name & email go to users table
        Auth::user()->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Everything else goes to service_providers table
        $provider->update([
            'type'             => $validated['type'],
            'phone'            => $validated['phone'],
            'address'          => $validated['address'],
            'city'             => $validated['city'],
            'state'            => $validated['state'],
            'zip_code'         => $validated['zip_code'],
            'services_offered' => $validated['services_offered'] ?? [],
            'description'      => $validated['description'] ?? null,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function editHours()
    {
        $provider = $this->provider();
        return view('provider.services.hours', compact('provider'));
    }

    public function updateHours(Request $request)
    {
        $provider  = $this->provider();
        $validated = $request->validate(['working_hours' => 'nullable|array']);
        $provider->update(['working_hours' => $validated['working_hours'] ?? null]);
        return back()->with('success', 'Working hours updated!');
    }

    public function analytics()
    {
        $provider = $this->provider();

        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $months[] = [
                'label'   => $m->format('M Y'),
                'revenue' => $provider->bookings()
                    ->where('status', 'completed')
                    ->whereYear('scheduled_date', $m->year)
                    ->whereMonth('scheduled_date', $m->month)
                    ->sum('final_cost'),
                'count' => $provider->bookings()
                    ->whereYear('scheduled_date', $m->year)
                    ->whereMonth('scheduled_date', $m->month)
                    ->count(),
            ];
        }

        $services = $provider->bookings()
            ->selectRaw('service_type, count(*) as count, avg(final_cost) as avg_cost')
            ->groupBy('service_type')
            ->orderByDesc('count')
            ->get();

        $ratingDist = [];
        for ($i = 5; $i >= 1; $i--) {
            $ratingDist[$i] = $provider->bookings()
                ->where('status', 'completed')->where('rating', $i)->count();
        }

        $total      = $provider->bookings()->count();
        $cancelled  = $provider->bookings()->where('status', 'cancelled')->count();
        $cancelRate = $total > 0 ? round(($cancelled / $total) * 100, 1) : 0;

        $avgDays = $provider->bookings()
            ->where('status', 'completed')
            ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
            ->value('avg_days');

        return view('provider.services.analytics', compact(
            'provider', 'months', 'services', 'ratingDist', 'cancelRate', 'avgDays'
        ));
    }
}