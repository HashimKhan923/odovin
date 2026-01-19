<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'service_type',
        'description',
        'due_mileage',
        'due_date',
        'priority',
        'status',
        'is_recurring',
        'recurrence_mileage',
        'recurrence_months',
    ];

    protected $casts = [
        'due_date' => 'date',
        'is_recurring' => 'boolean',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function markCompleted()
    {
        $this->update(['status' => 'completed']);
        
        if ($this->is_recurring) {
            $this->createNextSchedule();
        }
    }

    protected function createNextSchedule()
    {
        $newSchedule = $this->replicate();
        
        if ($this->due_date) {
            $newSchedule->due_date = $this->due_date->addMonths($this->recurrence_months);
        }
        
        if ($this->due_mileage) {
            $newSchedule->due_mileage = $this->due_mileage + $this->recurrence_mileage;
        }
        
        $newSchedule->status = 'pending';
        $newSchedule->save();
    }
}