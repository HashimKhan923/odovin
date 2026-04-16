@extends('provider.layouts.app')
@section('title', 'My Submitted Offers')
@section('content')
<style>
.pg-container { padding:2rem; }
.page-title { font-family:'Orbitron',sans-serif; font-size:1.5rem; font-weight:800; margin-bottom:.35rem; }
.page-title span { color:var(--accent-cyan); }
.tab-row { display:flex; gap:.5rem; margin:1.25rem 0; flex-wrap:wrap; }
.tab-btn { padding:.5rem 1.25rem; border-radius:20px; border:1px solid var(--border-color); background:transparent; color:var(--text-secondary); font-family:'Chakra Petch',sans-serif; font-size:.8rem; font-weight:600; cursor:pointer; text-decoration:none; transition:all .3s; }
.tab-btn.active { border-color:var(--accent-cyan); color:var(--accent-cyan); background:rgba(0,212,255,.08); }
.stats-row { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1.5rem; }
.stat-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:14px; padding:1.25rem 1.5rem; text-align:center; position:relative; overflow:hidden; }
.stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; }
.stat-pending::before  { background:var(--accent-warning); }
.stat-accepted::before { background:var(--accent-green); }
.stat-rejected::before { background:rgba(255,255,255,.2); }
.stat-num { font-family:'Orbitron',sans-serif; font-size:2rem; font-weight:900; }
.stat-lbl { font-size:.75rem; color:var(--text-tertiary); text-transform:uppercase; letter-spacing:.08em; margin-top:.25rem; }
.filter-bar { background:var(--card-bg); border:1px solid var(--border-color); border-radius:14px; padding:1rem 1.5rem; margin-bottom:1.5rem; }
.filter-select { padding:.5rem .875rem; background:rgba(0,212,255,.05); border:1px solid var(--border-color); border-radius:8px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.85rem; }
.offers-list { display:flex; flex-direction:column; gap:1rem; }
.offer-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.5rem; position:relative; overflow:hidden; transition:all .3s; }
.offer-card::before { content:''; position:absolute; top:0; left:0; bottom:0; width:3px; }
.offer-pending::before  { background:var(--accent-warning); }
.offer-accepted::before { background:var(--accent-green); }
.offer-rejected::before { background:rgba(255,255,255,.15); }
.offer-card.offer-rejected { opacity:.6; }
.offer-layout { display:grid; grid-template-columns:1fr auto; gap:1.5rem; align-items:start; }
.offer-job-type { font-family:'Orbitron',sans-serif; font-size:1.1rem; font-weight:700; margin-bottom:.35rem; }
.offer-job-num { font-size:.75rem; color:var(--text-tertiary); }
.offer-price { font-family:'Orbitron',sans-serif; font-size:1.75rem; font-weight:900; color:var(--accent-warning); white-space:nowrap; text-align:right; }
.status-badge { display:inline-flex; align-items:center; gap:.4rem; padding:.35rem .875rem; border-radius:20px; font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; }
.status-pending  { background:rgba(255,170,0,.15); color:var(--accent-warning); border:1px solid rgba(255,170,0,.3); }
.status-accepted { background:rgba(0,255,170,.15); color:var(--accent-green); border:1px solid rgba(0,255,170,.3); }
.status-rejected { background:rgba(255,255,255,.06); color:var(--text-tertiary); border:1px solid rgba(255,255,255,.1); }
.meta-strip { display:flex; gap:1.5rem; flex-wrap:wrap; margin-top:.875rem; font-size:.825rem; color:var(--text-secondary); }
.meta-item { display:flex; align-items:center; gap:.4rem; }
.meta-item svg { width:14px; height:14px; color:var(--accent-cyan); }
.btn-view { display:inline-flex; align-items:center; gap:.5rem; padding:.5rem 1.1rem; background:rgba(0,212,255,.08); border:1px solid rgba(0,212,255,.25); border-radius:8px; color:var(--accent-cyan); font-size:.8rem; font-weight:600; text-decoration:none; margin-top:1rem; transition:all .3s; }
.btn-view:hover { background:rgba(0,212,255,.15); }
.empty-state { text-align:center; padding:5rem 2rem; color:var(--text-tertiary); }

