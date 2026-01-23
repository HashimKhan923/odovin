<?php
namespace App\Services;
class AlertService
{
    public function unread(int $userId, int $limit = 5)
    {
        return \App\Models\Alert::where('user_id', $userId)
            ->where('is_read', false)
            ->latest()
            ->limit($limit)
            ->get();
    }
}
