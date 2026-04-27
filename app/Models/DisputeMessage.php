<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DisputeMessage extends Model
{
    protected $fillable = [
        'dispute_id', 'user_id', 'sender_role', 'message', 'attachments', 'is_internal',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_internal' => 'boolean',
    ];

    public function dispute(): BelongsTo { return $this->belongsTo(Dispute::class); }
    public function user(): BelongsTo    { return $this->belongsTo(User::class); }

    public function attachmentUrls(): array
    {
        return array_map(
            fn($p) => Storage::disk('public')->url($p),
            $this->attachments ?? []
        );
    }

    public function roleColor(): string
    {
        return match($this->sender_role) {
            'admin'    => '#a855f7',
            'provider' => '#00d4ff',
            default    => '#00ffaa',
        };
    }

    public function roleLabel(): string
    {
        return match($this->sender_role) {
            'admin'    => '⚖ Admin',
            'provider' => '🔧 Provider',
            default    => '👤 Consumer',
        };
    }
}