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

        $lat    = (float) $job->latitude;
        $lng    = (float) $job->longitude;
        $radius = (int)   ($job->radius ?? 25);

        // Log what we're working with for debugging
        \Illuminate\Support\Facades\Log::info('[JobNotification] notifyNearbyProviders', [
            'job_id'     => $job->id,
            'job_radius' => $job->radius,
            'radius_used'=> $radius,
            'lat'        => $lat,
            'lng'        => $lng,
        ]);

        // Get ALL providers with coords, calculate distance in PHP — avoids MySQL HAVING alias bug
        $allProviders = ServiceProvider::active()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $providers = $allProviders->filter(function ($provider) use ($lat, $lng, $radius) {
            $provLat = (float) $provider->latitude;
            $provLng = (float) $provider->longitude;

            // Haversine in PHP — guaranteed accurate, no SQL alias issues
            $earthRadius = 3959; // miles
            $dLat = deg2rad($provLat - $lat);
            $dLng = deg2rad($provLng - $lng);
            $a = sin($dLat / 2) ** 2
               + cos(deg2rad($lat)) * cos(deg2rad($provLat)) * sin($dLng / 2) ** 2;
            $distance = round($earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a)), 2);

            $provider->distance = $distance;

            \Illuminate\Support\Facades\Log::info('[JobNotification] provider check', [
                'provider_id'   => $provider->id,
                'business_name' => $provider->business_name,
                'distance_mi'   => $distance,
                'radius_mi'     => $radius,
                'within_radius' => $distance <= $radius,
            ]);

            return $distance <= $radius;
        });

        foreach ($providers as $provider) {
            if (!$provider->user_id) continue;
            Alert::create([
                'user_id'      => $provider->user_id,
                'type'         => 'booking',
                'title'        => '🔔 New Job Near You',
                'message'      => "A customer needs {$job->service_type} within " . round($provider->distance, 1) . " miles. Budget: {$job->budgetLabel()}.",
                'action_url'   => route('provider.jobs.show', $job),
                'priority'     => 'warning',
                'for_provider' => true,
            ]);
        }
    }

    /**
     * Notify the consumer's preferred provider (from prior diagnosis) about the job.
     */
    public static function notifyPreferredProvider(ServiceJobPost $job): void
    {
        if (!$job->preferred_provider_id) return;

        $provider = \App\Models\ServiceProvider::with('user')->find($job->preferred_provider_id);
        if (!$provider || !$provider->user_id) return;

        $vehicle  = $job->vehicle;
        $vehName  = $vehicle ? "{$vehicle->year} {$vehicle->make} {$vehicle->model}" : 'a vehicle';

        Alert::create([
            'user_id'      => $provider->user_id,
            'type'         => 'job_assignment',
            'title'        => "⭐ Preferred Provider — {$job->service_type}",
            'message'      => "A customer who previously used your services has posted a {$job->service_type} job for {$vehName} and selected you as their preferred provider.",
            'action_url'   => route('provider.jobs.show', $job),
            'priority'     => 'high',
            'for_provider' => true,
        ]);
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
            'for_provider' => false,
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
            'for_provider' => true,
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
            'for_provider' => true,
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
            'for_provider' => true,
        ]);
    }

    /**
     * When a job is directly assigned to a provider.
     * for_provider = true — only shows in provider dashboard bell.
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
            'for_provider' => true,
        ]);
    }

    /**
     * When provider changes work_status → notify the consumer.
     * for_provider = false — only shows in consumer dashboard bell.
     */
    public static function workStatusUpdated(ServiceJobPost $job, string $newStatus): void
    {
        if (!$job->user_id) return;

        $providerName = $job->acceptedOffer?->serviceProvider?->name ?? 'Your provider';
        $vehicleName  = "{$job->vehicle->year} {$job->vehicle->make} {$job->vehicle->model}";

        $config = match ($newStatus) {
            'confirmed'   => [
                'title'    => '✅ Job Confirmed',
                'message'  => "{$providerName} confirmed your {$job->service_type} job for {$vehicleName}. They will be in touch soon.",
                'priority' => 'info',
            ],
            'in_progress' => [
                'title'    => '🔧 Work In Progress',
                'message'  => "{$providerName} has started working on your {$job->service_type} for {$vehicleName}.",
                'priority' => 'warning',
            ],
            'completed'   => [
                'title'    => '🎉 Service Completed',
                'message'  => "{$providerName} completed your {$job->service_type} for {$vehicleName}."
                            . ($job->final_cost ? " Final cost: \${$job->final_cost}." : '')
                            . " Please leave a review!",
                'priority' => 'info',
            ],
            'cancelled'   => [
                'title'    => '❌ Job Cancelled by Provider',
                'message'  => "{$providerName} cancelled the {$job->service_type} job for {$vehicleName}. You can post a new job to find another provider.",
                'priority' => 'critical',
            ],
            default => null,
        };

        if (!$config) return;

        Alert::create([
            'user_id'      => $job->user_id,
            'vehicle_id'   => $job->vehicle_id,
            'type'         => 'booking',
            'title'        => $config['title'],
            'message'      => $config['message'],
            'action_url'   => route('jobs.show', $job),
            'priority'     => $config['priority'],
            'for_provider' => false,
        ]);
    }

    /**
     * When consumer submits a rating on a completed job → notify the provider.
     * for_provider = true — only shows in provider dashboard bell.
     */
    public static function jobReviewSubmitted(ServiceJobPost $job): void
    {
        $provider = $job->acceptedOffer?->serviceProvider;
        if (!$provider?->user_id) return;

        Alert::create([
            'user_id'      => $provider->user_id,
            'type'         => 'booking',
            'title'        => "New {$job->rating}⭐ Review on Job",
            'message'      => "{$job->user->name} rated your {$job->service_type} job for {$job->vehicle->year} {$job->vehicle->make} {$job->vehicle->model}.",
            'action_url'   => route('provider.jobs.my-offers'),
            'priority'     => 'info',
            'for_provider' => true,
        ]);
    }
}