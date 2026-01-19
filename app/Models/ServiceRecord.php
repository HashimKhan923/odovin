<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'service_provider_id',
        'service_type',
        'description',
        'service_date',
        'mileage_at_service',
        'cost',
        'invoice_number',
        'invoice_file',
        'parts_replaced',
        'notes',
        'next_service_mileage',
        'next_service_date',
    ];

    protected $casts = [
        'service_date' => 'date',
        'cost' => 'decimal:2',
        'parts_replaced' => 'array',
        'next_service_date' => 'date',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function serviceProvider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class);
    }
}
