@extends('layouts.app')
@section('title', 'My Quote Requests')

@section('content')
<style>
.qr-page { max-width: 860px; margin: 0 auto; padding: 2rem 1.5rem; }
.page-header { display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem; margin-bottom:1.75rem; }
.page-title { font-family:'Orbitron',sans-serif; font-size:1.5rem; font-weight:800; }
.page-title span { color:var(--accent-cyan); }
.stats-row { display:grid; grid-template-columns:repeat(3,1fr); gap:.875rem; margin-bottom:1.75rem; }
.stat-chip { background:var(--card-bg); border:1px solid var(--border-color); border-radius:12px; padding:1rem 1.25rem; text-align:center; }
.stat-num { font-family:'Orbitron',sans-serif; font-size:1.75rem; font-weight:900; }
.stat-lbl { font-size:.72rem; text-transform:uppercase; letter-spacing:.07em; color:var(--text-tertiary); margin-top:.2rem; }
.filter-bar { display:flex; gap:.5rem; margin-bottom:1.5rem; flex-wrap:wrap; }
.filter-btn { padding:.4rem 1rem; border-radius:20px; border:1px solid var(--border-color); background:transparent; color:var(--text-secondary); font-family:'Chakra Petch',sans-serif; font-size:.78rem; font-weight:600; cursor:pointer; text-decoration:none; transition:all .2s; }
.filter-btn.active, .filter-btn:hover { border-color:var(--accent-cyan); color:var(--accent-cyan); background:rgba(0,212,255,.08); }
.quote-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.5rem; margin-bottom:1rem; position:relative; overflow:hidden; transition:border-color .3s; }
.quote-card:hover { border-color:rgba(0,212,255,.25); }
.quote-card-top { position:absolute; top:0; left:0; bottom:0; width:3px; }
.status-pending  .quote-card-top { background:var(--accent-warning); }
.status-quoted   .quote-card-top { background:var(--accent-cyan); }
.status-declined .quote-card-top { background:rgba(255,51,102,.6); }
.status-expired  .quote-card-top { background:rgba(255,255,255,.15); }
.qc-layout { display:grid; grid-template-columns:1fr auto; gap:1.25rem; align-items:start; }
.qc-service { font-family:'Orbitron',sans-serif; font-size:1rem; font-weight:700; margin-bottom:.25rem; }
.qc-provider { font-size:.8rem; color:var(--text-secondary); margin-bottom:.625rem; }
.qc-meta { display:flex; gap:1rem; flex-wrap:wrap; font-size:.78rem; color:var(--text-tertiary); }
.status-pill { display:inline-flex; align-items:center; gap:.35rem; padding:.25rem .75rem; border-radius:20px; font-size:.72rem; font-weight:700; }
.qc-price { font-family:'Orbitron',sans-serif; font-size:1.5rem; font-weight:900; color:var(--accent-cyan); white-space:nowrap; text-align:right; }
.qc-price-label { font-size:.7rem; color:var(--text-tertiary); text-align:right; margin-top:.2rem; }
.btn-view { display:inline-flex; align-items:center; gap:.4rem; padding:.5rem 1rem; background:rgba(0,212,255,.08); border:1px solid rgba(0,212,255,.2); border-radius:8px; color:var(--accent-cyan); font-size:.78rem; font-weight:600; text-decoration:none; transition:all .2s; margin-top:.875rem; }
.btn-view:hover { background:rgba(0,212,255,.15); }
.empty-state { text-align:center; padding:5rem 2rem; color:var(--text-tertiary); }
</style>

