<?php
// app/Models/Vehicle.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Alert;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'vin',
        'make',
        'model',
        'year',
        'trim',
        'engine',
        'transmission',
        'fuel_type',
        'color',
        'license_plate',
        'purchase_date',
        'purchase_price',
        'current_mileage',
        'specifications',
        'is_primary',
        'status',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'specifications' => 'array',
        'is_primary' => 'boolean',
        'current_mileage' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(VehicleDocument::class);
    }

    public function maintenanceSchedules(): HasMany
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    public function serviceRecords(): HasMany
    {
        return $this->hasMany(ServiceRecord::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ServiceBooking::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return "{$this->year} {$this->make} {$this->model}";
    }

    // Methods
    public function getTotalExpenses($startDate = null, $endDate = null)
    {
        $query = $this->expenses();
        
        if ($startDate) {
            $query->where('expense_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('expense_date', '<=', $endDate);
        }
        
        return $query->sum('amount');
    }

    public function getUpcomingMaintenance()
    {
        return $this->maintenanceSchedules()
            ->whereIn('status', ['pending', 'overdue'])
            ->orderBy('due_date')
            ->get();
    }

    public function getExpiringDocuments($days = 30)
    {
        return $this->documents()
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now(), now()->addDays($days)])
            ->get();
    }

    public function updateMileage($newMileage)
    {
        if ($newMileage > $this->current_mileage) {
            $this->update(['current_mileage' => $newMileage]);
            $this->checkMaintenanceSchedules();
        }
    }

    protected function checkMaintenanceSchedules()
    {
        // Find pending schedules that are now overdue based on new mileage
        $nowOverdue = $this->maintenanceSchedules()
            ->where('status', 'pending')
            ->whereNotNull('due_mileage')
            ->where('due_mileage', '<=', $this->current_mileage)
            ->get();

        if ($nowOverdue->isEmpty()) return;

        // Mark them all overdue
        $this->maintenanceSchedules()
            ->whereIn('id', $nowOverdue->pluck('id'))
            ->update(['status' => 'overdue']);

        // Create one alert per overdue schedule
        foreach ($nowOverdue as $schedule) {
            // Avoid duplicate alerts — skip if an unread alert already exists for this schedule
            $alreadyAlerted = Alert::where('user_id', $this->user_id)
                ->where('type', 'maintenance')
                ->where('for_provider', false)
                ->where('is_read', false)
                ->where('title', 'like', '%' . $schedule->service_type . '%')
                ->where('vehicle_id', $this->id)
                ->exists();

            if ($alreadyAlerted) continue;

            $overdueMiles = $this->current_mileage - $schedule->due_mileage;

            Alert::create([
                'user_id'      => $this->user_id,
                'vehicle_id'   => $this->id,
                'type'         => 'maintenance',
                'title'        => '⚠️ Maintenance Due: ' . $schedule->service_type,
                'message'      => "{$this->year} {$this->make} {$this->model} — {$schedule->service_type} was due at "
                                . number_format($schedule->due_mileage) . ' mi. '
                                . 'You are now ' . number_format($overdueMiles) . ' mi overdue.',
                'action_url'   => route('maintenance.index'),
                'priority'     => $overdueMiles > 500 ? 'critical' : 'warning',
                'for_provider' => false,
            ]);
        }
    }

    public function fuelLogs(): HasMany
    {
        return $this->hasMany(FuelLog::class);
    }

    public function tripLogs(): HasMany
    {
        return $this->hasMany(TripLog::class);
    }

    public function insurances(): HasMany
    {
        return $this->hasMany(Insurance::class);
    }

    public function getAverageMpg()
    {
        return $this->fuelLogs()->where('mpg', '>', 0)->avg('mpg');
    }

    public function getTotalFuelCost()
    {
        return $this->fuelLogs()->sum('total_cost');
    }

    public function getBusinessMiles($startDate = null, $endDate = null)
    {
        $query = $this->tripLogs()->where('purpose', 'business');
        
        if ($startDate) {
            $query->where('trip_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('trip_date', '<=', $endDate);
        }
        
        return $query->sum('distance');
    }

    public function getCurrentInsurance()
    {
        return $this->insurances()
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->latest()
            ->first();
    }

    public function recalls()
    {
        return $this->hasMany(VehicleRecall::class);
    }



    public function totalExpenses(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    public function vehicleAgeInMonths(): int
    {
        return max(1, $this->created_at->diffInMonths(now()));
    }

    public function costPerMile(): float
    {
        if ($this->current_mileage <= 0) {
            return 0;
        }

        return round($this->totalExpenses() / $this->current_mileage, 2);
    }

    public function expenseByType(string $type): float
    {
        return (float) $this->expenses()
            ->whereHas('category', fn ($q) => $q->where('slug', $type))
            ->sum('amount');
    }

    public function expenseByCategory(string $category): float
    {
        return (float) $this->expenses()
            ->where('category', $category)
            ->sum('amount');
    }

    public function aiInsight()
    {
        return $this->hasOne(\App\Models\VehicleAIInsight::class);
    }

}