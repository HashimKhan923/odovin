<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProviderCertification extends Model
{
    protected $fillable = [
        'service_provider_id', 'name', 'issuing_body', 'certificate_number',
        'issued_at', 'expires_at', 'file_path', 'file_original_name',
        'file_mime', 'file_size', 'status', 'admin_notes',
        'reviewed_by', 'reviewed_at', 'show_on_profile',
    ];

    protected $casts = [
        'issued_at'       => 'date',
        'expires_at'      => 'date',
        'reviewed_at'     => 'datetime',
        'show_on_profile' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function provider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class, 'service_provider_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ── State helpers ──────────────────────────────────────────────

    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isExpiringSoon(): bool
    {
        return $this->expires_at
            && !$this->isExpired()
            && $this->expires_at->diffInDays(now()) <= 30;
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'approved' => 'Verified',
            'rejected' => 'Rejected',
            default    => 'Under Review',
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'approved' => '#00ffaa',
            'rejected' => '#ff8099',
            default    => '#ffaa00',
        };
    }

    public function fileUrl(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    public function fileSizeLabel(): string
    {
        $bytes = $this->file_size ?? 0;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 0) . ' KB';
        return $bytes . ' B';
    }

    public function isPdf(): bool
    {
        return $this->file_mime === 'application/pdf';
    }

    public function isImage(): bool
    {
        return in_array($this->file_mime, ['image/jpeg', 'image/png', 'image/webp']);
    }
}