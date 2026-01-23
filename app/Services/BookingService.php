<?php

namespace App\Services;

use App\Models\ServiceBooking;

class BookingService
{
    public function recent(int $userId, int $limit = 5)
    {
        return ServiceBooking::where('user_id', $userId)
            ->with(['vehicle', 'serviceProvider'])
            ->latest()
            ->limit($limit)
            ->get();
    }
}
