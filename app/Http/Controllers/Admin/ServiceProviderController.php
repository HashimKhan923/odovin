<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceProvider;
use App\Models\ServiceBooking;
use Illuminate\Http\Request;

class ServiceProviderController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceProvider::withCount('bookings');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by service type
        if ($request->filled('service_type')) {
            $query->whereJsonContains('services_offered', $request->service_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $providers = $query->paginate(15)->withQueryString();

        return view('admin.providers.index', compact('providers'));
    }

    public function show(ServiceProvider $provider)
    {
        $provider->load(['bookings.user', 'bookings.vehicle']);
        
        $stats = [
            'total_bookings' => $provider->bookings()->count(),
            'completed_bookings' => $provider->bookings()->where('status', 'completed')->count(),
            'cancelled_bookings' => $provider->bookings()->where('status', 'cancelled')->count(),
            'pending_bookings' => $provider->bookings()->where('status', 'pending')->count(),
            'average_rating' => $provider->bookings()->whereNotNull('rating')->avg('rating'),
            'total_revenue' => $provider->bookings()->where('status', 'completed')->sum('price'),
        ];

        return view('admin.providers.show', compact('provider', 'stats'));
    }

    public function create()
    {
        return view('admin.providers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:service_providers,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'services_offered' => 'required|array',
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        ServiceProvider::create($validated);

        return redirect()->route('admin.providers.index')
            ->with('success', 'Service provider created successfully.');
    }

    public function edit(ServiceProvider $provider)
    {
        return view('admin.providers.edit', compact('provider'));
    }

    public function update(Request $request, ServiceProvider $provider)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:service_providers,email,' . $provider->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'services_offered' => 'required|array',
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $provider->update($validated);

        return redirect()->route('admin.providers.index')
            ->with('success', 'Service provider updated successfully.');
    }

    public function destroy(ServiceProvider $provider)
    {
        $provider->delete();

        return redirect()->route('admin.providers.index')
            ->with('success', 'Service provider deleted successfully.');
    }

    public function toggleStatus(ServiceProvider $provider)
    {
        $newStatus = $provider->status === 'active' ? 'inactive' : 'active';
        $provider->update(['status' => $newStatus]);

        return back()->with('success', 'Provider status updated successfully.');
    }
}