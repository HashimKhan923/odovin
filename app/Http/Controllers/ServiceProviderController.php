<?php

namespace App\Http\Controllers;

use App\Models\ServiceProvider;
use Illuminate\Http\Request;

class ServiceProviderController extends Controller
{
    public function index(Request $request)
    {
        $providers = ServiceProvider::active()
            ->when($request->type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->when($request->verified, function ($query) {
                return $query->verified();
            })
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('city', 'like', "%{$search}%")
                      ->orWhere('services_offered', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('rating')
            ->orderByDesc('is_verified')
            ->paginate(12);

        $types = [
            'mechanic' => 'Mechanic',
            'dealership' => 'Dealership',
            'body_shop' => 'Body Shop',
            'detailing' => 'Detailing',
            'towing' => 'Towing',
            'other' => 'Other',
        ];

        return view('providers.index', compact('providers', 'types'));
    }

    public function show(ServiceProvider $provider)
    {
        $provider->load(['bookings' => function ($query) {
            $query->where('status', 'completed')
                  ->whereNotNull('rating')
                  ->latest()
                  ->limit(10);
        }]);

        $userVehicles = auth()->user()->vehicles()->active()->get();

        return view('providers.show', compact('provider', 'userVehicles'));
    }

    public function searchNearby(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:100',
            'type' => 'nullable|string',
        ]);

        $radius = $validated['radius'] ?? 25; // miles

        // Haversine formula for distance calculation
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
            ->when($request->type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->orderBy('distance')
            ->get();

        return view('providers.nearby', compact('providers'));
    }
}