<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\JobEscrow;
use App\Services\EscrowService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoReleaseEscrows extends Command
{
    protected $signature   = 'escrow:auto-release';
    protected $description = 'Release held escrows where the 72-hour consumer confirmation window has passed';

    public function handle(EscrowService $service): void
    {
        $overdue = JobEscrow::overdue()->with('jobPost.assignedProvider')->get();

        if ($overdue->isEmpty()) {
            $this->info('No overdue escrows.');
            return;
        }

        $this->info("Found {$overdue->count()} overdue escrow(s). Releasing...");

        foreach ($overdue as $escrow) {
            try {
                $service->release($escrow);

                // Alert provider
                if ($escrow->jobPost->assignedProvider) {
                    Alert::create([
                        'user_id'      => $escrow->jobPost->assignedProvider->user_id,
                        'vehicle_id'   => $escrow->jobPost->vehicle_id,
                        'type'         => 'booking',
                        'title'        => 'Payment Auto-Released',
                        'message'      => "Payment for job #{$escrow->jobPost->job_number} was automatically released after 72 hours.",
                        'priority'     => 'success',
                        'for_provider' => true,
                    ]);
                }

                // Alert consumer
                Alert::create([
                    'user_id'      => $escrow->jobPost->user_id,
                    'vehicle_id'   => $escrow->jobPost->vehicle_id,
                    'type'         => 'booking',
                    'title'        => 'Payment Auto-Released',
                    'message'      => "Payment for job #{$escrow->jobPost->job_number} was automatically released to the provider after 72 hours.",
                    'priority'     => 'info',
                    'for_provider' => false,
                ]);

                $this->line("  ✓ Released escrow #{$escrow->id} (job #{$escrow->jobPost->job_number})");

            } catch (\Throwable $e) {
                $this->error("  ✗ Failed escrow #{$escrow->id}: {$e->getMessage()}");
                Log::error('[AutoRelease] Failed', ['escrow' => $escrow->id, 'error' => $e->getMessage()]);
            }
        }

        $this->info('Done.');
    }
}