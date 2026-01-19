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

    public function getFormattedPurposeAttribute(): string
    {
        return ucfirst($this->purpose);
    }
}