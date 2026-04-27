@extends('layouts.app')
@section('title', 'Manage Report Link')
@section('content')
<style>
.sr-show { max-width:800px; margin:0 auto; padding:2rem 1.5rem; }
.back-link { display:inline-flex;align-items:center;gap:.5rem;color:var(--accent-cyan);text-decoration:none;font-size:.875rem;font-weight:600;margin-bottom:1.5rem;transition:gap .2s; }
.back-link:hover { gap:.875rem; }
.hero { background:var(--card-bg);border:1px solid var(--border-color);border-radius:18px;padding:1.75rem;margin-bottom:1.25rem;position:relative;overflow:hidden; }
.hero::before { content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--accent-cyan),var(--accent-green)); }
.url-box { display:flex;align-items:center;gap:.75rem;padding:.875rem 1rem;background:rgba(0,212,255,.05);border:1px solid rgba(0,212,255,.2);border-radius:10px;margin:1rem 0; }
.url-text { flex:1;font-size:.8rem;font-family:'Chakra Petch',monospace;color:var(--accent-cyan);word-break:break-all; }
.btn-copy { padding:.5rem 1rem;background:rgba(0,212,255,.12);border:1px solid rgba(0,212,255,.3);border-radius:8px;color:var(--accent-cyan);font-size:.75rem;font-weight:700;cursor:pointer;white-space:nowrap; }
.share-actions { display:flex;gap:.75rem;flex-wrap:wrap;margin-top:1rem; }
.btn-action { display:inline-flex;align-items:center;gap:.5rem;padding:.65rem 1.1rem;border-radius:9px;font-size:.78rem;font-weight:600;text-decoration:none;cursor:pointer;border:none;transition:all .2s; }
.btn-pdf      { background:rgba(168,85,247,.1);border:1px solid rgba(168,85,247,.25);color:#c084fc; }
.btn-preview  { background:rgba(0,212,255,.08);border:1px solid rgba(0,212,255,.2);color:var(--accent-cyan); }
.btn-revoke   { background:rgba(255,51,102,.08);border:1px solid rgba(255,51,102,.2);color:#ff8099; }
.card { background:var(--card-bg);border:1px solid var(--border-color);border-radius:14px;padding:1.5rem;margin-bottom:1.25rem; }
.card-title { font-family:'Orbitron',sans-serif;font-size:.85rem;font-weight:700;margin-bottom:1rem;padding-bottom:.75rem;border-bottom:1px solid var(--border-color); }
.record-row { padding:.875rem 0;border-bottom:1px solid rgba(0,212,255,.04);display:flex;justify-content:space-between;align-items:flex-start;gap:1rem; }
.record-row:last-child { border:none; }
.rr-type { font-weight:700;color:var(--text-primary);font-size:.875rem;margin-bottom:.2rem; }
.rr-date { font-size:.75rem;color:var(--text-tertiary); }
.rr-cost { font-family:'Orbitron',sans-serif;font-size:.9rem;font-weight:700;color:var(--accent-green);white-space:nowrap; }
</style>

<div class="sr-show">
    @if(session('success'))
    <div style="background:rgba(0,255,170,.1);border:1px solid rgba(0,255,170,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-green);font-size:.875rem;">✓ {{ session('success') }}</div>
    @endif

    <a href="{{ route('service-history.report.index') }}" class="back-link">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Share Links
    </a>

    {{-- Hero --}}
    <div class="hero">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:.75rem;">
            <div>
                <div style="font-family:'Orbitron',sans-serif;font-size:1.1rem;font-weight:800;">{{ $share->label ?? 'Service History Report' }}</div>
                <div style="font-size:.82rem;color:var(--text-secondary);margin-top:.25rem;">
                    🚗 {{ $share->vehicle->year }} {{ $share->vehicle->make }} {{ $share->vehicle->model }}
                    @if($share->vehicle->vin) · VIN: {{ $share->vehicle->vin }}@endif
                </div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:.72rem;color:var(--text-tertiary);">{{ $share->view_count }} view{{ $share->view_count !== 1 ? 's' : '' }}</div>
                <div style="font-size:.78rem;margin-top:.2rem;color:{{ $share->isExpired() ? '#ff8099' : 'var(--accent-green)' }};">
                    {{ $share->isExpired() ? '✕ Expired' : '✓ Active' }} · {{ $share->expiryLabel() }}
                </div>
            </div>
        </div>

        <div class="url-box">
            <span class="url-text">{{ $share->publicUrl() }}</span>
            <button class="btn-copy" onclick="copyUrl('{{ $share->publicUrl() }}', this)">Copy Link</button>
        </div>

        <div class="share-actions">
            <a href="{{ $share->publicUrl() }}" target="_blank" class="btn-action btn-preview">👁 Preview →</a>
            <a href="{{ route('service-history.report.pdf', $share->token) }}" target="_blank" class="btn-action btn-pdf">📄 Download PDF</a>
            <form method="POST" action="{{ route('service-history.report.destroy', $share) }}" style="margin:0;" onsubmit="return confirm('Revoke this link permanently?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-action btn-revoke">🗑 Revoke Link</button>
            </form>
        </div>
    </div>

    {{-- Records preview --}}
    <div class="card">
        <div class="card-title">📋 Included Records ({{ $records->count() }})</div>
        @forelse($records as $record)
        <div class="record-row">
            <div>
                <div class="rr-type">{{ $record->service_type }}</div>
                <div class="rr-date">{{ $record->service_date->format('M d, Y') }}
                    @if($share->include_provider_details && $record->serviceProvider) · {{ $record->serviceProvider->business_name }}@endif
                </div>
            </div>
            @if($share->include_costs && $record->cost)
            <div class="rr-cost">${{ number_format($record->cost, 2) }}</div>
            @endif
        </div>
        @empty
        <div style="font-size:.875rem;color:var(--text-tertiary);text-align:center;padding:2rem;">No service records match the selected date range.</div>
        @endforelse
    </div>
</div>

<script>
function copyUrl(url, btn) {
    navigator.clipboard.writeText(url).then(() => {
        btn.textContent = '✓ Copied!';
        setTimeout(() => { btn.textContent = 'Copy Link'; }, 2000);
    });
}
</script>
@endsection