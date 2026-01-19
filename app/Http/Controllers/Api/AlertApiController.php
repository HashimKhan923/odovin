<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Reminder;
use App\Models\Vehicle;
use App\Http\Resources\AlertResource;
use App\Http\Resources\ReminderResource;
use Illuminate\Http\Request;

class AlertApiController extends Controller
{
    public function index(Request $request)
    {
        $alerts = Alert::where('user_id', $request->user()->id)
            ->with('vehicle')
            ->when($request->type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->when($request->priority, function ($query, $priority) {
                return $query->where('priority', $priority);
            })
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => AlertResource::collection($alerts),
        ]);
    }

    public function unread(Request $request)
    {
        $alerts = Alert::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->with('vehicle')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => AlertResource::collection($alerts),
        ]);
    }

    public function markAsRead(Alert $alert)
    {
        $this->authorize('update', $alert);

        $alert->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Alert marked as read',
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        Alert::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All alerts marked as read',
        ]);
    }

    public function destroy(Alert $alert)
    {
        $this->authorize('delete', $alert);

        $alert->delete();

        return response()->json([
            'success' => true,
            'message' => 'Alert deleted successfully',
        ]);
    }

    // Reminders
    public function reminders(Request $request)
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
            ->get();

        return response()->json([
            'success' => true,
            'data' => ReminderResource::collection($reminders),
        ]);
    }

    public function storeReminder(Request $request)
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

        Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $reminder = Reminder::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Reminder created successfully',
            'data' => new ReminderResource($reminder->load('vehicle')),
        ], 201);
    }

    public function showReminder(Reminder $reminder)
    {
        $this->authorize('view', $reminder->vehicle);

        return response()->json([
            'success' => true,
            'data' => new ReminderResource($reminder->load('vehicle')),
        ]);
    }

    public function updateReminder(Request $request, Reminder $reminder)
    {
        $this->authorize('update', $reminder->vehicle);

        $validated = $request->validate([
            'type' => 'in:maintenance,registration,insurance,inspection,payment,custom',
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'date',
            'reminder_date' => 'date|before:due_date',
            'priority' => 'in:low,medium,high',
        ]);

        $reminder->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Reminder updated successfully',
            'data' => new ReminderResource($reminder),
        ]);
    }

    public function deleteReminder(Reminder $reminder)
    {
        $this->authorize('delete', $reminder->vehicle);

        $reminder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reminder deleted successfully',
        ]);
    }

    public function completeReminder(Reminder $reminder)
    {
        $this->authorize('update', $reminder->vehicle);

        $reminder->update(['is_completed' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Reminder marked as completed',
            'data' => new ReminderResource($reminder),
        ]);
    }
}
