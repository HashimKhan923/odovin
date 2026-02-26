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
                'user_id'    => $provider->user_id,
                'type'       => 'booking',
                'title'      => '🔔 New Job Near You',
                'message'    => "A customer needs {$job->service_type} within " . round($provider->distance, 1) . " miles. Budget: {$job->budgetLabel()}.",
                'action_url' => route('provider.jobs.show', $job),
                'priority'   => 'warning',
            ]);
        }
    }

    /**
     * When a provider submits an offer, alert the consumer.
     */
    public static function newOffer(ServiceJobOffer $offer): void
    {
        $job  = $offer->jobPost;
        $prov = $offer->serviceProvider;
        if (!$job?->user_id) return;

        Alert::create([
            'user_id'    => $job->user_id,
            'type'       => 'booking',
            'title'      => '💰 New Offer Received',
            'message'    => "{$prov->name} submitted an offer of \${$offer->offered_price} for your {$job->service_type} job.",
            'action_url' => route('jobs.show', $job),
            'priority'   => 'info',
        ]);
    }

    /**
     * When consumer accepts an offer, alert the winning provider.
     */
    public static function offerAccepted(ServiceJobOffer $offer): void
    {
        $job  = $offer->jobPost;
        $prov = $offer->serviceProvider;
        if (!$prov?->user_id) return;

        Alert::create([
            'user_id'    => $prov->user_id,
            'type'       => 'booking',
            'title'      => '✅ Your Offer Was Accepted!',
            'message'    => "{$job->user->name} accepted your offer of \${$offer->offered_price} for {$job->service_type}.",
            'action_url' => route('provider.jobs.my-offers'),
            'priority'   => 'info',
        ]);
    }

    /**
     * When an offer is rejected, notify the provider.
     */
    public static function offerRejected(ServiceJobOffer $offer): void
    {
        $job  = $offer->jobPost;
        $prov = $offer->serviceProvider;
        if (!$prov?->user_id) return;

        Alert::create([
            'user_id'    => $prov->user_id,
            'type'       => 'booking',
            'title'      => 'Offer Not Selected',
            'message'    => "Another provider was selected for the {$job->service_type} job. Keep looking for new jobs!",
            'priority'   => 'info',
        ]);
    }

    /**
     * When a job post is cancelled, notify providers who submitted offers.
     */
public static function jobCancelled(ServiceJobOffer $offer): void
{
    $job  = $offer->jobPost;
    $prov = $offer->serviceProvider;
    if (!$prov?->user_id) return;

    Alert::create([
        'user_id'    => $prov->user_id,
        'type'       => 'booking',
        'title'      => 'Job Post Cancelled',
        'message'    => "The {$job->service_type} job you offered on has been cancelled by the customer.",
        'priority'   => 'info',
    ]);
}

// ─────────────────────────────────────────────────────────────────────
// Work-status notifications (mirrors ProviderNotificationService for bookings)
// ─────────────────────────────────────────────────────────────────────

    /**
     * When provider changes work_status → notify the consumer.
     */
    public static function workStatusUpdated(\App\Models\ServiceJobPost $job, string $newStatus): void
    {
        if (!$job->user_id) return;

        $providerName = $job->acceptedOffer?->serviceProvider?->name ?? 'The service provider';

        $config = match ($newStatus) {
            'confirmed' => [
                'title'    => '✅ Job Confirmed',
                'message'  => "{$providerName} confirmed your {$job->service_type} job. They will start on the agreed date.",
                'priority' => 'info',
            ],
            'in_progress' => [
                'title'    => '🔧 Work In Progress',
                'message'  => "{$providerName} has started working on your {$job->service_type}. You'll be notified when done.",
                'priority' => 'warning',
            ],
            'completed' => [
                'title'    => '🎉 Job Completed!',
                'message'  => "{$providerName} has completed your {$job->service_type}. Please review the final cost and leave a rating.",
                'priority' => 'info',
            ],
            'cancelled' => [
                'title'    => '❌ Job Cancelled by Provider',
                'message'  => "{$providerName} has cancelled your {$job->service_type} job. Please post a new job to find another provider.",
                'priority' => 'critical',
            ],
            default => null,
        };

        if (!$config) return;

        Alert::create([
            'user_id'    => $job->user_id,
            'vehicle_id' => $job->vehicle_id,
            'type'       => 'booking',
            'title'      => $config['title'],
            'message'    => $config['message'],
            'action_url' => route('jobs.show', $job),
            'priority'   => $config['priority'],
        ]);
    }

    /**
     * When consumer submits a rating on a completed job → notify the provider.
     */
    public static function jobReviewSubmitted(\App\Models\ServiceJobPost $job): void
    {
        $provider = $job->acceptedOffer?->serviceProvider;
        if (!$provider?->user_id) return;

            Alert::create([
                'user_id'    => $provider->user_id,
                'type'       => 'booking',
                'title'      => "New {$job->rating}★ Review on Job",
                'message'    => "{$job->user->name} rated your {$job->service_type} job.",
                'action_url' => route('provider.jobs.my-offers'),
                'priority'   => 'info',
            ]);
        }
    }