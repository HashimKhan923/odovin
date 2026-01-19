<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\VehicleRecall;
use App\Models\Alert;
use Illuminate\Support\Facades\Http;

class NhtsaRecallService
{
    public function syncByVin(Vehicle $vehicle): int
    {
        if (!$vehicle->vin) {
            return 0;
        }

        $response = Http::get(
            'https://api.nhtsa.gov/recalls/recallsByVehicle',
            ['vin' => $vehicle->vin]
        );

        if (!$response->ok()) {
            return 0;
        }

        $results = $response->json('results', []);
        $newCount = 0;

        foreach ($results as $recall) {
            $record = VehicleRecall::firstOrCreate(
                [
                    'vehicle_id' => $vehicle->id,
                    'nhtsa_campaign_number' => $recall['NHTSACampaignNumber'],
                ],
                [
                    'component' => $recall['Component'] ?? null,
                    'summary' => $recall['Summary'] ?? null,
                    'consequence' => $recall['Consequence'] ?? null,
                    'remedy' => $recall['Remedy'] ?? null,
                    'report_received_date' => $recall['ReportReceivedDate'] ?? null,
                    'is_open' => ($recall['RecallStatus'] ?? '') === 'Open',
                ]
            );

            if ($record->wasRecentlyCreated) {
                $newCount++;

                Alert::create([
                    'user_id' => $vehicle->user_id,
                    'title' => 'Vehicle Recall Found',
                    'message' => 'A new safety recall was found for ' . $vehicle->full_name,
                ]);
            }
        }

        return $newCount;
    }
}
