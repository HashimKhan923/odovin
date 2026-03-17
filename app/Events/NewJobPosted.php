<?php

namespace App\Events;

use App\Models\ServiceJobPost;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewJobPosted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ServiceJobPost $job) {}

    public function broadcastOn(): array
    {
        return [new Channel('job-board')];
    }

    public function broadcastAs(): string
    {
        return 'new-job';
    }

    public function broadcastWith(): array
    {
        return [
            'id'               => $this->job->id,
            'job_number'       => $this->job->job_number,
            'service_type'     => $this->job->service_type,
            'description'      => \Str::limit($this->job->description, 120),
            'budget_label'     => $this->job->budgetLabel(),
            'budget_min'       => $this->job->budget_min,
            'budget_max'       => $this->job->budget_max,
            'preferred_date'   => $this->job->preferred_date,
            'preferred_time'   => $this->job->preferred_time,
            'latitude'         => $this->job->latitude,
            'longitude'        => $this->job->longitude,
            'location_address' => $this->job->location_address,
            'radius'           => $this->job->radius,
            'expires_at'       => $this->job->expires_at?->toIso8601String(),
            'created_at'       => $this->job->created_at->toIso8601String(),
            'vehicle'          => [
                'year'  => $this->job->vehicle->year,
                'make'  => $this->job->vehicle->make,
                'model' => $this->job->vehicle->model,
            ],
            'show_url' => route('provider.jobs.show', $this->job),
        ];
    }
}