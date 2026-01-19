<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'zip_code',
        'latitude',
        'longitude',
        'services_offered',
        'rating',
        'total_reviews',
        'is_verified',
        'is_active',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

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
        $avgRating = $this->bookings()
            ->whereNotNull('rating')
            ->avg('rating');
            
        $totalReviews = $this->bookings()
            ->whereNotNull('rating')
            ->count();
            
        $this->update([
            'rating' => $avgRating ?? 0,
            'total_reviews' => $totalReviews,
        ]);
    }
}