@extends('layouts.app')
@section('title', 'Service Report Links')
@section('content')
<style>
.sr-wrap { max-width:860px; margin:0 auto; padding:2rem 1.5rem; }
.page-title { font-family:'Orbitron',sans-serif; font-size:1.4rem; font-weight:800; }
.page-title span { color:var(--accent-cyan); }
.share-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.5rem; margin-bottom:1rem; position:relative; overflow:hidden; transition:border-color .3s; }
.share-card:hover { border-color:rgba(0,212,255,.2); }
.share-card::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; background:linear-gradient(90deg,var(--accent-cyan),var(--accent-green)); }
.sc-layout { display:grid; grid-template-columns:1fr auto; gap:1rem; align-items:start; }
.sc-label { font-family:'Orbitron',sans-serif; font-size:.9rem; font-weight:700; margin-bottom:.25rem; }
.sc-vehicle { font-size:.82rem; color:var(--text-secondary); margin-bottom:.5rem; }
.sc-meta { display:flex; gap:1rem; flex-wrap:wrap; font-size:.75rem; color:var(--text-tertiary); margin-bottom:.875rem; }
.sc-url { display:flex; align-items:center; gap:.5rem; padding:.6rem .875rem; background:rgba(0,212,255,.05); border:1px solid rgba(0,212,255,.15); border-radius:8px; font-size:.72rem; font-family:'Chakra Petch',monospace; color:var(--accent-cyan); word-break:break-all; }
.btn-copy { padding:.4rem .875rem; background:rgba(0,212,255,.1); border:1px solid rgba(0,212,255,.25); border-radius:7px; color:var(--accent-cyan); font-size:.72rem; font-weight:700; cursor:pointer; white-space:nowrap; transition:all .2s; }
.btn-copy:hover { background:rgba(0,212,255,.2); }
.action-row { display:flex; gap:.5rem; flex-wrap:wrap; margin-top:.875rem; }
.btn-sm { display:inline-flex; align-items:center; gap:.35rem; padding:.4rem .875rem; border-radius:8px; font-size:.75rem; font-weight:600; text-decoration:none; cursor:pointer; border:none; transition:all .2s; font-family:'Chakra Petch',sans-serif; }
.btn-view   { background:rgba(0,212,255,.08); border:1px solid rgba(0,212,255,.2); color:var(--accent-cyan); }
.btn-delete { background:rgba(255,51,102,.08); border:1px solid rgba(255,51,102,.2); color:#ff8099; }
.expired-badge { display:inline-flex; padding:.2rem .6rem; border-radius:6px; font-size:.7rem; font-weight:700; background:rgba(255,255,255,.06); color:var(--text-tertiary); border:1px solid var(--border-color); }
.active-badge  { display:inline-flex; padding:.2rem .6rem; border-radius:6px; font-size:.7rem; font-weight:700; background:rgba(0,255,170,.1); color:var(--accent-green); border:1px solid rgba(0,255,170,.25); }
.pill { display:inline-flex; padding:.15rem .5rem; border-radius:6px; font-size:.68rem; font-weight:700; margin-right:.35rem; }
.empty-state { text-align:center; padding:5rem 2rem; color:var(--text-tertiary); }
</style>

<div class="sr-wrap">
    @if(session('success'))
    <div style="background:rgba(0,255,170,.1);border:1px solid rgba(0,255,170,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-green);font-size:.875rem;">✓ {{ session('success') }}</div>
    @endif

    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;margin-bottom:1.75rem;">
        <div>
            <div class="page-title">Service Report <span>Links</span></div>
            <p style="font-size:.825rem;color:var(--text-tertiary);margin-top:.35rem;">Shareable links to your vehicle service history — for buyers, insurers, or mechanics</p>
        </div>
        <a href="{{ route('service-history.report.create') }}"
           style="display:inline-flex;align-items:center;gap:.5rem;padding:.7rem 1.25rem;background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green));border:none;border-radius:10px;color:#000;font-family:'Orbitron',sans-serif;font-weight:700;font-size:.75rem;text-decoration:none;">
            + Create Share Link
        </a>
    </div>

    @if($shares->isEmpty())
    <div class="empty-state">
        <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:.2;margin:0 auto 1rem;display:block;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
        <h3 style="font-family:'Orbitron',sans-serif;font-size:1rem;color:var(--text-secondary);margin-bottom:.75rem;">No share links yet</h3>
        <p style="font-size:.875rem;margin-bottom:1.5rem;">Create a shareable link to your vehicle's service history — perfect for selling your car or sharing with an insurance company.</p>
        <a href="{{ route('service-history.report.create') }}"
           style="display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 1.5rem;background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green));border:none;border-radius:10px;color:#000;font-family:'Orbitron',sans-serif;font-weight:700;font-size:.8rem;text-decoration:none;">
            Create Your First Link →
        </a>
    </div>
    @else
    @foreach($shares as $share)
    <div class="share-card">
        <div class="sc-layout">
            <div>
                <div class="sc-label">{{ $share->label ?? 'Service History Report' }}</div>
                <div class="sc-vehicle">🚗 {{ $share->vehicle->year }} {{ $share->vehicle->make }} {{ $share->vehicle->model }}{{ $share->vehicle->vin ? ' · VIN: ' . $share->vehicle->vin : '' }}</div>
                <div class="sc-meta">
                    <span class="{{ $share->isExpired() ? 'expired-badge' : 'active-badge' }}">
                        {{ $share->isExpired() ? '✕ Expired' : '✓ Active' }}
                    </span>
                    <span>{{ $share->expiryLabel() }}</span>
                    <span>👁 {{ $share->view_count }} view{{ $share->view_count !== 1 ? 's' : '' }}</span>
                    @if($share->last_viewed_at)<span>Last viewed {{ $share->last_viewed_at->diffForHumans() }}</span>@endif
                    @if($share->from_date || $share->to_date)<span>📅 {{ $share->from_date?->format('M Y') ?? 'Start' }} – {{ $share->to_date?->format('M Y') ?? 'Now' }}</span>@endif
                </div>
                <div style="margin-bottom:.5rem;">
                    @if($share->include_costs)<span class="pill" style="background:rgba(0,212,255,.1);color:var(--accent-cyan);">💰 Costs</span>@endif
                    @if($share->include_diagnostics)<span class="pill" style="background:rgba(168,85,247,.1);color:#c084fc;">🔧 Diagnostics</span>@endif
                    @if($share->include_provider_details)<span class="pill" style="background:rgba(0,255,170,.1);color:var(--accent-green);">🏪 Providers</span>@endif
                    @if($share->include_photos)<span class="pill" style="background:rgba(255,170,0,.1);color:var(--accent-warning);">📸 Photos</span>@endif
                </div>
                <div class="sc-url">
                    <span id="url-{{ $share->id }}" style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis;max-width:480px;">{{ $share->publicUrl() }}</span>
                    <button class="btn-copy" onclick="copyUrl('{{ $share->publicUrl() }}', this)">Copy</button>
                </div>
                <div class="action-row">
                    <a href="{{ route('service-history.report.show', $share) }}" class="btn-sm btn-view">Manage →</a>
                    <a href="{{ $share->publicUrl() }}" target="_blank" class="btn-sm btn-view">Preview ↗</a>
                    <a href="{{ route('service-history.report.pdf', $share->token) }}" target="_blank" class="btn-sm" style="background:rgba(168,85,247,.08);border:1px solid rgba(168,85,247,.2);color:#c084fc;">📄 Download PDF</a>
                    <form method="POST" action="{{ route('service-history.report.destroy', $share) }}" style="margin:0;" onsubmit="return confirm('Revoke this share link? Anyone with the link will lose access.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-sm btn-delete">Revoke</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @endif
</div>

<script>
function copyUrl(url, btn) {
    navigator.clipboard.writeText(url).then(() => {
        btn.textContent = '✓ Copied!';
        btn.style.background = 'rgba(0,255,170,.15)';
        btn.style.color = 'var(--accent-green)';
        setTimeout(() => { btn.textContent = 'Copy'; btn.style.background = ''; btn.style.color = ''; }, 2000);
    });
}
</script>
@endsection