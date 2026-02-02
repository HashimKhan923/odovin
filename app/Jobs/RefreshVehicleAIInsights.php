<?php

namespace App\Jobs;

use App\Models\Vehicle;
use App\Services\AI\VinInsightService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshVehicleAIInsights implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(VinInsightService $ai)
    {
        Vehicle::with('aiInsight')
            ->whereNotNull('mileage')
            ->chunkById(50, function ($vehicles) use ($ai) {

                foreach ($vehicles as $vehicle) {
                    $insight = $vehicle->aiInsight;

                    if (!$insight) {
                        continue;
                    }

                    $mileageDiff = abs(
                        $vehicle->mileage - $insight->mileage_at_generation
                    );

                    $stale = $insight->generated_at
                        ->lt(now()->subDays(90));

                    if ($mileageDiff < 5000 && !$stale) {
                        continue;
                    }

                    $payload = [
                        'year' => $vehicle->year,
                        'make' => $vehicle->make,
                        'model' => $vehicle->model,
                        'engine' => $vehicle->engine,
                        'fuel' => $vehicle->fuel_type,
                        'current_mileage' => $vehicle->mileage,
                        'country' => 'US'
                    ];

                    $aiResponse = $ai->generate($payload);

                    $insight->update([
                        'summary' => $aiResponse['summary'] ?? $insight->summary,
                        'known_issues' => $aiResponse['known_issues'] ?? $insight->known_issues,
                        'maintenance_tips' => $aiResponse['maintenance_tips'] ?? $insight->maintenance_tips,
                        'owner_tips' => $aiResponse['owner_tips'] ?? $insight->owner_tips,
                        'cost_expectations' => $aiResponse['cost_expectations'] ?? $insight->cost_expectations,
                        'peace_of_mind_score' => $aiResponse['peace_of_mind_score'] ?? $insight->peace_of_mind_score,
                        'mileage_at_generation' => $vehicle->mileage,
                        'generated_at' => now(),
                    ]);
                }
            });
    }
}
