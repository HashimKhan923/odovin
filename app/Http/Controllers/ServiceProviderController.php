<?php

namespace App\Http\Controllers;

use App\Models\ServiceProvider;
use Illuminate\Http\Request;

class ServiceProviderController extends Controller
{
    public function index(Request $request)
    {


        $query = ServiceProvider::active();

        

        if ($request->latitude && $request->longitude) {
            $lat    = $request->latitude;
            $lng    = $request->longitude;
            $radius = $request->radius ?? 25;

            $query->selectRaw("
                *,
                (3959 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )) AS distance
            ", [$lat, $lng, $lat]);

            $query->havingRaw('distance <= ?', [$radius]);
        } else {
            $query->select('*');
        }

        $query->when($request->type, fn($q, $type) => $q->where('type', $type));
        $query->when($request->verified, fn($q) => $q->verified());

        if ($request->min_rating) {
            $query->where('rating', '>=', $request->min_rating);
        }

        $query->when($request->search, function ($q, $search) {
            return $q->where(function ($query) use ($search) {
                $query->where('business_name', 'like', "%{$search}%")
                      ->orWhere('city', 'like', "%{$search}%")
                      ->orWhere('services_offered', 'like', "%{$search}%");
            });
        });


        $sort = $request->sort ?? 'rating';
        switch ($sort) {
            case 'distance':
                ($request->latitude && $request->longitude)
                    ? $query->orderBy('distance', 'asc')
                    : $query->orderByDesc('rating');
                break;
            case 'reviews': $query->orderByDesc('total_reviews'); break;
            case 'name':    $query->orderBy('business_name', 'asc'); break;
            default:        $query->orderByDesc('rating')->orderByDesc('is_verified');
        }

        $providers = $query->paginate(12)->withQueryString();

        $stats = [
            'total'    => ServiceProvider::active()->count(),
            'verified' => ServiceProvider::active()->verified()->count(),
            'types'    => ServiceProvider::active()->distinct('type')->count('type'),
        ];

        $types = [
            'ev_specialist' => 'EV Specialist',
            'mechanic'      => 'Mechanic',
            'dealership'    => 'Dealership',
            'body_shop'     => 'Body Shop',
            'detailing'     => 'Detailing',
            'towing'        => 'Towing',
            'other'         => 'Other',
        ];


        return view('providers.index', compact('providers', 'types', 'stats'));
    }

    public function show(ServiceProvider $provider)
    {
        $provider->load(['bookings' => function ($query) {
            $query->where('status', 'completed')
                  ->whereNotNull('rating')
                  ->latest()
                  ->limit(15);
        }]);

        $ratingBreakdown = [];
        for ($i = 5; $i >= 1; $i--) {
            $ratingBreakdown[$i] = $provider->bookings()
                ->where('status', 'completed')
                ->where('rating', $i)
                ->count();
        }
        $totalRated = array_sum($ratingBreakdown);

        $bookedSlots = $provider->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('scheduled_date', '>=', now())
            ->where('scheduled_date', '<=', now()->addDays(14))
            ->pluck('scheduled_date')
            ->map(fn($d) => $d->format('Y-m-d'))
            ->toArray();

        $userVehicles   = auth()->user()->vehicles()->active()->get();
        $servicesArray  = is_array($provider->services_offered)
            ? $provider->services_offered
            : array_filter(explode(',', $provider->services_offered ?? ''));

        return view('providers.show', compact(
            'provider', 'userVehicles', 'ratingBreakdown',
            'totalRated', 'bookedSlots', 'servicesArray'
        ));
    }

    public function searchNearby(Request $request)
    {
        $validated = $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius'    => 'nullable|numeric|min:1|max:100',
            'type'      => 'nullable|string',
        ]);

        $radius = $validated['radius'] ?? 25;

        $providers = ServiceProvider::active()
            ->selectRaw("
                *,
                (3959 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )) AS distance
            ", [$validated['latitude'], $validated['longitude'], $validated['latitude']])
            ->having('distance', '<=', $radius)
            ->when($request->type,     fn($q, $t) => $q->where('type', $t))
            ->when($request->verified, fn($q)      => $q->verified())
            ->orderBy('distance')
            ->get();

        return view('providers.nearby', compact('providers'));
    }
}