<?php

namespace App\Http\Controllers;

use App\Models\Insurance;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class InsuranceController extends Controller
{
    public function index(Request $request)
    {
        $policies = Insurance::whereHas('vehicle', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with('vehicle')
            ->latest()
            ->get();

        $vehicles = $request->user()->vehicles;

        return view('insurance.index', compact('policies', 'vehicles'));
    }

    public function create()
    {
        $vehicles = auth()->user()->vehicles()->active()->get();
        return view('insurance.create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'provider' => 'required|string|max:255',
            'policy_number' => 'required|string|max:255',
            'coverage_type' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'premium_amount' => 'required|numeric|min:0',
            'payment_frequency' => 'required|in:monthly,quarterly,semi-annual,annual',
            'deductible' => 'nullable|numeric|min:0',
            'coverage_limits' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $vehicle = Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $insurance = Insurance::create($validated);

        // Create reminder for renewal
        $vehicle->reminders()->create([
            'type' => 'insurance',
            'title' => 'Insurance Renewal - ' . $validated['provider'],
            'description' => 'Policy ' . $validated['policy_number'] . ' expires soon',
            'due_date' => $validated['end_date'],
            'reminder_date' => date('Y-m-d', strtotime($validated['end_date'] . ' -30 days')),
            'priority' => 'high',
        ]);

        return redirect()
            ->route('insurance.index')
            ->with('success', 'Insurance policy added successfully!');
    }

    public function show(Insurance $insurance)
    {
        $this->authorize('view', $insurance->vehicle);
        return view('insurance.show', compact('insurance'));
    }
}