/* ── Counter-offer styles ── */
.counter-incoming { margin-top:.875rem; padding:1rem 1.1rem; background:rgba(255,170,0,.07); border:1px solid rgba(255,170,0,.3); border-radius:10px; }
.counter-incoming-title { font-size:.72rem; font-weight:700; letter-spacing:.05em; color:var(--accent-warning); text-transform:uppercase; margin-bottom:.625rem; }
.counter-price-row { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:.75rem; margin-bottom:.875rem; }
.counter-price-info { font-size:.875rem; color:var(--text-secondary); }
.counter-price-val { font-family:'Orbitron',sans-serif; font-size:1.1rem; font-weight:800; color:var(--accent-warning); }
.counter-meta { font-size:.72rem; color:var(--text-tertiary); margin-top:.25rem; }
.counter-actions { display:flex; gap:.625rem; flex-shrink:0; }
.btn-accept-counter { padding:.6rem 1.1rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:8px; color:#000; font-family:'Orbitron',sans-serif; font-weight:700; font-size:.72rem; cursor:pointer; transition:all .25s; white-space:nowrap; }
.btn-accept-counter:hover { transform:translateY(-1px); box-shadow:0 4px 15px rgba(0,212,255,.3); }
.btn-reject-counter { padding:.6rem 1rem; background:rgba(255,51,102,.1); border:1px solid rgba(255,51,102,.3); border-radius:8px; color:#ff8099; font-family:'Orbitron',sans-serif; font-weight:700; font-size:.72rem; cursor:pointer; transition:all .25s; white-space:nowrap; }
.btn-reject-counter:hover { background:rgba(255,51,102,.18); }
.counter-accepted-badge { margin-top:.875rem; padding:.75rem 1rem; background:rgba(0,255,170,.06); border:1px solid rgba(0,255,170,.2); border-radius:10px; font-size:.82rem; }
.counter-rejected-note { margin-top:.875rem; padding:.75rem 1rem; background:rgba(255,255,255,.03); border:1px solid rgba(255,255,255,.08); border-radius:10px; font-size:.82rem; color:var(--text-tertiary); }
</style>

<div class="pg-container">
    <div class="page-title">My <span>Submitted Offers</span></div>
    <p style="color:var(--text-tertiary);font-size:.85rem;">Track all the job offers you've submitted</p>

    @if(session('success'))
    <div style="background:rgba(0,255,170,.1);border:1px solid rgba(0,255,170,.3);border-radius:10px;padding:1rem 1.25rem;margin-top:1rem;color:var(--accent-green);font-size:.875rem;">✓ {{ session('success') }}</div>
    @endif
    @if(session('info'))
    <div style="background:rgba(0,212,255,.08);border:1px solid rgba(0,212,255,.3);border-radius:10px;padding:1rem 1.25rem;margin-top:1rem;color:var(--accent-cyan);font-size:.875rem;">ℹ {{ session('info') }}</div>
    @endif
    @if(session('error'))
    <div style="background:rgba(255,51,102,.1);border:1px solid rgba(255,51,102,.3);border-radius:10px;padding:1rem 1.25rem;margin-top:1rem;color:#ff8099;font-size:.875rem;">{{ session('error') }}</div>
    @endif

    <div class="tab-row">
        <a href="{{ route('provider.jobs.index') }}" class="tab-btn">🗺 Open Jobs Nearby</a>
        <a href="{{ route('provider.jobs.my-offers') }}" class="tab-btn active">📋 My Submitted Offers</a>
    </div>

    <div class="stats-row">
        <div class="stat-card stat-pending">
            <div class="stat-num" style="color:var(--accent-warning)">{{ $stats['pending'] }}</div>
            <div class="stat-lbl">Pending</div>
        </div>
        <div class="stat-card stat-accepted">
            <div class="stat-num" style="color:var(--accent-green)">{{ $stats['accepted'] }}</div>
            <div class="stat-lbl">Accepted</div>
        </div>
        <div class="stat-card stat-rejected">
            <div class="stat-num" style="color:var(--text-tertiary)">{{ $stats['rejected'] }}</div>
            <div class="stat-lbl">Not Selected</div>
        </div>
    </div>

    <div class="filter-bar">
        <form method="GET" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:center;">
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                @foreach(['pending','accepted','rejected'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if($offers->isEmpty())
    <div class="empty-state">
        <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:.25;margin-bottom:1rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <h3 style="font-family:'Orbitron',sans-serif;font-size:1.1rem;color:var(--text-secondary);margin-bottom:.75rem;">No Offers Yet</h3>
        <p>Browse the job board and submit offers to customers near you.</p>
        <a href="{{ route('provider.jobs.index') }}" style="display:inline-flex;align-items:center;gap:.5rem;margin-top:1.5rem;padding:.75rem 1.5rem;background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green));border:none;border-radius:10px;color:#000;font-family:'Orbitron',sans-serif;font-weight:700;font-size:.8rem;text-decoration:none;">
            Browse Job Board →
        </a>
    </div>
    @else
    <div class="offers-list">
        @foreach($offers as $offer)
        @php $job = $offer->jobPost; @endphp
        <div class="offer-card offer-{{ $offer->status }}">
            <div class="offer-layout">

                {{-- Left: job info --}}
                <div>
                    <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;margin-bottom:.25rem;">
                        <div class="offer-job-type">{{ $job?->service_type ?? 'Deleted Job' }}</div>
                        <span class="status-badge status-{{ $offer->status }}">{{ ucfirst($offer->status) }}</span>
                        {{-- Negotiation badge alongside status --}}
                        @if($offer->negotiation_status && $offer->negotiation_status !== 'pending')
                        <span style="font-size:.68rem;font-weight:700;padding:.2rem .6rem;border-radius:8px;
                            @if($offer->negotiation_status === 'countered') background:rgba(255,170,0,.12);color:var(--accent-warning);border:1px solid rgba(255,170,0,.25);
                            @elseif($offer->negotiation_status === 'counter_accepted') background:rgba(0,255,170,.1);color:var(--accent-green);border:1px solid rgba(0,255,170,.2);
                            @else background:rgba(255,255,255,.05);color:var(--text-tertiary);border:1px solid rgba(255,255,255,.1); @endif
                        ">{{ $offer->negotiationLabel() }}</span>
                        @endif
                    </div>

                    @if($job)
                    <div class="offer-job-num">Job #{{ $job->job_number }} · {{ $job->vehicle->year ?? '' }} {{ $job->vehicle->make ?? '' }} {{ $job->vehicle->model ?? '' }}</div>
                    <div class="meta-strip">
                        <div class="meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ \Carbon\Carbon::parse($offer->available_date)->format('M d, Y') }} at {{ $offer->available_time }}
                        </div>
                        @if($offer->estimated_duration)
                        <div class="meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            ~{{ $offer->estimated_duration }} min
                        </div>
                        @endif
                        <div class="meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Submitted {{ $offer->created_at->diffForHumans() }}
                        </div>
                    </div>

                    {{-- ── Counter-offer section ── --}}

                    @if($offer->negotiation_status === 'countered')
                    {{-- Customer sent a counter — provider must respond --}}
                    <div class="counter-incoming">
                        <div class="counter-incoming-title">💬 Counter-Offer from Customer</div>
                        <div class="counter-price-row">
                            <div>
                                <div class="counter-price-info">
                                    Your price: <strong style="color:var(--text-primary);">${{ number_format($offer->offered_price,2) }}</strong>
                                    &nbsp;→&nbsp;
                                    Customer counter: <span class="counter-price-val">${{ number_format($offer->counter_price,2) }}</span>
                                </div>
                                @if($offer->counter_message)
                                <div style="font-size:.8rem;color:var(--text-secondary);margin-top:.35rem;font-style:italic;">"{{ $offer->counter_message }}"</div>
                                @endif
                                <div class="counter-meta">Sent {{ $offer->countered_at?->diffForHumans() }}</div>
                            </div>
                            <div class="counter-actions">
                                <form method="POST" action="{{ route('provider.counter.accept', $offer) }}">
                                    @csrf
                                    <button type="submit" class="btn-accept-counter"
                                        onclick="return confirm('Accept ${{ number_format($offer->counter_price,2) }}? This updates your offer price.')">
                                        ✓ Accept ${{ number_format($offer->counter_price,2) }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('provider.counter.reject', $offer) }}">
                                    @csrf
                                    <button type="submit" class="btn-reject-counter"
                                        onclick="return confirm('Decline this counter? Your original ${{ number_format($offer->offered_price,2) }} will stand.')">
                                        ✗ Decline
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    @elseif($offer->negotiation_status === 'counter_accepted')
                    {{-- Provider accepted the counter --}}
                    <div class="counter-accepted-badge">
                        <span style="color:var(--accent-green);font-weight:700;">✓ Counter Accepted</span>
                        <span style="color:var(--text-secondary);margin-left:.5rem;">
                            Agreed price: <strong style="font-family:'Orbitron',sans-serif;">${{ number_format($offer->counter_price,2) }}</strong>
                            <span style="font-size:.78rem;color:var(--text-tertiary);margin-left:.35rem;">(was ${{ number_format($offer->offered_price,2) }})</span>
                        </span>
                        <div style="font-size:.78rem;color:var(--text-tertiary);margin-top:.25rem;">
                            Waiting for customer to confirm the booking at this price.
                        </div>
                    </div>

                    @elseif($offer->negotiation_status === 'counter_rejected')
                    {{-- Provider rejected the counter --}}
                    <div class="counter-rejected-note">
                        Counter declined — customer can accept your ${{ number_format($offer->offered_price,2) }} or re-negotiate.
                    </div>
                    @endif

                    {{-- Accepted offer success banner --}}
                    @if($offer->status === 'accepted')
                    <div style="background:rgba(0,255,170,.08);border:1px solid rgba(0,255,170,.2);border-radius:10px;padding:.875rem 1rem;font-size:.825rem;color:var(--accent-green);margin-top:.875rem;">
                        🎉 Customer accepted your offer! Check your work queue to get started.
                    </div>
                    @endif

                    <a href="{{ route('provider.jobs.show', $job) }}" class="btn-view">View Job →</a>
                    @endif
                </div>

                {{-- Right: price --}}
                <div style="text-align:right;">
                    {{-- Show counter price if accepted, original otherwise --}}
                    @if($offer->negotiation_status === 'counter_accepted' && $offer->counter_price)
                    <div class="offer-price" style="color:var(--accent-green);">${{ number_format($offer->counter_price,2) }}</div>
                    <div style="font-size:.7rem;color:var(--text-tertiary);margin-top:.15rem;">agreed price</div>
                    <div style="font-size:.72rem;color:var(--text-tertiary);text-decoration:line-through;margin-top:.1rem;">${{ number_format($offer->offered_price,2) }}</div>
                    @else
                    <div class="offer-price">${{ number_format($offer->offered_price,2) }}</div>
                    <div style="font-size:.75rem;color:var(--text-tertiary);margin-top:.25rem;">your offer</div>
                    @endif
                </div>

            </div>
        </div>
        @endforeach
    </div>
    <div style="margin-top:2rem;">{{ $offers->links() }}</div>
    @endif
</div>
@endsection