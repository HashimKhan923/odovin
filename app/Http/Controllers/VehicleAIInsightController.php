<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleAIInsight;
use App\Services\AI\VinInsightService;
use Illuminate\Http\Request;

class VehicleAIInsightController extends Controller
{
    public function generate(Vehicle $vehicle, VinInsightService $ai)
    {
        // Prevent regeneration if already exists
        if ($vehicle->aiInsight) {
            return response()->json($vehicle->aiInsight);
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

        $insight = VehicleAIInsight::create([
            'vehicle_id' => $vehicle->id,
            'summary' => $aiResponse['summary'] ?? null,
            'known_issues' => $aiResponse['known_issues'] ?? [],
            'maintenance_tips' => $aiResponse['maintenance_tips'] ?? [],
            'owner_tips' => $aiResponse['owner_tips'] ?? [],
            'cost_expectations' => $aiResponse['cost_expectations'] ?? [],
            'peace_of_mind_score' => $aiResponse['peace_of_mind_score'] ?? 70,
            'mileage_at_generation' => $vehicle->mileage,
            'generated_at' => now(),
        ]);

        return response()->json($insight);
    }
}
