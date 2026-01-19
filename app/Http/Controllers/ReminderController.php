<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        $reminders = Reminder::whereHas('vehicle', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with('vehicle')
            ->when($request->vehicle_id, function ($query, $vehicleId) {
                return $query->where('vehicle_id', $vehicleId);
            })
            ->when($request->type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->when($request->completed !== null, function ($query) use ($request) {
                return $query->where('is_completed', $request->completed);
            })
            ->orderBy('due_date')
            ->paginate(15);

        $vehicles = $request->user()->vehicles;

        return view('reminders.index', compact('reminders', 'vehicles'));
    }

    public function create()
    {
        $vehicles = auth()->user()->vehicles()->active()->get();
        
        $types = [
            'maintenance' => 'Maintenance',
            'registration' => 'Registration Renewal',
            'insurance' => 'Insurance Renewal',
            'inspection' => 'Vehicle Inspection',
            'payment' => 'Payment Due',
            'custom' => 'Custom Reminder',
        ];

        return view('reminders.create', compact('vehicles', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'type' => 'required|in:maintenance,registration,insurance,inspection,payment,custom',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'reminder_date' => 'required|date|before:due_date',
            'priority' => 'required|in:low,medium,high',
        ]);

        // Verify vehicle belongs to user
        Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $reminder = Reminder::create($validated);

        return redirect()
            ->route('reminders.index')
            ->with('success', 'Reminder created successfully!');
    }

    public function show(Reminder $reminder)
    {
        $this->authorize('view', $reminder->vehicle);

        $reminder->load('vehicle');

        return view('reminders.show', compact('reminder'));
    }

    public function edit(Reminder $reminder)
    {
        $this->authorize('update', $reminder->vehicle);

        $vehicles = auth()->user()->vehicles()->active()->get();
        
        $types = [
            'maintenance' => 'Maintenance',
            'registration' => 'Registration Renewal',
            'insurance' => 'Insurance Renewal',
            'inspection' => 'Vehicle Inspection',
            'payment' => 'Payment Due',
            'custom' => 'Custom Reminder',
        ];

        return view('reminders.edit', compact('reminder', 'vehicles', 'types'));
    }

    public function update(Request $request, Reminder $reminder)
    {
        $this->authorize('update', $reminder->vehicle);

        $validated = $request->validate([
            'type' => 'required|in:maintenance,registration,insurance,inspection,payment,custom',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'reminder_date' => 'required|date|before:due_date',
            'priority' => 'required|in:low,medium,high',
        ]);

        $reminder->update($validated);

        return redirect()
            ->route('reminders.index')
            ->with('success', 'Reminder updated successfully!');
    }

    public function destroy(Reminder $reminder)
    {
        $this->authorize('delete', $reminder->vehicle);

        $reminder->delete();

        return redirect()
            ->route('reminders.index')
            ->with('success', 'Reminder deleted successfully!');
    }

    public function markComplete(Reminder $reminder)
    {
        $this->authorize('update', $reminder->vehicle);

        $reminder->update(['is_completed' => true]);

        return back()->with('success', 'Reminder marked as completed!');
    }
}