<div class="qr-page">
    @if(session('success'))
    <div style="background:rgba(0,255,170,.1);border:1px solid rgba(0,255,170,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-green);font-size:.875rem;">✓ {{ session('success') }}</div>
    @endif
    @if(session('info'))
    <div style="background:rgba(0,212,255,.08);border:1px solid rgba(0,212,255,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-cyan);font-size:.875rem;">ℹ {{ session('info') }}</div>
    @endif

    <div class="page-header">
        <div>
            <div class="page-title">My <span>Quote Requests</span></div>
            <p style="font-size:.825rem;color:var(--text-tertiary);margin-top:.35rem;">Quotes you've requested from specific providers</p>
        </div>
        <a href="{{ route('providers.index') }}"
           style="padding:.65rem 1.25rem;background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green));border:none;border-radius:10px;color:#000;font-family:'Orbitron',sans-serif;font-weight:700;font-size:.75rem;text-decoration:none;white-space:nowrap;">
            + Request a Quote
        </a>
    </div>

    <div class="stats-row">
        <div class="stat-chip">
            <div class="stat-num" style="color:var(--accent-warning);">{{ $stats['pending'] }}</div>
            <div class="stat-lbl">Awaiting Response</div>
        </div>
        <div class="stat-chip">
            <div class="stat-num" style="color:var(--accent-cyan);">{{ $stats['quoted'] }}</div>
            <div class="stat-lbl">Quotes Received</div>
        </div>
        <div class="stat-chip">
            <div class="stat-num" style="color:var(--text-secondary);">{{ $stats['total'] }}</div>
            <div class="stat-lbl">Total Requests</div>
        </div>
    </div>

    <div class="filter-bar">
        <a href="{{ route('quotes.index') }}" class="filter-btn {{ !request('status') ? 'active' : '' }}">All</a>
        @foreach(['pending' => 'Pending', 'quoted' => 'Quoted', 'declined' => 'Declined'] as $s => $l)
        <a href="{{ route('quotes.index', ['status' => $s]) }}" class="filter-btn {{ request('status') === $s ? 'active' : '' }}">{{ $l }}</a>
        @endforeach
    </div>

    @if($quotes->isEmpty())
    <div class="empty-state">
        <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:.2;margin:0 auto 1rem;display:block;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <h3 style="font-family:'Orbitron',sans-serif;font-size:1.1rem;color:var(--text-secondary);margin-bottom:.75rem;">No quote requests yet</h3>
        <p style="font-size:.875rem;">Browse providers and request a quote directly from their profile.</p>
        <a href="{{ route('providers.index') }}" style="display:inline-flex;align-items:center;gap:.5rem;margin-top:1.5rem;padding:.75rem 1.5rem;background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green));border:none;border-radius:10px;color:#000;font-family:'Orbitron',sans-serif;font-weight:700;font-size:.8rem;text-decoration:none;">
            Browse Providers →
        </a>
    </div>
    @else
    @foreach($quotes as $quote)
    <div class="quote-card status-{{ $quote->status }}">
        <div class="quote-card-top"></div>
        <div class="qc-layout">
            <div>
                <div class="qc-service">{{ $quote->service_type }}</div>
                <div class="qc-provider">
                    {{ $quote->provider->name }}
                    @if($quote->provider->is_verified) · <span style="color:var(--accent-green);">✓ Verified</span>@endif
                </div>
                <div class="qc-meta">
                    <span>{{ $quote->urgencyLabel() }}</span>
                    @if($quote->vehicle)<span>🚗 {{ $quote->vehicle->year }} {{ $quote->vehicle->make }} {{ $quote->vehicle->model }}</span>@endif
                    @if($quote->budgetLabel() !== 'Flexible')<span>💰 Budget: {{ $quote->budgetLabel() }}</span>@endif
                    <span>🕐 {{ $quote->created_at->diffForHumans() }}</span>
                </div>
                <div style="margin-top:.625rem;">
                    <span class="status-pill" style="background:{{ $quote->statusColor() }}1a;color:{{ $quote->statusColor() }};border:1px solid {{ $quote->statusColor() }}44;">
                        {{ $quote->statusLabel() }}
                    </span>
                    @if($quote->isQuoted() && is_null($quote->consumer_action))
                    <span style="font-size:.72rem;color:var(--accent-warning);margin-left:.75rem;font-weight:600;">⚡ Action required</span>
                    @elseif($quote->consumer_action === 'accepted')
                    <span style="font-size:.72rem;color:var(--accent-green);margin-left:.75rem;font-weight:600;">✓ You accepted</span>
                    @elseif($quote->consumer_action === 'declined')
                    <span style="font-size:.72rem;color:var(--text-tertiary);margin-left:.75rem;">You declined</span>
                    @endif
                </div>
                <a href="{{ route('quotes.show', $quote) }}" class="btn-view">View Details →</a>
            </div>
            <div>
                @if($quote->isQuoted())
                <div class="qc-price">${{ number_format($quote->quoted_price, 2) }}</div>
                <div class="qc-price-label">quoted price</div>
                @elseif($quote->isPending())
                <div style="font-size:.72rem;color:var(--accent-warning);text-align:right;font-weight:600;white-space:nowrap;">⏳ Awaiting<br>response</div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
    <div style="margin-top:1.5rem;">{{ $quotes->links() }}</div>
    @endif
</div>
@endsection