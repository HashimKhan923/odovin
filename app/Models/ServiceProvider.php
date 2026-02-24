<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'type', 'phone', 'address', 'city', 'state', 'zip_code',
        'latitude', 'longitude', 'services_offered', 'description',
        'rating', 'total_reviews', 'is_verified', 'is_active', 'working_hours',
    ];

    protected $casts = [
        'rating'           => 'decimal:2',
        'is_verified'      => 'boolean',
        'is_active'        => 'boolean',
        'latitude'         => 'decimal:8',
        'longitude'        => 'decimal:8',
        'services_offered' => 'array',
        'working_hours'    => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Convenience accessors delegating to the linked user
    public function getNameAttribute(): string
    {
        return $this->user?->name ?? '';
    }

    public function getEmailAttribute(): string
    {
        return $this->user?->email ?? '';
    }

    public function serviceRecords(): HasMany
    {
        return $this->hasMany(ServiceRecord::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ServiceBooking::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function updateRating()
    {
        $avgRating    = $this->bookings()->whereNotNull('rating')->avg('rating');
        $totalReviews = $this->bookings()->whereNotNull('rating')->count();
        $this->update(['rating' => $avgRating ?? 0, 'total_reviews' => $totalReviews]);
    }
}