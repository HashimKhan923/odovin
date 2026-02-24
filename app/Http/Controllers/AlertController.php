<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
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
            ->paginate(20);

        return view('alerts.index', compact('alerts'));
    }

    public function fetch(Request $request)
    {
        $userId = $request->user()->id;

        $notifications = Alert::where('user_id', $userId)
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn($a) => [
                'id'         => $a->id,
                'title'      => $a->title,
                'message'    => $a->message,
                'type'       => $a->type,
                'priority'   => $a->priority,
                'color'      => $a->color,
                'icon'       => $a->icon,
                'action_url' => $a->action_url ?? null,
                'is_read'    => $a->is_read,
                'time'       => $a->created_at->diffForHumans(),
            ]);

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => Alert::where('user_id', $userId)->where('is_read', false)->count(),
        ]);
    }

    public function markAsRead(Alert $alert)
    { 
        // $this->authorize('update', $alert);

        $alert->markAsRead();

        return back();
    }

    public function markAllAsRead(Request $request)
    {
        Alert::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back()->with('success', 'All alerts marked as read!');
    }

    public function destroy(Alert $alert)
    {
        // $this->authorize('delete', $alert);

        $alert->delete();

        return back()->with('success', 'Alert deleted!');
    }
}