<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index(Request $request)
    {
        $alerts = Alert::where('user_id', $request->user()->id)
            ->where('for_provider', 0)
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
        $forProvider = $request->boolean('provider'); // provider layout sends ?provider=1

        $notifications = Alert::where('user_id', $userId)
            ->where('for_provider', $forProvider)
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn($a) => [
                'id'         => $a->id,
                'title'      => $a->title,
                'message'    => $a->message,
                'type'       => $a->type,
                'priority'   => $a->priority,
                'color'      => $a->color ?? null,
                'icon'       => $a->icon ?? null,
                'action_url' => $a->action_url ?? null,
                'is_read'    => $a->is_read,
                'time'       => $a->created_at->diffForHumans(),
            ]);

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => Alert::where('user_id', $userId)
                                    ->where('for_provider', $forProvider)
                                    ->where('is_read', false)
                                    ->count(),
        ]);
    }


    /**
     * Returns live sidebar badge counts.
     * GET /alerts/counts?provider=1
     */
    public function counts(Request $request)
    {
        $userId      = $request->user()->id;
        $forProvider = $request->boolean('provider');

        $data = [
            'unread_count' => Alert::where('user_id', $userId)
                                   ->where('for_provider', $forProvider)
                                   ->where('is_read', false)
                                   ->count(),
        ];

        if ($forProvider) {
            // Open jobs count for provider sidebar badge
            $data['open_jobs_count'] = \App\Models\ServiceJobPost::open()->count();
        } else {
            // Active jobs count for consumer sidebar badge
            $data['active_jobs_count'] = \App\Models\ServiceJobPost::where('user_id', $userId)
                ->whereIn('status', ['open', 'accepted'])
                ->count();
        }

        return response()->json($data);
    }

    public function markAsRead(Alert $alert)
    {
        $alert->markAsRead();
        return back();
    }

    public function markAllAsRead(Request $request)
    {
        $forProvider = $request->boolean('provider');

        Alert::where('user_id', $request->user()->id)
            ->where('for_provider', $forProvider)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'All alerts marked as read!');
    }

    public function destroy(Alert $alert)
    {
        $alert->delete();
        return back()->with('success', 'Alert deleted!');
    }
}