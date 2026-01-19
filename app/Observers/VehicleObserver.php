<?php

namespace App\Observers;

use App\Models\Vehicle;
use App\Services\NhtsaRecallService;

class VehicleObserver
{
    public function created(Vehicle $vehicle): void
    {
        app(NhtsaRecallService::class)->syncByVin($vehicle);
    }

    public function updated(Vehicle $vehicle): void
    {
        if ($vehicle->wasChanged('vin')) {
            app(NhtsaRecallService::class)->syncByVin($vehicle);
        }
    }
}
