<?php

namespace App\Services;

use App\Models\MaintenanceSchedule;
use Illuminate\Support\Collection;

class MaintenanceService
{
    public function upcoming(Collection $vehicleIds, int $limit = 5)
    {
        return MaintenanceSchedule::whereIn('vehicle_id', $vehicleIds)
            ->whereIn('status', ['pending', 'overdue'])
            ->with('vehicle')
            ->orderBy('due_date')
            ->limit($limit)
            ->get();
    }
}
