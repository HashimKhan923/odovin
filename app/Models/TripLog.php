<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'trip_date',
        'start_location',
        'start_odometer',
        'end_odometer',
        'distance',
        'purpose',
        'destination',
        'notes',
    ];

    protected $casts = [
        'trip_date' => 'date',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    // ── Accessors ──────────────────────────────────────────────────────────

    public function getFormattedPurposeAttribute(): string
    {
        return ucfirst($this->purpose);
    }

    public function getPurposeColorAttribute(): string
    {
        return match($this->purpose) {
            'business' => '#00d4ff',
            'personal' => '#00ffaa',
            'commute'  => '#ffaa00',
            default    => '#ffffff',
        };
    }

    public function getPurposeIconAttribute(): string
    {
        return match($this->purpose) {
            'business' => '💼',
            'personal' => '🏠',
            'commute'  => '🚗',
            default    => '📍',
        };
    }

    public function getRouteAttribute(): string
    {
        $from = $this->start_location ?: 'Origin';
        return "{$from} → {$this->destination}";
    }
}