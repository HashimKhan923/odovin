<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceDiagnostic extends Model
{
    protected $fillable = [
        'vehicle_id', 'service_record_id', 'service_provider_id',
        'title', 'description', 'location', 'category', 'severity',
        'is_safety_critical', 'status',
        'estimated_cost_min', 'estimated_cost_max',
        'resolved_by_provider_id', 'resolved_at', 'resolution_notes',
        'status_updated_by_provider_id', 'status_updated_at', 'status_notes',
    ];

    protected $casts = [
        'is_safety_critical' => 'boolean',
        'resolved_at'        => 'datetime',
        'status_updated_at'  => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────────────────
    public function vehicle(): BelongsTo      { return $this->belongsTo(Vehicle::class); }
    public function serviceRecord(): BelongsTo { return $this->belongsTo(ServiceRecord::class); }
    public function serviceProvider(): BelongsTo { return $this->belongsTo(ServiceProvider::class); }
    public function resolvedByProvider(): BelongsTo { return $this->belongsTo(ServiceProvider::class, 'resolved_by_provider_id'); }

    // ── Scopes ───────────────────────────────────────────────────────────────
    public function scopeOpen($q)     { return $q->whereIn('status', ['open', 'acknowledged', 'monitoring']); }
    public function scopeCritical($q) { return $q->where('severity', 'critical'); }
    public function scopeSafety($q)   { return $q->where('is_safety_critical', true); }

    // ── Accessors ────────────────────────────────────────────────────────────
    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'low'      => '#00ffaa',
            'medium'   => '#ffaa00',
            'high'     => '#ff6600',
            'critical' => '#ff3366',
            default    => '#888',
        };
    }

    public function getSeverityBgAttribute(): string
    {
        return match($this->severity) {
            'low'      => 'rgba(0,255,170,.1)',
            'medium'   => 'rgba(255,170,0,.1)',
            'high'     => 'rgba(255,102,0,.1)',
            'critical' => 'rgba(255,51,102,.1)',
            default    => 'rgba(136,136,136,.1)',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'open'         => '#ff3366',
            'acknowledged' => '#ffaa00',
            'in_progress'  => '#00d4ff',
            'monitoring'   => '#aa88ff',
            'resolved'     => '#00ffaa',
            'ignored'      => '#555577',
            default        => '#888',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'open'         => 'Open',
            'acknowledged' => 'Acknowledged',
            'in_progress'  => 'In Progress',
            'monitoring'   => 'Monitoring',
            'resolved'     => 'Resolved',
            'ignored'      => 'Ignored',
            default        => ucfirst($this->status),
        };
    }

    public function getCategoryIconAttribute(): string
    {
        return match($this->category) {
            'brakes'       => '🛑',
            'engine'       => '⚙️',
            'transmission' => '🔄',
            'suspension'   => '🔩',
            'electrical'   => '⚡',
            'tires'        => '🔵',
            'body'         => '🚗',
            'fluids'       => '💧',
            'cooling'      => '❄️',
            'exhaust'      => '💨',
            'safety'       => '🛡️',
            default        => '🔧',
        };
    }

    public function getCostRangeAttribute(): string
    {
        if (!$this->estimated_cost_min && !$this->estimated_cost_max) return 'Estimate unavailable';
        if ($this->estimated_cost_min && $this->estimated_cost_max) {
            return '$' . number_format($this->estimated_cost_min) . ' – $' . number_format($this->estimated_cost_max);
        }
        return 'From $' . number_format($this->estimated_cost_min ?? $this->estimated_cost_max);
    }

    public function getIsOpenAttribute(): bool
    {
        return in_array($this->status, ['open', 'acknowledged', 'monitoring', 'in_progress']);
    }
}