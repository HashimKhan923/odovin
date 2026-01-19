<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceProvider;
use App\Http\Resources\ServiceProviderResource;
use Illuminate\Http\Request;

class ServiceProviderApiController extends Controller
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
            ->get();

        return response()->json([
            'success' => true,
            'data' => ServiceProviderResource::collection($providers),
        ]);
    }

    public function show(ServiceProvider $provider)
    {
        $provider->load(['bookings' => function ($query) {
            $query->where('status', 'completed')
                  ->whereNotNull('rating')
                  ->latest()
                  ->limit(10);
        }]);

        return response()->json([
            'success' => true,
            'data' => new ServiceProviderResource($provider),
        ]);
    }

    public function nearby(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:100',
            'type' => 'nullable|string',
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
            ->when($request->type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->orderBy('distance')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ServiceProviderResource::collection($providers),
        ]);
    }

    public function byType(Request $request, string $type)
    {
        $providers = ServiceProvider::active()
            ->where('type', $type)
            ->when($request->verified, function ($query) {
                return $query->verified();
            })
            ->orderByDesc('rating')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ServiceProviderResource::collection($providers),
        ]);
    }
}