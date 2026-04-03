<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceJobPost;
use App\Models\ServiceJobOffer;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobRealTimeController extends Controller
{
    /**
     * Provider polls this to get new open jobs since a timestamp.
     * GET /api/jobs/live?since=<unix_timestamp>&lat=<lat>&lng=<lng>&radius=<miles>
     */
    public function liveJobs(Request $request)
    {
        $since  = $request->since  ? \Carbon\Carbon::createFromTimestamp($request->since) : now()->subMinutes(5);
        $lat    = $request->lat;
        $lng    = $request->lng;
        $radius = $request->radius ?? 50;

        $query = ServiceJobPost::open()
            ->with(['vehicle'])
            ->where('created_at', '>', $since);

        if ($lat && $lng) {
            $query->selectRaw("
                service_job_posts.*,
                (3959 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )) AS distance
            ", [$lat, $lng, $lat])
            ->havingRaw('distance <= ?', [$radius])
            ->orderBy('distance');
        } else {
            $query->select('service_job_posts.*')->latest();
        }

        $jobs = $query->get()
            ->filter(function ($job) use ($lat, $lng) {
                // Respect the job's own radius — provider must be within it
                if (!$lat || !$lng || !$job->latitude || !$job->longitude || !$job->radius) return true;
                $dLat = deg2rad($job->latitude - $lat);
                $dLng = deg2rad($job->longitude - $lng);
                $a = sin($dLat/2)**2 + cos(deg2rad($lat)) * cos(deg2rad($job->latitude)) * sin($dLng/2)**2;
                $dist = 3959 * 2 * atan2(sqrt($a), sqrt(1-$a));
                return $dist <= (float) $job->radius;
            })
            ->map(function ($job) {
            return [
                'id'               => $job->id,
                'job_number'       => $job->job_number,
                'service_type'     => $job->service_type,
                'description'      => \Str::limit($job->description, 120),
                'budget_label'     => $job->budgetLabel(),
                'budget_min'       => $job->budget_min,
                'budget_max'       => $job->budget_max,
                'preferred_date'   => $job->preferred_date,
                'preferred_time'   => $job->preferred_time,
                'latitude'         => $job->latitude,
                'longitude'        => $job->longitude,
                'location_address' => $job->location_address,
                'radius'           => $job->radius,
                'expires_at'       => $job->expires_at?->toIso8601String(),
                'created_at'       => $job->created_at->toIso8601String(),
                'offers_count'     => $job->offers()->count(),
                'distance'         => isset($job->distance) ? round($job->distance, 1) : null,
                'vehicle'          => [
                    'year'  => $job->vehicle->year,
                    'make'  => $job->vehicle->make,
                    'model' => $job->vehicle->model,
                ],
                'show_url'         => route('provider.jobs.show', $job),
            ];
        });

        // Also return IDs of jobs that just closed (accepted/cancelled) so provider can remove them
        $closedJobIds = ServiceJobPost::whereIn('status', ['accepted', 'cancelled', 'expired'])
            ->where('updated_at', '>', $since)
            ->pluck('id');

        return response()->json([
            'new_jobs'       => $jobs,
            'closed_job_ids' => $closedJobIds,
            'server_time'    => now()->timestamp,
        ]);
    }

    /**
     * Consumer polls this to get new offers on their job.
     * GET /api/jobs/{job}/offers/live?since=<unix_timestamp>
     */
    public function liveOffers(Request $request, ServiceJobPost $job)
    {
        // Security: only the job owner can poll
        if ($job->user_id !== Auth::id()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $since = $request->since
            ? \Carbon\Carbon::createFromTimestamp($request->since)
            : now()->subMinutes(5);

        $offers = ServiceJobOffer::where('job_post_id', $job->id)
            ->where('created_at', '>', $since)
            ->with('serviceProvider')
            ->get()
            ->map(function ($offer) use ($job) {
                $prov     = $offer->serviceProvider;
                $distance = null;
                if ($job->latitude && $job->longitude && $prov->latitude && $prov->longitude) {
                    $dLat = deg2rad($prov->latitude - $job->latitude);
                    $dLng = deg2rad($prov->longitude - $job->longitude);
                    $a    = sin($dLat / 2) ** 2
                        + cos(deg2rad($job->latitude)) * cos(deg2rad($prov->latitude)) * sin($dLng / 2) ** 2;
                    $distance = round(3959 * 2 * atan2(sqrt($a), sqrt(1 - $a)), 1);
                }

                return [
                    'offer_id'           => $offer->id,
                    'offered_price'      => $offer->offered_price,
                    'available_date'     => $offer->available_date,
                    'available_time'     => $offer->available_time,
                    'estimated_duration' => $offer->estimated_duration,
                    'message'            => $offer->message,
                    'status'             => $offer->status,
                    'created_at'         => $offer->created_at->toIso8601String(),
                    'distance'           => $distance,
                    'provider' => [
                        'id'            => $prov->id,
                        'name'          => $prov->name,
                        'type'          => $prov->type,
                        'is_verified'   => $prov->is_verified,
                        'rating'        => $prov->rating,
                        'total_reviews' => $prov->total_reviews,
                        'city'          => $prov->city,
                        'state'         => $prov->state,
                        'profile_url'   => route('providers.show', $prov),
                    ],
                    'accept_url' => route('jobs.accept-offer', [$offer->jobPost, $offer]),
                ];
            });

        // Also check if job status changed (e.g. another tab accepted)
        $job->refresh();

        return response()->json([
            'new_offers'  => $offers,
            'job_status'  => $job->status,
            'server_time' => now()->timestamp,
        ]);
    }

    /**
     * Provider polls for updates to their submitted offers.
     * GET /api/provider/offers/live?since=<unix_timestamp>
     */
    public function liveProviderOffers(Request $request)
    {
        $provider = Auth::user()->serviceProvider;
        if (!$provider) return response()->json(['error' => 'No provider profile'], 403);

        $since = $request->since
            ? \Carbon\Carbon::createFromTimestamp($request->since)
            : now()->subMinutes(5);

        // Offers whose status changed since last poll
        $updatedOffers = ServiceJobOffer::where('service_provider_id', $provider->id)
            ->where('updated_at', '>', $since)
            ->whereIn('status', ['accepted', 'rejected'])
            ->with('jobPost')
            ->get()
            ->map(fn($o) => [
                'offer_id'     => $o->id,
                'job_id'       => $o->job_post_id,
                'service_type' => $o->jobPost?->service_type,
                'new_status'   => $o->status,
                'offered_price'=> $o->offered_price,
            ]);

        return response()->json([
            'updated_offers' => $updatedOffers,
            'server_time'    => now()->timestamp,
        ]);
    }
}