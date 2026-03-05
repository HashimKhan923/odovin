<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\ServiceJobPost;
use App\Models\ServiceJobOffer;
use App\Models\ServiceRecord;
use App\Models\MaintenanceSchedule;
use Illuminate\Support\Facades\DB;
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

    // ─────────────────────────────────────────────────────────────────────────
    // Completion flow: GET shows combined form, POST saves everything
    // ─────────────────────────────────────────────────────────────────────────

    public function completeForm(ServiceJobPost $job)
    {
        $provider = $this->provider();

        // Must be accepted offer for this provider
        $offer = ServiceJobOffer::where('job_post_id', $job->id)
            ->where('service_provider_id', $provider->id)
            ->where('status', 'accepted')
            ->firstOrFail();

        // Only allow from in_progress
        abort_unless(($job->work_status ?? 'pending') === 'in_progress', 403, 'Job must be in progress to complete.');

        // Load existing service record if already created (re-edit scenario)
        $existingRecord = ServiceRecord::where('job_post_id', $job->id)->first();

        $job->load(['vehicle', 'user', 'acceptedOffer.serviceProvider']);

        return view('provider.jobs.complete', compact('job', 'offer', 'provider', 'existingRecord'));
    }

    public function completeSubmit(Request $request, ServiceJobPost $job)
    {
        $provider = $this->provider();

        $offer = ServiceJobOffer::where('job_post_id', $job->id)
            ->where('service_provider_id', $provider->id)
            ->where('status', 'accepted')
            ->firstOrFail();

        abort_unless(($job->work_status ?? 'pending') === 'in_progress', 403);

        $validated = $request->validate([
            // Job completion fields
            'final_cost'           => 'required|numeric|min:0.01',
            'provider_notes'       => 'nullable|string|max:1000',
            // Service record fields
            'service_type'         => 'required|string|max:255',
            'description'          => 'required|string|max:2000',
            'service_date'         => 'required|date',
            'mileage_at_service'   => 'nullable|integer|min:0',
            'invoice_number'       => 'nullable|string|max:100',
            'parts_replaced'       => 'nullable|string|max:1000',
            'notes'                => 'nullable|string|max:2000',
            'next_service_date'    => 'nullable|date',
            'next_service_mileage' => 'nullable|integer|min:0',
        ]);

        DB::transaction(function () use ($validated, $job, $provider) {

            // ── 1. Mark job complete ─────────────────────────────────────────────
            $job->update([
                'work_status'        => 'completed',
                'status'             => 'completed',
                'final_cost'         => $validated['final_cost'],
                'provider_notes'     => $validated['provider_notes'] ?? null,
                'work_completed_at'  => now(),
            ]);

            // ── 2. Upsert service record (create or update existing) ─────────────
            $partsArray = !empty($validated['parts_replaced'])
                ? array_values(array_filter(array_map('trim', explode(',', $validated['parts_replaced']))))
                : null;

            $record = ServiceRecord::updateOrCreate(
                ['job_post_id' => $job->id],
                [
                    'vehicle_id'           => $job->vehicle_id,
                    'service_provider_id'  => $provider->id,
                    'service_type'         => $validated['service_type'],
                    'description'          => $validated['description'],
                    'service_date'         => $validated['service_date'],
                    'mileage_at_service'   => $validated['mileage_at_service'] ?? null,
                    'cost'                 => $validated['final_cost'],
                    'invoice_number'       => $validated['invoice_number'] ?? null,
                    'parts_replaced'       => $partsArray,
                    'notes'                => $validated['notes'] ?? null,
                    'next_service_date'    => $validated['next_service_date'] ?? null,
                    'next_service_mileage' => $validated['next_service_mileage'] ?? null,
                ]
            );

            // ── 3. Update vehicle mileage ────────────────────────────────────────
            $vehicle = $job->vehicle;
            if ($vehicle && !empty($validated['mileage_at_service'])) {
                $vehicle->updateMileage((int) $validated['mileage_at_service']);
            }

            // ── 4. Upsert expense record ─────────────────────────────────────────
            if ($vehicle) {
                $expense = $record->expense;
                $expenseData = [
                    'category'         => 'maintenance',
                    'description'      => $validated['service_type'] . ' — ' . $validated['description'],
                    'amount'           => $validated['final_cost'],
                    'expense_date'     => $validated['service_date'],
                    'odometer_reading' => $validated['mileage_at_service'] ?? null,
                ];
                if ($expense) {
                    $expense->update($expenseData);
                } else {
                    $vehicle->expenses()->create(array_merge(
                        $expenseData,
                        ['service_record_id' => $record->id]
                    ));
                }
            }

            // ── 5. Maintenance: complete matching schedule, schedule next ────────
            if ($vehicle) {
                $this->handleMaintenanceOnComplete($vehicle, $validated);
            }

            // ── 6. Notify consumer + refresh provider rating ─────────────────────
            $job->refresh()->load(['user', 'vehicle', 'acceptedOffer.serviceProvider']);
            JobNotificationService::workStatusUpdated($job, 'completed');
            $this->refreshProviderRating($provider);
        });

        return redirect()->route('provider.jobs.work.show', $job)
            ->with('success', '🎉 Job completed! Service record, expenses, and maintenance all updated.');
    }

    protected function handleMaintenanceOnComplete(\App\Models\Vehicle $vehicle, array $data): void
    {
        $serviceType    = $data['service_type'];
        $hasNextDate    = !empty($data['next_service_date']);
        $hasNextMileage = !empty($data['next_service_mileage']);

        $schedule = MaintenanceSchedule::where('vehicle_id', $vehicle->id)
            ->where('service_type', $serviceType)
            ->whereIn('status', ['pending', 'overdue', 'scheduled'])
            ->orderByRaw("FIELD(status, 'overdue', 'pending', 'scheduled')")
            ->orderBy('due_date')
            ->first();

        if ($schedule) {
            $schedule->markCompleted(); // auto-creates next if recurring

            if ($hasNextDate || $hasNextMileage) {
                $next = MaintenanceSchedule::where('vehicle_id', $vehicle->id)
                    ->where('service_type', $serviceType)
                    ->where('status', 'pending')
                    ->latest()->first();

                if ($next) {
                    $updates = [];
                    if ($hasNextDate)    $updates['due_date']    = $data['next_service_date'];
                    if ($hasNextMileage) $updates['due_mileage'] = $data['next_service_mileage'];
                    $next->update($updates);
                }
            }
        } elseif ($hasNextDate || $hasNextMileage) {
            MaintenanceSchedule::create([
                'vehicle_id'   => $vehicle->id,
                'service_type' => $serviceType,
                'description'  => "Next {$serviceType} — scheduled after service on " .
                                   \Carbon\Carbon::parse($data['service_date'])->format('M d, Y'),
                'due_date'     => $hasNextDate    ? $data['next_service_date']    : null,
                'due_mileage'  => $hasNextMileage ? $data['next_service_mileage'] : null,
                'priority'     => 'medium',
                'status'       => 'pending',
                'is_recurring' => false,
            ]);
        }
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