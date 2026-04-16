<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceJobOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_post_id', 'service_provider_id', 'offered_price',
        'available_date', 'available_time', 'estimated_duration',
        'message', 'status','counter_price', 'counter_message', 'countered_at', 'negotiation_status',
    ];

    protected $casts = [
        'offered_price' => 'decimal:2',
        'counter_price' => 'decimal:2',
        'available_date' => 'date',
        'available_time' => 'datetime:H:i',
        'estimated_duration' => 'integer',
        'countered_at' => 'datetime',
        
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(ServiceJobPost::class, 'job_post_id');
    }

    public function serviceProvider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class);
    }

     /** The active/final price — counter if accepted, otherwise original */
    public function activePrice(): float
    {
        if ($this->negotiation_status === 'counter_accepted' && $this->counter_price) {
            return (float) $this->counter_price;
        }
        return (float) $this->offered_price;
    }
 
    /** Whether consumer has sent a counter that provider hasn't responded to yet */
    public function awaitingProviderResponse(): bool
    {
        return $this->negotiation_status === 'countered';
    }
 
    /** Whether this offer has an active counter the consumer sent */
    public function hasCounter(): bool
    {
        return !is_null($this->counter_price);
    }
 
    public function negotiationLabel(): string
    {
        return match ($this->negotiation_status) {
            'countered'         => 'Counter Sent',
            'counter_accepted'  => 'Counter Accepted',
            'counter_rejected'  => 'Counter Declined',
            default             => 'Pending',
        };
    }
 
    public function negotiationColor(): string
    {
        return match ($this->negotiation_status) {
            'countered'         => '#ffaa00',
            'counter_accepted'  => '#00ffaa',
            'counter_rejected'  => '#ff8099',
            default             => '#aaa',
        };
    }
}