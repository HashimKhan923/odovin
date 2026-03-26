<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\ServiceJobPost;
use App\Models\ServiceJobOffer;
use App\Models\ServiceProvider;

class JobNotificationService
{
    /**
     * When a consumer posts a job, alert all nearby active providers.
     * for_provider = true — only shows in provider dashboard bell.
     */
    public static function notifyNearbyProviders(ServiceJobPost $job): void
    {
        if (!$job->latitude || !$job->longitude) return;

        $lat    = $job->latitude;
        $lng    = $job->longitude;
        $radius = $job->radius ?? 25;

        $providers = ServiceProvider::active()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->selectRaw("
                *,
                (3959 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )) AS distance
            ", [$lat, $lng, $lat])
            ->havingRaw('distance <= ?', [$radius])
            ->get();

        foreach ($providers as $provider) {
            if (!$provider->user_id) continue;
            Alert::create([
                'user_id'      => $provider->user_id,
                'type'         => 'booking',
                'title'        => '🔔 New Job Near You',
                'message'      => "A customer needs {$job->service_type} within " . round($provider->distance, 1) . " miles. Budget: {$job->budgetLabel()}.",
                'action_url'   => route('provider.jobs.show', $job),
                'priority'     => 'warning',
                'for_provider' => true,  // ← provider only
            ]);
        }
    }

    /**
     * When a provider submits an offer, alert the consumer.
     * for_provider = false — only shows in consumer dashboard bell.
     */
    public static function newOffer(ServiceJobOffer $offer): void
    {
        $job  = $offer->jobPost;
        $prov = $offer->serviceProvider;
        if (!$job?->user_id) return;

        Alert::create([
            'user_id'      => $job->user_id,
            'type'         => 'booking',
            'title'        => '💰 New Offer Received',
            'message'      => "{$prov->name} submitted an offer of \${$offer->offered_price} for your {$job->service_type} job.",
            'action_url'   => route('jobs.show', $job),
            'priority'     => 'info',
            'for_provider' => false,  // ← consumer only
        ]);
    }

    /**
     * When consumer accepts an offer, alert the winning provider.
     * for_provider = true — only shows in provider dashboard bell.
     */
    public static function offerAccepted(ServiceJobOffer $offer): void
    {
        $job  = $offer->jobPost;
        $prov = $offer->serviceProvider;
        if (!$prov?->user_id) return;

        Alert::create([
            'user_id'      => $prov->user_id,
            'type'         => 'booking',
            'title'        => '✅ Your Offer Was Accepted!',
            'message'      => "{$job->user->name} accepted your offer of \${$offer->offered_price} for {$job->service_type}.",
            'action_url'   => route('provider.jobs.my-offers'),
            'priority'     => 'info',
            'for_provider' => true,  // ← provider only
        ]);
    }

    /**
     * When an offer is rejected, notify the provider.
     * for_provider = true — only shows in provider dashboard bell.
     */
    public static function offerRejected(ServiceJobOffer $offer): void
    {
        $job  = $offer->jobPost;
        $prov = $offer->serviceProvider;
        if (!$prov?->user_id) return;

        Alert::create([
            'user_id'      => $prov->user_id,
            'type'         => 'booking',
            'title'        => 'Offer Not Selected',
            'message'      => "Another provider was selected for the {$job->service_type} job. Keep looking for new jobs!",
            'priority'     => 'info',
            'for_provider' => true,  // ← provider only
        ]);
    }

    /**
     * When a job post is cancelled, notify providers who submitted offers.
     * for_provider = true — only shows in provider dashboard bell.
     */
    public static function jobCancelled(ServiceJobOffer $offer): void
    {
        $job  = $offer->jobPost;
        $prov = $offer->serviceProvider;
        if (!$prov?->user_id) return;

        Alert::create([
            'user_id'      => $prov->user_id,
            'type'         => 'booking',
            'title'        => 'Job Post Cancelled',
            'message'      => "The {$job->service_type} job you offered on has been cancelled by the customer.",
            'priority'     => 'info',
            'for_provider' => true,  // ← provider only
        ]);
    }

    /**
     * Alias used by some controllers — same as notifyNearbyProviders.
     */
    public static function notifyAssignedProvider(ServiceJobPost $job): void
    {
        if (!$job->assigned_provider_id) return;

        $provider = ServiceProvider::find($job->assigned_provider_id);
        if (!$provider?->user_id) return;

        Alert::create([
            'user_id'      => $provider->user_id,
            'type'         => 'booking',
            'title'        => '🔔 New Job Assigned to You',
            'message'      => "A customer has assigned a {$job->service_type} job directly to you.",
            'action_url'   => route('provider.jobs.show', $job),
            'priority'     => 'warning',
            'for_provider' => true,  // ← provider only
        ]);
    }

    /**
     * When a provider updates work status, notify the consumer.
     * for_provider = false — only shows in consumer dashboard bell.
     */
    public static function workStatusUpdated(ServiceJobPost $job, string $newStatus): void
    {
        if (!$job->user_id) return;

        $providerName = $job->acceptedOffer?->serviceProvider?->name ?? 'Your provider';
        $vehicleName  = "{$job->vehicle->year} {$job->vehicle->make} {$job->vehicle->model}";

        [$title, $message, $priority] = match ($newStatus) {
            'confirmed'   => [
                '✅ Job Confirmed',
                "{$providerName} confirmed your {$job->service_type} job for {$vehicleName}. They will be in touch soon.",
                'info',
            ],
            'in_progress' => [
                '🔧 Work In Progress',
                "{$providerName} has started working on your {$job->service_type} for {$vehicleName}.",
                'info',
            ],
            'completed'   => [
                '🎉 Service Completed',
                "{$providerName} completed your {$job->service_type} for {$vehicleName}."
                . ($job->final_cost ? " Final cost: \${$job->final_cost}." : '')
                . " Please leave a review!",
                'info',
            ],
            'cancelled'   => [
                '❌ Job Cancelled',
                "{$providerName} cancelled the {$job->service_type} job for {$vehicleName}. You can post a new job to find another provider.",
                'critical',
            ],
            default => [
                '📋 Job Status Updated',
                "Your {$job->service_type} job status changed to {$newStatus}.",
                'info',
            ],
        };

        Alert::create([
            'user_id'      => $job->user_id,
            'vehicle_id'   => $job->vehicle_id,
            'type'         => 'booking',
            'title'        => $title,
            'message'      => $message,
            'action_url'   => route('jobs.show', $job),
            'priority'     => $priority,
            'for_provider' => false,  // ← consumer only
        ]);
    }

}