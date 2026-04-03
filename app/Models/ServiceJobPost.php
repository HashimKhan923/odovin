<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\ServiceProvider;

class ServiceJobPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'vehicle_id', 'job_number', 'service_type', 'description',
        'budget_min', 'budget_max', 'preferred_date', 'preferred_time',
        'latitude', 'longitude', 'location_address', 'radius',
        'status', 'accepted_offer_id', 'expires_at', 'customer_notes',
        // Work tracking
        'assigned_provider_id',
        'preferred_provider_id',
        'attached_diagnostic_ids',
        // Work tracking
        'work_status', 'final_cost', 'provider_notes',
        'rating', 'review', 'work_started_at', 'work_completed_at',
        'media',
    ];

    protected $casts = [
        'budget_min'        => 'decimal:2',
        'budget_max'        => 'decimal:2',
        'final_cost'        => 'decimal:2',
        'latitude'          => 'decimal:7',
        'longitude'         => 'decimal:7',
        'expires_at'        => 'datetime',
        'work_started_at'   => 'datetime',
        'work_completed_at' => 'datetime',
        'media'                   => 'array',
        'attached_diagnostic_ids' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($job) {
            $job->job_number = 'JB' . strtoupper(Str::random(8));
            $job->expires_at = $job->expires_at ?? now()->addHours(24);
        });
    }

    // ── Relationships ──────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(ServiceJobOffer::class, 'job_post_id');
    }

    public function acceptedOffer(): BelongsTo
    {
        return $this->belongsTo(ServiceJobOffer::class, 'accepted_offer_id');
    }

    public function preferredProvider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class, 'preferred_provider_id');
    }

    public function attachedDiagnostics()
    {
        return \App\Models\ServiceDiagnostic::whereIn('id', $this->attached_diagnostic_ids ?? [])->get();
    }

    public function assignedProvider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class, 'assigned_provider_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeOpen($query)
    {
        return $query->where('status', 'open')
                     ->where(function ($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     });
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isOpen(): bool
    {
        return $this->status === 'open' && !$this->isExpired();
    }

    public function budgetLabel(): string
    {
        if ($this->budget_min && $this->budget_max) {
            return '$' . number_format($this->budget_min) . ' – $' . number_format($this->budget_max);
        }
        if ($this->budget_max) {
            return 'Up to $' . number_format($this->budget_max);
        }
        return 'Flexible';
    }

    // ── Work status helpers ────────────────────────────────────────────────

    public function workStatusLabel(): string
    {
        return match ($this->work_status ?? 'pending') {
            'pending'     => 'Awaiting Confirmation',
            'confirmed'   => 'Confirmed',
            'in_progress' => 'In Progress',
            'completed'   => 'Completed',
            'cancelled'   => 'Cancelled',
            default       => ucfirst($this->work_status),
        };
    }

    public function workStatusColor(): string
    {
        return match ($this->work_status ?? 'pending') {
            'pending'     => 'var(--accent-warning)',
            'confirmed'   => 'var(--accent-cyan)',
            'in_progress' => '#a855f7',
            'completed'   => 'var(--accent-green)',
            'cancelled'   => '#ff3366',
            default       => 'var(--text-tertiary)',
        };
    }

    public function canBeRated(): bool
    {
        return $this->work_status === 'completed'
            && $this->status === 'accepted'
            && is_null($this->rating);
    }

    public function updateProviderRating(): void
    {
        if ($this->acceptedOffer?->serviceProvider) {
            $this->acceptedOffer->serviceProvider->updateRating();
        }
    }
}