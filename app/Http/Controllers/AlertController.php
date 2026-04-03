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
        $userId     = $request->user()->id;
        $isProvider = $request->boolean('provider'); // ?provider=1 from provider bell

        $query = Alert::where('user_id', $userId);

        // Provider bell only shows provider-facing alerts; consumer bell only consumer alerts
        if ($isProvider) {
            $query->where('for_provider', true);
        } else {
            $query->where(function ($q) {
                $q->where('for_provider', false)->orWhereNull('for_provider');
            });
        }

        $notifications = $query->latest()->limit(20)->get()
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

        $unreadCount = Alert::where('user_id', $userId)
            ->where('is_read', false)
            ->when($isProvider,
                fn($q) => $q->where('for_provider', true),
                fn($q) => $q->where(fn($q2) => $q2->where('for_provider', false)->orWhereNull('for_provider'))
            )
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
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
        $isProvider = $request->boolean('provider');

        $query = Alert::where('user_id', $request->user()->id)->where('is_read', false);

        if ($isProvider) {
            $query->where('for_provider', true);
        } else {
            $query->where(fn($q) => $q->where('for_provider', false)->orWhereNull('for_provider'));
        }

        $query->update(['is_read' => true, 'read_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('success', 'All alerts marked as read!');
    }

    public function destroy(Alert $alert)
    {
        // $this->authorize('delete', $alert);

        $alert->delete();

        return back()->with('success', 'Alert deleted!');
    }
}