@extends('provider.layouts.app')
@section('title', 'My Certifications')
@section('content')
<style>
.cert-header { display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem; margin-bottom:1.75rem; }
.page-title { font-family:'Orbitron',sans-serif; font-size:1.4rem; font-weight:800; }
.page-title span { color:var(--accent-cyan); }
.stats-row { display:grid; grid-template-columns:repeat(4,1fr); gap:.875rem; margin-bottom:1.75rem; }
.stat-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:12px; padding:1rem 1.25rem; text-align:center; }
.stat-num { font-family:'Orbitron',sans-serif; font-size:1.75rem; font-weight:900; }
.stat-lbl { font-size:.7rem; text-transform:uppercase; letter-spacing:.07em; color:var(--text-tertiary); margin-top:.2rem; }
.cert-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.5rem; margin-bottom:1rem; position:relative; overflow:hidden; transition:border-color .3s; }
.cert-card:hover { border-color:rgba(0,212,255,.2); }
.cert-card-top { position:absolute; top:0; left:0; right:0; height:2px; }
.status-approved .cert-card-top { background:linear-gradient(90deg,var(--accent-green),var(--accent-cyan)); }
.status-pending  .cert-card-top { background:var(--accent-warning); }
.status-rejected .cert-card-top { background:rgba(255,51,102,.6); }
.cert-layout { display:grid; grid-template-columns:1fr auto; gap:1.5rem; align-items:start; }
.cert-name { font-family:'Orbitron',sans-serif; font-size:1rem; font-weight:700; margin-bottom:.25rem; }
.cert-body { font-size:.8rem; color:var(--text-secondary); margin-bottom:.625rem; }
.cert-meta { display:flex; gap:1rem; flex-wrap:wrap; font-size:.75rem; color:var(--text-tertiary); margin-bottom:.875rem; }
.cert-meta span { display:flex; align-items:center; gap:.35rem; }
.status-pill { display:inline-flex; align-items:center; gap:.35rem; padding:.25rem .75rem; border-radius:20px; font-size:.72rem; font-weight:700; }
.action-row { display:flex; gap:.625rem; flex-wrap:wrap; margin-top:.875rem; }
.btn-sm { display:inline-flex; align-items:center; gap:.4rem; padding:.4rem .875rem; border-radius:8px; font-size:.75rem; font-weight:600; text-decoration:none; cursor:pointer; border:none; transition:all .2s; font-family:'Chakra Petch',sans-serif; }
.btn-view  { background:rgba(0,212,255,.08); border:1px solid rgba(0,212,255,.2); color:var(--accent-cyan); }
.btn-view:hover { background:rgba(0,212,255,.15); }
.btn-hide  { background:rgba(255,255,255,.05); border:1px solid var(--border-color); color:var(--text-tertiary); }
.btn-delete { background:rgba(255,51,102,.08); border:1px solid rgba(255,51,102,.2); color:#ff8099; }
.badge-show { display:inline-flex; align-items:center; gap:.35rem; padding:.2rem .625rem; border-radius:8px; font-size:.7rem; font-weight:700; background:rgba(0,255,170,.1); color:var(--accent-green); border:1px solid rgba(0,255,170,.25); }
.badge-hide { background:rgba(255,255,255,.06); color:var(--text-tertiary); border:1px solid var(--border-color); font-size:.7rem; font-weight:600; padding:.2rem .625rem; border-radius:8px; display:inline-flex; align-items:center; gap:.35rem; }
.admin-note { background:rgba(255,170,0,.06); border:1px solid rgba(255,170,0,.2); border-radius:8px; padding:.75rem 1rem; font-size:.8rem; color:var(--accent-warning); margin-top:.75rem; }
.reject-note { background:rgba(255,51,102,.06); border:1px solid rgba(255,51,102,.2); border-radius:8px; padding:.75rem 1rem; font-size:.8rem; color:#ff8099; margin-top:.75rem; }
.empty-state { text-align:center; padding:5rem 2rem; color:var(--text-tertiary); }
.file-thumb { width:64px; height:64px; background:rgba(0,212,255,.08); border:1px solid rgba(0,212,255,.2); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; flex-shrink:0; }
.verified-banner { background:linear-gradient(135deg,rgba(0,255,170,.08),rgba(0,212,255,.06)); border:1px solid rgba(0,255,170,.25); border-radius:14px; padding:1.1rem 1.5rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; }
</style>

<div class="cert-header">
    <div>
        <div class="page-title">My <span>Certifications</span></div>
        <p style="font-size:.825rem;color:var(--text-tertiary);margin-top:.25rem;">Upload your professional certificates to get verified and earn trust badges on your profile</p>
    </div>
    <a href="{{ route('provider.certifications.create') }}"
       style="display:inline-flex;align-items:center;gap:.5rem;padding:.7rem 1.25rem;background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green));border:none;border-radius:10px;color:#000;font-family:'Orbitron',sans-serif;font-weight:700;font-size:.75rem;text-decoration:none;">
        + Upload Certificate
    </a>
</div>

@if(session('success'))
<div style="background:rgba(0,255,170,.1);border:1px solid rgba(0,255,170,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-green);font-size:.875rem;">✓ {{ session('success') }}</div>
@endif
@if(session('info'))
<div style="background:rgba(0,212,255,.08);border:1px solid rgba(0,212,255,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-cyan);font-size:.875rem;">ℹ {{ session('info') }}</div>
@endif

@if($provider->is_verified && $stats['approved'] > 0)
<div class="verified-banner">
    <span style="font-size:1.5rem;">✅</span>
    <div>
        <div style="font-weight:700;color:var(--accent-green);font-size:.875rem;">Verified Provider</div>
        <div style="font-size:.78rem;color:var(--text-secondary);">Your profile shows the Verified badge to consumers. {{ $stats['approved'] }} certification{{ $stats['approved']!==1?'s':'' }} verified.</div>
    </div>
</div>
@endif

<div class="stats-row">
    <div class="stat-card">
        <div class="stat-num" style="color:var(--text-secondary);">{{ $stats['total'] }}</div>
        <div class="stat-lbl">Total</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:var(--accent-green);">{{ $stats['approved'] }}</div>
        <div class="stat-lbl">Verified</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:var(--accent-warning);">{{ $stats['pending'] }}</div>
        <div class="stat-lbl">Under Review</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#ff8099;">{{ $stats['rejected'] }}</div>
        <div class="stat-lbl">Rejected</div>
    </div>
</div>

@if($certifications->isEmpty())
<div class="empty-state">
    <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:.2;margin:0 auto 1rem;display:block;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
    <h3 style="font-family:'Orbitron',sans-serif;font-size:1rem;color:var(--text-secondary);margin-bottom:.75rem;">No certifications yet</h3>
    <p style="font-size:.875rem;max-width:360px;margin:0 auto 1.5rem;">Upload your professional certificates to build trust with consumers and get the Verified badge on your profile.</p>
    <a href="{{ route('provider.certifications.create') }}" style="display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 1.5rem;background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green));border:none;border-radius:10px;color:#000;font-family:'Orbitron',sans-serif;font-weight:700;font-size:.8rem;text-decoration:none;">
        Upload Your First Certificate →
    </a>
</div>
@else
@foreach($certifications as $cert)
<div class="cert-card status-{{ $cert->status }}">
    <div class="cert-card-top"></div>
    <div class="cert-layout">
        <div>
            <div class="cert-name">{{ $cert->name }}</div>
            <div class="cert-body">
                {{ $cert->issuing_body }}
                @if($cert->certificate_number) · #{{ $cert->certificate_number }}@endif
            </div>
            <div class="cert-meta">
                @if($cert->issued_at)<span>📅 Issued {{ $cert->issued_at->format('M d, Y') }}</span>@endif
                @if($cert->expires_at)
                    <span style="{{ $cert->isExpired() ? 'color:#ff8099;' : ($cert->isExpiringSoon() ? 'color:var(--accent-warning);' : '') }}">
                        {{ $cert->isExpired() ? '⚠ Expired' : '🗓 Expires' }} {{ $cert->expires_at->format('M d, Y') }}
                    </span>
                @else
                    <span>🔄 No expiry</span>
                @endif
                <span>📎 {{ $cert->fileSizeLabel() }}</span>
            </div>

            <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
                <span class="status-pill" style="background:{{ $cert->statusColor() }}1a;color:{{ $cert->statusColor() }};border:1px solid {{ $cert->statusColor() }}44;">
                    @if($cert->isApproved())✓ @elseif($cert->isPending())⏳ @else✕ @endif
                    {{ $cert->statusLabel() }}
                </span>
                @if($cert->isApproved())
                    <span class="{{ $cert->show_on_profile ? 'badge-show' : 'badge-hide' }}">
                        {{ $cert->show_on_profile ? '👁 Showing on profile' : '🙈 Hidden from profile' }}
                    </span>
                @endif
            </div>

            @if($cert->isRejected() && $cert->admin_notes)
            <div class="reject-note">
                <strong>Rejection reason:</strong> {{ $cert->admin_notes }}
                <br><span style="font-size:.75rem;opacity:.8;">You can delete this and re-upload with the correct document.</span>
            </div>
            @elseif($cert->isPending())
            <div class="admin-note">⏳ Under review — our team typically reviews within 24 hours.</div>
            @endif

            <div class="action-row">
                <a href="{{ $cert->fileUrl() }}" target="_blank" class="btn-sm btn-view">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    View File
                </a>
                @if($cert->isApproved())
                <form method="POST" action="{{ route('provider.certifications.toggle', $cert) }}" style="margin:0;">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-sm btn-hide">
                        {{ $cert->show_on_profile ? '🙈 Hide from Profile' : '👁 Show on Profile' }}
                    </button>
                </form>
                @endif
                @if(!$cert->isPending())
                <form method="POST" action="{{ route('provider.certifications.destroy', $cert) }}" style="margin:0;"
                    onsubmit="return confirm('Delete this certification?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-sm btn-delete">Delete</button>
                </form>
                @endif
            </div>
        </div>

        <div class="file-thumb">
            @if($cert->isPdf()) 📄
            @elseif($cert->isImage()) 🖼
            @else 📎
            @endif
        </div>
    </div>
</div>
@endforeach
@endif
@endsection