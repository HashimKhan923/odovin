<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ServiceReportShare extends Model
{
    protected $fillable = [
        'user_id', 'vehicle_id', 'token',
        'include_costs', 'include_diagnostics',
        'include_provider_details', 'include_photos',
        'from_date', 'to_date', 'label',
        'expires_at', 'view_count', 'last_viewed_at',
    ];

    protected $casts = [
        'include_costs'            => 'boolean',
        'include_diagnostics'      => 'boolean',
        'include_provider_details' => 'boolean',
        'include_photos'           => 'boolean',
        'from_date'                => 'date',
        'to_date'                  => 'date',
        'expires_at'               => 'datetime',
        'last_viewed_at'           => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn($s) => $s->token = $s->token ?? Str::random(48));
    }

    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
    public function vehicle(): BelongsTo { return $this->belongsTo(Vehicle::class); }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return !$this->isExpired();
    }

    public function publicUrl(): string
    {
        return route('service-history.report.public', $this->token);
    }

    public function expiryLabel(): string
    {
        if (!$this->expires_at) return 'Never expires';
        if ($this->isExpired()) return 'Expired ' . $this->expires_at->diffForHumans();
        return 'Expires ' . $this->expires_at->format('M d, Y');
    }

    public function recordQuery()
    {
        $q = ServiceRecord::where('vehicle_id', $this->vehicle_id)
            ->with(['serviceProvider', 'serviceDiagnostics'])
            ->latest('service_date');

        if ($this->from_date) $q->where('service_date', '>=', $this->from_date);
        if ($this->to_date)   $q->where('service_date', '<=', $this->to_date);

        return $q;
    }
}