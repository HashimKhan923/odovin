<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class VinDecoderService
{
    protected $apiUrl = 'https://vpic.nhtsa.dot.gov/api/vehicles/DecodeVin';

    public function decode(string $vin): array
    {
        // Cache VIN data for 30 days
        return Cache::remember("vin_{$vin}", 60 * 60 * 24 * 30, function () use ($vin) {
            try {
                $response = Http::get("{$this->apiUrl}/{$vin}", [
                    'format' => 'json'
                ]);

                if (!$response->successful()) {
                    throw new \Exception('VIN decoder API request failed');
                }

                $data = $response->json();
                
                return $this->parseVinData($data['Results'] ?? []);
            } catch (\Exception $e) {
                // Return empty data if API fails
                return $this->getEmptyData();
            }
        });
    }

    protected function parseVinData(array $results): array
    {
        $data = [
            'make' => $this->findValue($results, 'Make'),
            'model' => $this->findValue($results, 'Model'),
            'year' => $this->findValue($results, 'Model Year'),
            'trim' => $this->findValue($results, 'Trim'),
            'engine' => $this->findValue($results, 'Engine Model'),
            'transmission' => $this->findValue($results, 'Transmission Style'),
            'fuel_type' => $this->findValue($results, 'Fuel Type - Primary'),
            'specifications' => [
                'body_class' => $this->findValue($results, 'Body Class'),
                'doors' => $this->findValue($results, 'Doors'),
                'drive_type' => $this->findValue($results, 'Drive Type'),
                'vehicle_type' => $this->findValue($results, 'Vehicle Type'),
                'plant_city' => $this->findValue($results, 'Plant City'),
                'plant_country' => $this->findValue($results, 'Plant Country'),
                'manufacturer' => $this->findValue($results, 'Manufacturer Name'),
            ],
        ];

        return $data;
    }

    protected function findValue(array $results, string $variableName): ?string
    {
        foreach ($results as $result) {
            if (isset($result['Variable']) && $result['Variable'] === $variableName) {
                return $result['Value'] ?? null;
            }
        }
        return null;
    }

    protected function getEmptyData(): array
    {
        return [
            'make' => null,
            'model' => null,
            'year' => null,
            'trim' => null,
            'engine' => null,
            'transmission' => null,
            'fuel_type' => null,
            'specifications' => [],
        ];
    }
}