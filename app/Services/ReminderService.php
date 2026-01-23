<?php
namespace App\Services;
class ReminderService
{
    public function upcoming($vehicleIds, int $limit = 5)
    {
        return \App\Models\Reminder::whereIn('vehicle_id', $vehicleIds)
            ->where('is_completed', false)
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->limit($limit)
            ->get();
    }
}
