<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'fill_date',
        'odometer',
        'gallons',
        'price_per_gallon',
        'total_cost',
        'mpg',
        'is_full_tank',
        'gas_station',
        'notes',
    ];

    protected $casts = [
        'fill_date' => 'date',
        'gallons' => 'decimal:2',
        'price_per_gallon' => 'decimal:3',
        'total_cost' => 'decimal:2',
        'mpg' => 'decimal:2',
        'is_full_tank' => 'boolean',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}