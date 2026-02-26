<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\ServiceJobPost;
use App\Models\ServiceJobOffer;
use App\Models\ServiceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\JobNotificationService;

class JobWorkController extends Controller
{
    protected function provider()
    {
        return Auth::user()->serviceProvider;
    }

    /**
     * My Work Queue — all accepted jobs for this provider.
     */
    public function index(Request $request)
    {
        $provider = $this->provider();

        $offers = ServiceJobOffer::where('service_provider_id', $provider->id)
            ->where('status', 'accepted')
            ->with(['jobPost.vehicle', 'jobPost.user'])
            ->when($request->work_status, fn($q, $ws) =>
                $q->whereHas('jobPost', fn($jq) => $jq->where('work_status', $ws))
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'pending'     => $this->countByWorkStatus($provider->id, 'pending'),
            'confirmed'   => $this->countByWorkStatus($provider->id, 'confirmed'),
            'in_progress' => $this->countByWorkStatus($provider->id, 'in_progress'),
            'completed'   => $this->countByWorkStatus($provider->id, 'completed'),
            'total_earned'=> ServiceJobPost::whereHas('offers', fn($q) =>
                                $q->where('service_provider_id', $provider->id)->where('status', 'accepted'))
                            ->where('work_status', 'completed')->sum('final_cost'),
        ];

        return view('provider.jobs.work-index', compact('provider', 'offers', 'stats'));
    }

    private function countByWorkStatus(int $providerId, string $status): int
    {
        return ServiceJobOffer::where('service_provider_id', $providerId)
            ->where('status', 'accepted')
            ->whereHas('jobPost', fn($q) => $q->where('work_status', $status))
            ->count();
    }

    /**
     * Show detail of a specific accepted job + status update form.
     */
    public function show(ServiceJobPost $job)
    {
        $provider = $this->provider();

        $offer = ServiceJobOffer::where('job_post_id', $job->id)
            ->where('service_provider_id', $provider->id)
            ->where('status', 'accepted')
            ->firstOrFail();

        $job->load(['vehicle', 'user']);

        return view('provider.jobs.work-show', compact('provider', 'job', 'offer'));
    }

    /**
     * Update the work status of a job.
     * Lifecycle: pending → confirmed → in_progress → completed (or → cancelled)
     */
    public function updateStatus(Request $request, ServiceJobPost $job)
    {
        $provider = $this->provider();

        $offer = ServiceJobOffer::where('job_post_id', $job->id)
            ->where('service_provider_id', $provider->id)
            ->where('status', 'accepted')
            ->firstOrFail();

        $validated = $request->validate([
            'work_status'    => 'required|in:confirmed,in_progress,completed,cancelled',
            'provider_notes' => 'nullable|string|max:1000',
            'final_cost'     => 'nullable|numeric|min:0',
        ]);

        // Guard valid transitions
        $current = $job->work_status ?? 'pending';
        $new     = $validated['work_status'];

        $allowed = match ($current) {
            'pending'     => ['confirmed', 'cancelled'],
            'confirmed'   => ['in_progress', 'cancelled'],
            'in_progress' => ['completed', 'cancelled'],
            default       => [],
        };

        if (!in_array($new, $allowed)) {
            return back()->with('error', "Cannot move from '{$current}' to '{$new}'.");
        }

        // Require final_cost when completing
        if ($new === 'completed') {
            $request->validate(['final_cost' => 'required|numeric|min:1']);
        }

        $updateData = ['work_status' => $new];

        if (!empty($validated['provider_notes'])) {
            $updateData['provider_notes'] = $validated['provider_notes'];
        }

        if ($new === 'in_progress')  $updateData['work_started_at']   = now();
        if ($new === 'completed')    $updateData['work_completed_at']  = now();
        if ($new === 'completed')    $updateData['final_cost']         = $validated['final_cost'];
        if ($new === 'completed')    $updateData['status']             = 'completed'; // close the post
        if ($new === 'cancelled')    $updateData['status']             = 'cancelled';

        $job->update($updateData);
        $job->refresh()->load(['user', 'vehicle', 'acceptedOffer.serviceProvider']);

        // Notify consumer about status change
        JobNotificationService::workStatusUpdated($job, $new);

        // Generate revenue record on completion
        if ($new === 'completed') {
            $this->generateRevenueRecord($job, $provider);
            // Update provider's overall rating from all job ratings
            $this->refreshProviderRating($provider);
        }

        return redirect()->route('provider.jobs.work.show', $job)
            ->with('success', $this->successMessage($new));
    }

    private function generateRevenueRecord(ServiceJobPost $job, $provider): void
    {
        // Avoid duplicates
        if (ServiceRecord::where('job_post_id', $job->id)->exists()) return;

        ServiceRecord::create([
            'vehicle_id'          => $job->vehicle_id,
            'service_provider_id' => $provider->id,
            'job_post_id'         => $job->id,
            'service_type'        => $job->service_type,
            'description'         => $job->description,
            'service_date'        => $job->work_completed_at ?? now(),
            'cost'                => $job->final_cost,
            'notes'               => $job->provider_notes,
            'mileage_at_service'  => null,
        ]);
    }

    private function refreshProviderRating($provider): void
    {
        // Average ratings from both bookings AND job posts
        $bookingAvg = $provider->bookings()->whereNotNull('rating')->avg('rating') ?? 0;
        $bookingCnt = $provider->bookings()->whereNotNull('rating')->count();

        $jobAvg = ServiceJobPost::whereHas('offers', fn($q) =>
            $q->where('service_provider_id', $provider->id)->where('status', 'accepted'))
            ->whereNotNull('rating')->avg('rating') ?? 0;
        $jobCnt = ServiceJobPost::whereHas('offers', fn($q) =>
            $q->where('service_provider_id', $provider->id)->where('status', 'accepted'))
            ->whereNotNull('rating')->count();

        $totalCnt = $bookingCnt + $jobCnt;
        $avgRating = $totalCnt > 0
            ? (($bookingAvg * $bookingCnt) + ($jobAvg * $jobCnt)) / $totalCnt
            : 0;

        $provider->update([
            'rating'        => round($avgRating, 2),
            'total_reviews' => $totalCnt,
        ]);
    }

    private function successMessage(string $status): string
    {
        return match ($status) {
            'confirmed'   => '✅ Job confirmed! The customer has been notified.',
            'in_progress' => '🔧 Job marked as in progress. Customer notified.',
            'completed'   => '🎉 Job completed! Revenue recorded. Customer can now leave a review.',
            'cancelled'   => '❌ Job cancelled. Customer has been notified.',
            default       => 'Status updated.',
        };
    }
}