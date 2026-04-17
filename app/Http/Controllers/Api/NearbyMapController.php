<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;

class NearbyMapController extends Controller
{
    public function __invoke(Request $request)
    {
        $lat    = (float) $request->lat;
        $lng    = (float) $request->lng;
        $radius = min((int) ($request->radius ?? \App\Models\AppSetting::int('default_nearby_radius_miles', 25)), \App\Models\AppSetting::int('max_radius_miles', 100));

        if (!$lat || !$lng) {
            return response()->json(['providers' => [], 'debug' => ['error' => 'no coords']]);
        }

        $providers = ServiceProvider::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->selectRaw("
                id, business_name, type, latitude, longitude,
                is_verified, is_active, rating, total_reviews,
                (3959 * acos(
                    LEAST(1, GREATEST(-1,
                        cos(radians(?)) * cos(radians(latitude)) *
                        cos(radians(longitude) - radians(?)) +
                        sin(radians(?)) * sin(radians(latitude))
                    ))
                )) AS distance
            ", [$lat, $lng, $lat])
            ->havingRaw('distance <= ?', [$radius])
            ->orderBy('distance')
            ->get()
            ->map(fn($p) => [
                'id'           => $p->id,
                'name'         => $p->business_name,
                'type'         => ucfirst(str_replace('_', ' ', $p->type ?? '')),
                'latitude'     => $p->latitude,
                'longitude'    => $p->longitude,
                'is_verified'  => (bool) $p->is_verified,
                'is_active'    => (bool) $p->is_active,
                'rating'       => $p->rating ? round($p->rating, 1) : 0,
                'total_reviews'=> $p->total_reviews ?? 0,
                'distance'     => round($p->distance, 1),
            ]);

        return response()->json([
            'providers' => $providers,
            'debug'     => ['lat' => $lat, 'lng' => $lng, 'radius' => $radius, 'total' => $providers->count()],
        ]);
    }
}