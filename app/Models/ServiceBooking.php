<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ServiceBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'service_provider_id',
        'booking_number',
        'service_type',
        'description',
        'scheduled_date',
        'status',
        'estimated_cost',
        'final_cost',
        'customer_notes',
        'provider_notes',
        'rating',
        'review',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'estimated_cost' => 'decimal:2',
        'final_cost' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($booking) {
            $booking->booking_number = 'BK' . strtoupper(Str::random(8));
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function serviceProvider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class);
    }
}