<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Dispute extends Model
{
    protected $fillable = [
        'job_post_id', 'job_escrow_id', 'raised_by_user_id', 'raised_by_role',
        'reference', 'reason_code', 'description', 'status',
        'assigned_to', 'resolution', 'resolution_notes', 'resolution_amount',
        'resolved_by', 'resolved_at',
        'consumer_evidence', 'provider_evidence',
        'message_count', 'last_message_at',
    ];

    protected $casts = [
        'consumer_evidence' => 'array',
        'provider_evidence' => 'array',
        'resolved_at'       => 'datetime',
        'last_message_at'   => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn($d) => $d->reference = 'DSP-' . strtoupper(Str::random(8)));
    }

    // ── Relationships ──────────────────────────────────────────────

    public function job(): BelongsTo
    {
        return $this->belongsTo(ServiceJobPost::class, 'job_post_id');
    }

    public function escrow(): BelongsTo
    {
        return $this->belongsTo(JobEscrow::class, 'job_escrow_id');
    }

    public function raisedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'raised_by_user_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(DisputeMessage::class)->orderBy('created_at');
    }

    // ── State helpers ──────────────────────────────────────────────

    public function isOpen(): bool        { return $this->status === 'open'; }
    public function isUnderReview(): bool { return $this->status === 'under_review'; }
    public function isResolved(): bool    { return str_starts_with($this->status, 'resolved'); }
    public function isClosed(): bool      { return $this->status === 'closed'; }
    public function isActive(): bool      { return in_array($this->status, ['open', 'under_review']); }

    public function statusLabel(): string
    {
        return match($this->status) {
            'open'                  => 'Open',
            'under_review'          => 'Under Review',
            'resolved_consumer'     => 'Resolved — Refunded',
            'resolved_provider'     => 'Resolved — Released',
            'resolved_split'        => 'Resolved — Split',
            'closed'                => 'Closed',
            default                 => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match(true) {
            $this->isOpen()                      => '#ffaa00',
            $this->isUnderReview()               => '#00d4ff',
            str_starts_with($this->status, 'resolved') => '#00ffaa',
            default                              => '#666',
        };
    }

    public function reasonLabel(): string
    {
        return match($this->reason_code) {
            'work_not_done'  => 'Work Not Completed',
            'poor_quality'   => 'Poor Quality Work',
            'no_show'        => 'Provider No-Show',
            'overcharged'    => 'Overcharged',
            'damage'         => 'Vehicle Damaged',
            'other'          => 'Other',
            default          => ucfirst(str_replace('_', ' ', $this->reason_code)),
        };
    }

    public function resolutionLabel(): string
    {
        return match($this->resolution) {
            'full_refund'         => 'Full Refund to Consumer',
            'partial_refund'      => 'Partial Refund — $' . number_format(($this->resolution_amount ?? 0) / 100, 2),
            'release_to_provider' => 'Payment Released to Provider',
            'no_action'           => 'No Action — Dispute Closed',
            default               => $this->resolution ?? '—',
        };
    }
}