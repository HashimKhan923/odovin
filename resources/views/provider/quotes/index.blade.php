@extends('provider.layouts.app')
@section('title', 'Quote Requests')

@section('content')
<style>
.pg { padding: 0; }
.page-title { font-family:'Orbitron',sans-serif; font-size:1.4rem; font-weight:800; margin-bottom:.35rem; }
.page-title span { color:var(--accent-cyan); }
.stats-row { display:grid; grid-template-columns:repeat(3,1fr); gap:.875rem; margin-bottom:1.5rem; }
.stat-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:12px; padding:1.1rem 1.25rem; text-align:center; position:relative; overflow:hidden; }
.stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; }
.sc-pending::before  { background:var(--accent-warning); }
.sc-quoted::before   { background:var(--accent-cyan); }
.sc-accepted::before { background:var(--accent-green); }
.stat-num { font-family:'Orbitron',sans-serif; font-size:1.75rem; font-weight:900; }
.stat-lbl { font-size:.7rem; text-transform:uppercase; letter-spacing:.07em; color:var(--text-tertiary); margin-top:.2rem; }
.filter-bar { display:flex; gap:.5rem; margin-bottom:1.25rem; flex-wrap:wrap; }
.filter-btn { padding:.4rem 1rem; border-radius:20px; border:1px solid var(--border-color); background:transparent; color:var(--text-secondary); font-family:'Chakra Petch',sans-serif; font-size:.78rem; font-weight:600; cursor:pointer; text-decoration:none; transition:all .2s; }
.filter-btn.active, .filter-btn:hover { border-color:var(--accent-cyan); color:var(--accent-cyan); background:rgba(0,212,255,.08); }
.qr-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:14px; padding:1.5rem; margin-bottom:.875rem; position:relative; overflow:hidden; transition:border-color .3s; }
.qr-card:hover { border-color:rgba(0,212,255,.25); }
.qr-card::before { content:''; position:absolute; top:0; left:0; bottom:0; width:3px; }
.qrc-pending::before  { background:var(--accent-warning); }
.qrc-quoted::before   { background:var(--accent-cyan); }
.qrc-declined::before { background:rgba(255,255,255,.1); }
.qr-layout { display:grid; grid-template-columns:1fr auto; gap:1rem; align-items:start; }
.qr-service { font-family:'Orbitron',sans-serif; font-size:.95rem; font-weight:700; margin-bottom:.25rem; }
.qr-consumer { font-size:.8rem; color:var(--text-secondary); margin-bottom:.5rem; }
.qr-meta { display:flex; gap:.875rem; flex-wrap:wrap; font-size:.75rem; color:var(--text-tertiary); }
.urgency-pip { font-weight:700; }
.btn-respond { display:inline-flex; align-items:center; gap:.4rem; padding:.5rem 1.1rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:8px; color:#000; font-family:'Orbitron',sans-serif; font-weight:700; font-size:.72rem; cursor:pointer; text-decoration:none; transition:all .2s; margin-top:.875rem; }
.btn-respond:hover { transform:translateY(-1px); }
.btn-view { display:inline-flex; align-items:center; gap:.4rem; padding:.5rem 1rem; background:rgba(0,212,255,.08); border:1px solid rgba(0,212,255,.2); border-radius:8px; color:var(--accent-cyan); font-size:.78rem; text-decoration:none; margin-top:.875rem; }
.status-pill { display:inline-flex; align-items:center; gap:.3rem; padding:.2rem .625rem; border-radius:12px; font-size:.7rem; font-weight:700; }
.empty-state { text-align:center; padding:5rem 2rem; color:var(--text-tertiary); }
</style>

<div class="pg">
    @if(session('success'))
    <div style="background:rgba(0,255,170,.1);border:1px solid rgba(0,255,170,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-green);font-size:.875rem;">✓ {{ session('success') }}</div>
    @endif
    @if(session('info'))
    <div style="background:rgba(0,212,255,.08);border:1px solid rgba(0,212,255,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-cyan);font-size:.875rem;">ℹ {{ session('info') }}</div>
    @endif

    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem;">
        <div>
            <div class="page-title"><span>Quote</span> Requests</div>
            <p style="font-size:.825rem;color:var(--text-tertiary);margin-top:.25rem;">Customers requesting personalised quotes from you directly</p>
        </div>
    </div>

    <div class="stats-row">
        <div class="stat-card sc-pending">
            <div class="stat-num" style="color:var(--accent-warning);">{{ $stats['pending'] }}</div>
            <div class="stat-lbl">Need Response</div>
        </div>
        <div class="stat-card sc-quoted">
            <div class="stat-num" style="color:var(--accent-cyan);">{{ $stats['quoted'] }}</div>
            <div class="stat-lbl">Quotes Sent</div>
        </div>
        <div class="stat-card sc-accepted">
            <div class="stat-num" style="color:var(--accent-green);">{{ $stats['accepted'] }}</div>
            <div class="stat-lbl">Converted to Jobs</div>
        </div>
    </div>

    <div class="filter-bar">
        <a href="{{ route('provider.quotes.index') }}" class="filter-btn {{ !request('status') ? 'active' : '' }}">All</a>
        @foreach(['pending' => 'Needs Response', 'quoted' => 'Quoted', 'declined' => 'Declined'] as $s => $l)
        <a href="{{ route('provider.quotes.index', ['status' => $s]) }}" class="filter-btn {{ request('status') === $s ? 'active' : '' }}">{{ $l }}</a>
        @endforeach
    </div>

    @if($quotes->isEmpty())
    <div class="empty-state">
        <svg width="52" height="52" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:.2;margin:0 auto 1rem;display:block;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
        <h3 style="font-family:'Orbitron',sans-serif;font-size:1rem;color:var(--text-secondary);margin-bottom:.625rem;">No quote requests yet</h3>
        <p style="font-size:.825rem;">When customers request quotes from your profile, they'll appear here.</p>
    </div>
    @else
    @foreach($quotes as $quote)
    <div class="qr-card qrc-{{ $quote->status }}">
        <div class="qr-layout">
            <div>
                <div class="qr-service">{{ $quote->service_type }}</div>
                <div class="qr-consumer">
                    {{ $quote->user->name }}
                    @if($quote->vehicle) · {{ $quote->vehicle->year }} {{ $quote->vehicle->make }} {{ $quote->vehicle->model }}@endif
                </div>
                <div class="qr-meta">
                    <span class="urgency-pip">{{ $quote->urgencyLabel() }}</span>
                    @if($quote->budgetLabel() !== 'Flexible')<span>💰 {{ $quote->budgetLabel() }}</span>@endif
                    @if($quote->preferred_date)<span>📅 {{ \Carbon\Carbon::parse($quote->preferred_date)->format('M d') }}</span>@endif
                    <span>{{ $quote->created_at->diffForHumans() }}</span>
                </div>
                <div style="margin-top:.5rem;">
                    <span class="status-pill" style="background:{{ $quote->statusColor() }}1a;color:{{ $quote->statusColor() }};border:1px solid {{ $quote->statusColor() }}33;">
                        {{ $quote->statusLabel() }}
                    </span>
                    @if($quote->consumer_action === 'accepted')
                    <span style="font-size:.72rem;color:var(--accent-green);margin-left:.625rem;font-weight:600;">✓ Customer accepted → job posted</span>
                    @endif
                </div>
                @if($quote->isPending())
                <a href="{{ route('provider.quotes.show', $quote) }}" class="btn-respond">Respond to Quote →</a>
                @else
                <a href="{{ route('provider.quotes.show', $quote) }}" class="btn-view">View →</a>
                @endif
            </div>
            <div style="text-align:right;">
                @if($quote->isQuoted())
                <div style="font-family:'Orbitron',sans-serif;font-size:1.35rem;font-weight:900;color:var(--accent-cyan);">${{ number_format($quote->quoted_price, 2) }}</div>
                <div style="font-size:.7rem;color:var(--text-tertiary);margin-top:.15rem;">your quote</div>
                @elseif($quote->isPending())
                <div style="font-size:.72rem;color:var(--accent-warning);font-weight:700;white-space:nowrap;">⚡ Action<br>needed</div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
    <div style="margin-top:1.25rem;">{{ $quotes->links() }}</div>
    @endif
</div>
@endsection