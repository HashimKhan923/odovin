<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| job-board         — public channel, all providers listen for new jobs
| job.{id}          — private channel, only the job owner (consumer)
| provider.{id}     — private channel, individual provider
| user.{id}         — private channel, any authenticated user (consumer alerts)
|
*/

// Public channel — any authenticated provider sees new job posts
Broadcast::channel('job-board', function () {
    return true;
});

// Private channel — only the job's owner (consumer) sees new offers
Broadcast::channel('job.{jobId}', function ($user, $jobId) {
    return \App\Models\ServiceJobPost::where('id', $jobId)
        ->where('user_id', $user->id)
        ->exists();
});

// Private channel — individual provider sees offer status changes
Broadcast::channel('provider.{providerId}', function ($user, $providerId) {
    return optional($user->serviceProvider)->id == $providerId;
});

// Private channel — any user (used for consumer real-time alert badge)
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});