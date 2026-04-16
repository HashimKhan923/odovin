<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceJobPost;
use App\Models\JobEscrow;
use Illuminate\Http\Request;

class JobPostController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceJobPost::with(['user', 'vehicle', 'assignedProvider', 'offers', 'escrow']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('work_status')) {
            $query->where('work_status', $request->work_status);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('job_number', 'like', "%$s%")
                  ->orWhere('service_type', 'like', "%$s%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%$s%")
                      ->orWhere('email', 'like', "%$s%"));
            });
        }

        $jobs = $query->latest()->paginate(25)->withQueryString();

        $stats = [
            'total'        => ServiceJobPost::count(),
            'open'         => ServiceJobPost::where('status', 'open')->count(),
            'accepted'     => ServiceJobPost::where('status', 'accepted')->count(),
            'completed'    => ServiceJobPost::where('work_status', 'completed')->count(),
            'escrow_held'  => JobEscrow::where('status', 'held')->sum('amount'),
            'escrow_count' => JobEscrow::where('status', 'held')->count(),
        ];

        return view('admin.jobs.index', compact('jobs', 'stats'));
    }

    public function show(ServiceJobPost $job)
    {
        $job->load([
            'user', 'vehicle', 'assignedProvider',
            'offers.serviceProvider', 'escrow',
            'acceptedOffer.serviceProvider'
        ]);
        return view('admin.jobs.show', compact('job'));
    }

    public function destroy(ServiceJobPost $job)
    {
        $job->delete();
        return back()->with('success', 'Job post deleted.');
    }

    public function forceClose(ServiceJobPost $job)
    {
        $job->update(['status' => 'expired', 'work_status' => 'cancelled']);
        return back()->with('success', 'Job post force-closed.');
    }
}