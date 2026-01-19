<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'type',
        'title',
        'description',
        'due_date',
        'reminder_date',
        'priority',
        'is_sent',
        'is_completed',
    ];

    protected $casts = [
        'due_date' => 'date',
        'reminder_date' => 'date',
        'is_sent' => 'boolean',
        'is_completed' => 'boolean',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}