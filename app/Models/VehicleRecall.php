<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleRecall extends Model
{
        protected $fillable = [
        'vehicle_id',
        'nhtsa_campaign_number',
        'component',
        'summary',
        'consequence',
        'remedy',
        'report_received_date',
        'is_open',
        'is_read',
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'is_read' => 'boolean',
        'report_received_date' => 'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
