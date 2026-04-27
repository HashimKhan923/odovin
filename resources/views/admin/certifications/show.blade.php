@extends('admin.layouts.app')
@section('title', 'Review Certification')
@section('content')
<style>
.review-wrap { max-width: 900px; margin: 0 auto; }
.cert-detail-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.75rem; margin-bottom:1.25rem; }
.detail-title { font-family:'Orbitron',sans-serif; font-size:.875rem; font-weight:700; margin-bottom:1.25rem; padding-bottom:.75rem; border-bottom:1px solid var(--border-color); }
.detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
.detail-item { padding:.75rem 0; }
.detail-label { font-size:.72rem; color:var(--text-tertiary); text-transform:uppercase; letter-spacing:.06em; margin-bottom:.3rem; }
.detail-value { font-size:.875rem; color:var(--text-primary); font-weight:600; }
.doc-preview { background:rgba(0,212,255,.04); border:1px solid rgba(0,212,255,.15); border-radius:14px; padding:1.5rem; text-align:center; }
.btn-approve { padding:.875rem 2rem; background:linear-gradient(135deg,var(--accent-green),var(--accent-cyan)); border:none; border-radius:10px; color:#000; font-family:'Orbitron',sans-serif; font-weight:800; font-size:.8rem; cursor:pointer; transition:all .3s; }
.btn-approve:hover { transform:translateY(-1px); box-shadow:0 4px 15px rgba(0,255,170,.3); }
.btn-reject  { padding:.875rem 2rem; background:rgba(255,51,102,.1); border:1px solid rgba(255,51,102,.3); border-radius:10px; color:#ff8099; font-family:'Orbitron',sans-serif; font-weight:700; font-size:.8rem; cursor:pointer; transition:all .2s; }
.btn-reject:hover { background:rgba(255,51,102,.18); }
.field-input, .field-textarea { width:100%; padding:.75rem 1rem; background:rgba(0,212,255,.04); border:1px solid var(--border-color); border-radius:10px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.875rem; transition:border-color .2s; }
.field-input:focus, .field-textarea:focus { outline:none; border-color:var(--accent-cyan); }
.field-textarea { resize:vertical; min-height:80px; line-height:1.6; }
.section-sep { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--text-tertiary); margin:1.5rem 0 1rem; padding-bottom:.5rem; border-bottom:1px solid var(--border-color); }
</style>

<div class="review-wrap">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem;">
        <div>
            <a href="{{ route('admin.certifications.index') }}" class="btn btn-secondary btn-sm">← Back to list</a>
        </div>
        <div style="display:flex;align-items:center;gap:.75rem;">
            <span style="display:inline-flex;align-items:center;gap:.35rem;padding:.3rem .875rem;border-radius:20px;font-size:.75rem;font-weight:700;background:{{ $certification->statusColor() }}1a;color:{{ $certification->statusColor() }};border:1px solid {{ $certification->statusColor() }}33;">
                {{ $certification->statusLabel() }}
            </span>
            @if($certification->reviewed_at)
            <span style="font-size:.75rem;color:var(--text-tertiary);">Reviewed {{ $certification->reviewed_at->diffForHumans() }} by {{ $certification->reviewer?->name ?? 'Admin' }}</span>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success" style="margin-bottom:1.5rem;">✓ {{ session('success') }}</div>
    @endif
    @if(session('info'))
    <div class="alert alert-info" style="margin-bottom:1.5rem;">ℹ {{ session('info') }}</div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 380px;gap:1.25rem;align-items:start;">
        <div>
            {{-- Certification details --}}
            <div class="cert-detail-card">
                <div class="detail-title">📋 Certification Details</div>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Name</div>
                        <div class="detail-value">{{ $certification->name }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Issuing Organization</div>
                        <div class="detail-value">{{ $certification->issuing_body }}</div>
                    </div>
                    @if($certification->certificate_number)
                    <div class="detail-item">
                        <div class="detail-label">Certificate Number</div>
                        <div class="detail-value">{{ $certification->certificate_number }}</div>
                    </div>
                    @endif
                    @if($certification->issued_at)
                    <div class="detail-item">
                        <div class="detail-label">Issued</div>
                        <div class="detail-value">{{ $certification->issued_at->format('M d, Y') }}</div>
                    </div>
                    @endif
                    <div class="detail-item">
                        <div class="detail-label">Expiry</div>
                        <div class="detail-value" style="{{ $certification->isExpired() ? 'color:#ff8099;' : '' }}">
                            {{ $certification->expires_at ? $certification->expires_at->format('M d, Y') . ($certification->isExpired() ? ' (Expired)' : '') : 'Never expires' }}
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Submitted</div>
                        <div class="detail-value">{{ $certification->created_at->format('M d, Y · g:i A') }}</div>
                    </div>
                </div>
            </div>

            {{-- Provider info --}}
            <div class="cert-detail-card">
                <div class="detail-title">🔧 Provider</div>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Business Name</div>
                        <div class="detail-value">{{ $certification->provider->business_name }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Type</div>
                        <div class="detail-value">{{ ucfirst(str_replace('_', ' ', $certification->provider->type)) }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Location</div>
                        <div class="detail-value">{{ $certification->provider->city }}, {{ $certification->provider->state }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Currently Verified</div>
                        <div class="detail-value" style="{{ $certification->provider->is_verified ? 'color:var(--accent-green)' : 'color:var(--text-tertiary)' }}">
                            {{ $certification->provider->is_verified ? '✓ Yes' : '✗ No' }}
                        </div>
                    </div>
                </div>
                <a href="{{ route('admin.providers.show', $certification->provider) }}"
                   style="display:inline-flex;align-items:center;gap:.4rem;margin-top:.5rem;padding:.5rem 1rem;background:rgba(0,212,255,.08);border:1px solid rgba(0,212,255,.2);border-radius:8px;color:var(--accent-cyan);font-size:.78rem;text-decoration:none;">
                   View Full Provider Profile →
                </a>
            </div>

            {{-- Previous admin notes --}}
            @if($certification->admin_notes)
            <div class="cert-detail-card">
                <div class="detail-title">💬 Admin Notes</div>
                <div style="font-size:.875rem;color:var(--text-secondary);line-height:1.7;">{{ $certification->admin_notes }}</div>
            </div>
            @endif
        </div>

        <div style="position:sticky;top:5rem;">
            {{-- Document preview --}}
            <div class="cert-detail-card" style="margin-bottom:1.25rem;">
                <div class="detail-title">📄 Document</div>
                <div class="doc-preview">
                    <div style="font-size:3rem;margin-bottom:.75rem;">{{ $certification->isPdf() ? '📄' : '🖼' }}</div>
                    <div style="font-size:.8rem;color:var(--text-secondary);margin-bottom:.5rem;word-break:break-all;">{{ $certification->file_original_name }}</div>
                    <div style="font-size:.72rem;color:var(--text-tertiary);margin-bottom:1rem;">{{ $certification->fileSizeLabel() }} · {{ strtoupper(pathinfo($certification->file_original_name, PATHINFO_EXTENSION)) }}</div>
                    <a href="{{ $certification->fileUrl() }}" target="_blank"
                       style="display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 1.25rem;background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green));border:none;border-radius:10px;color:#000;font-family:'Orbitron',sans-serif;font-weight:700;font-size:.75rem;text-decoration:none;">
                        Open Document ↗
                    </a>
                </div>
            </div>

            {{-- Review actions --}}
            @if($certification->isPending())
            <div class="cert-detail-card">
                <div class="detail-title">✅ Review Actions</div>

                {{-- Approve --}}
                <form method="POST" action="{{ route('admin.certifications.approve', $certification) }}" style="margin-bottom:1rem;">
                    @csrf
                    <div style="margin-bottom:.875rem;">
                        <label style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-tertiary);display:block;margin-bottom:.4rem;">
                            Notes for provider (optional)
                        </label>
                        <input type="text" name="admin_notes" class="field-input" placeholder="e.g. Certificate verified against ASE registry">
                    </div>
                    <button type="submit" class="btn-approve" style="width:100%;"
                        onclick="return confirm('Approve this certification and notify the provider?')">
                        ✓ Approve & Verify
                    </button>
                </form>

                <div style="height:1px;background:var(--border-color);margin:1rem 0;"></div>

                {{-- Reject --}}
                <form method="POST" action="{{ route('admin.certifications.reject', $certification) }}">
                    @csrf
                    <div style="margin-bottom:.875rem;">
                        <label style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#ff8099;display:block;margin-bottom:.4rem;">
                            Rejection Reason <span style="color:#ff8099;">*</span>
                        </label>
                        <textarea name="admin_notes" class="field-textarea" required
                            placeholder="e.g. Document is blurry, cannot read details. Please re-upload a clearer scan."></textarea>
                    </div>
                    <button type="submit" class="btn-reject" style="width:100%;"
                        onclick="return confirm('Reject this certification and notify the provider?')">
                        ✕ Reject
                    </button>
                </form>
            </div>
            @elseif($certification->isApproved())
            <div style="background:rgba(0,255,170,.06);border:1px solid rgba(0,255,170,.2);border-radius:14px;padding:1.25rem;text-align:center;">
                <div style="font-size:2rem;margin-bottom:.5rem;">✅</div>
                <div style="font-weight:700;color:var(--accent-green);margin-bottom:.25rem;">Verified</div>
                <div style="font-size:.78rem;color:var(--text-tertiary);">Approved {{ $certification->reviewed_at?->format('M d, Y') }}</div>
            </div>
            @else
            <div style="background:rgba(255,51,102,.06);border:1px solid rgba(255,51,102,.2);border-radius:14px;padding:1.25rem;text-align:center;">
                <div style="font-size:2rem;margin-bottom:.5rem;">✕</div>
                <div style="font-weight:700;color:#ff8099;margin-bottom:.25rem;">Rejected</div>
                <div style="font-size:.78rem;color:var(--text-tertiary);">{{ $certification->reviewed_at?->format('M d, Y') }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection