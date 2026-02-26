@extends('layouts.app')
@section('title', $job->service_type . ' — Job #' . $job->job_number)
@section('content')
<style>
:root[data-theme="dark"] {
    --card-bg:rgba(26,32,48,.85); --border-color:rgba(0,212,255,.1);
    --input-bg:rgba(0,212,255,.05); --text-primary:#fff;
    --text-secondary:rgba(255,255,255,.7); --text-tertiary:rgba(255,255,255,.45);
    --accent-cyan:#00d4ff; --accent-green:#00ffaa; --accent-warning:#ffaa00; --accent-danger:#ff3366;
}
:root[data-theme="light"] {
    --card-bg:rgba(255,255,255,.9); --border-color:rgba(0,0,0,.1);
    --input-bg:rgba(0,0,0,.03); --text-primary:#1a1f36;
    --text-secondary:rgba(26,31,54,.7); --text-tertiary:rgba(26,31,54,.45);
    --accent-cyan:#0066ff; --accent-green:#00cc88; --accent-warning:#ff9500; --accent-danger:#ff3366;
}
.pg-container { max-width:1100px; margin:0 auto; padding:2rem 1.5rem; }
.back-link { display:inline-flex; align-items:center; gap:.5rem; color:var(--accent-cyan); text-decoration:none; font-size:.875rem; font-weight:600; margin-bottom:1.5rem; }
.back-link:hover { gap:.875rem; }
.hero-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:20px; padding:2rem; margin-bottom:1.5rem; position:relative; overflow:hidden; }
.hero-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,var(--accent-cyan),var(--accent-green)); }
.hero-top { display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; flex-wrap:wrap; }
.job-title { font-family:'Orbitron',sans-serif; font-size:1.75rem; font-weight:800; margin-bottom:.5rem; }
.status-badge { padding:.375rem .875rem; border-radius:20px; font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; }
.status-open      { background:rgba(255,170,0,.15); color:var(--accent-warning); border:1px solid rgba(255,170,0,.3); }
.status-accepted  { background:rgba(0,255,170,.15); color:var(--accent-green);   border:1px solid rgba(0,255,170,.3); }
.status-completed { background:rgba(0,212,255,.15); color:var(--accent-cyan);    border:1px solid rgba(0,212,255,.3); }
.status-cancelled,.status-expired { background:rgba(255,255,255,.07); color:var(--text-tertiary); border:1px solid rgba(255,255,255,.1); }
.job-meta-strip { display:flex; gap:2rem; flex-wrap:wrap; margin-top:1.25rem; padding-top:1.25rem; border-top:1px solid var(--border-color); }
.meta-item { display:flex; align-items:center; gap:.625rem; font-size:.875rem; color:var(--text-secondary); }
.meta-item svg { width:16px; height:16px; color:var(--accent-cyan); }
.meta-item strong { color:var(--text-primary); }
.main-layout { display:grid; grid-template-columns:1fr 320px; gap:1.5rem; }
.card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.75rem; margin-bottom:1.5rem; }
.card-title { font-family:'Orbitron',sans-serif; font-size:.95rem; font-weight:700; margin-bottom:1.25rem; padding-bottom:.75rem; border-bottom:1px solid var(--border-color); }
/* Offer cards */
.offer-card { border:1px solid var(--border-color); border-radius:14px; padding:1.5rem; margin-bottom:1rem; transition:all .3s; position:relative; overflow:hidden; }
.offer-card:hover { border-color:rgba(0,212,255,.3); }
.offer-card.winner { border-color:rgba(0,255,170,.4); background:rgba(0,255,170,.04); }
.offer-card.winner::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--accent-green); }
.offer-card.rejected { opacity:.45; }
.offer-top { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1rem; }
.provider-name { font-weight:700; font-size:1rem; }
.provider-meta { display:flex; gap:.75rem; flex-wrap:wrap; margin-top:.25rem; }
.provider-badge { display:inline-flex; align-items:center; gap:.35rem; font-size:.72rem; padding:.2rem .6rem; border-radius:10px; }
.badge-verified { background:rgba(0,255,170,.1); color:var(--accent-green); border:1px solid rgba(0,255,170,.2); }
.badge-type     { background:rgba(0,212,255,.1); color:var(--accent-cyan); border:1px solid rgba(0,212,255,.2); }
.offer-price { font-family:'Orbitron',sans-serif; font-size:1.75rem; font-weight:900; color:var(--accent-warning); }
.offer-price span { font-size:.85rem; color:var(--text-tertiary); font-family:'Chakra Petch',sans-serif; }
.offer-details { display:grid; grid-template-columns:1fr 1fr; gap:.75rem; margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border-color); font-size:.825rem; color:var(--text-secondary); }
.offer-detail-item { display:flex; align-items:center; gap:.5rem; }
.offer-detail-item svg { width:14px; height:14px; color:var(--accent-cyan); }
.offer-message { font-size:.825rem; color:var(--text-secondary); margin-top:.875rem; padding:.875rem; background:rgba(0,212,255,.04); border-left:2px solid rgba(0,212,255,.3); border-radius:0 8px 8px 0; line-height:1.6; }
.offer-distance { font-size:.8rem; color:var(--text-tertiary); display:flex; align-items:center; gap:.35rem; margin-top:.5rem; }
.star-row { display:flex; gap:3px; }
.star-row svg { width:13px; height:13px; }
.btn-accept { display:inline-flex; align-items:center; gap:.5rem; padding:.6rem 1.25rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:8px; color:#000; font-family:'Orbitron',sans-serif; font-weight:700; font-size:.75rem; cursor:pointer; transition:all .3s; text-decoration:none; }
.btn-accept:hover { transform:translateY(-1px); box-shadow:0 4px 15px rgba(0,212,255,.4); }
.btn-danger { display:inline-flex; align-items:center; gap:.5rem; padding:.6rem 1.25rem; background:rgba(255,51,102,.1); border:1px solid rgba(255,51,102,.3); border-radius:8px; color:#ff8099; font-size:.8rem; font-weight:600; cursor:pointer; text-decoration:none; transition:all .3s; }
.btn-danger:hover { background:rgba(255,51,102,.2); }
.btn-success { display:inline-flex; align-items:center; gap:.5rem; padding:.6rem 1.25rem; background:rgba(0,255,170,.1); border:1px solid rgba(0,255,170,.3); border-radius:8px; color:var(--accent-green); font-size:.8rem; font-weight:600; cursor:pointer; text-decoration:none; transition:all .3s; }
.accepted-banner { background:rgba(0,255,170,.08); border:1px solid rgba(0,255,170,.25); border-radius:14px; padding:1.5rem; text-align:center; margin-bottom:1.5rem; }
.accepted-banner .icon { font-size:2.5rem; margin-bottom:.5rem; }
.accepted-banner h3 { font-family:'Orbitron',sans-serif; font-size:1.1rem; color:var(--accent-green); margin-bottom:.5rem; }
.waiting-hint { text-align:center; padding:2rem; color:var(--text-tertiary); font-size:.875rem; }
.waiting-hint .spinner { width:40px; height:40px; border:2px solid rgba(0,212,255,.15); border-top-color:var(--accent-cyan); border-radius:50%; animation:spin 1s linear infinite; margin:0 auto 1rem; }
@keyframes spin { to { transform:rotate(360deg); } }
.sidebar-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.5rem; margin-bottom:1rem; }
.sidebar-card h3 { font-family:'Orbitron',sans-serif; font-size:.85rem; font-weight:700; margin-bottom:1rem; padding-bottom:.75rem; border-bottom:1px solid var(--border-color); }
.desc-text { font-size:.875rem; color:var(--text-secondary); line-height:1.7; }
.kv-row { display:flex; justify-content:space-between; font-size:.825rem; margin-bottom:.75rem; }
.kv-row .k { color:var(--text-tertiary); }
.kv-row .v { color:var(--text-primary); font-weight:600; text-align:right; }
.expire-timer { text-align:center; padding:.875rem; background:rgba(255,170,0,.06); border:1px solid rgba(255,170,0,.2); border-radius:10px; font-size:.85rem; color:var(--accent-warning); margin-top:.75rem; }
@media(max-width:900px) { .main-layout { grid-template-columns:1fr; } }
</style>

<div class="pg-container">
    <a href="{{ route('jobs.index') }}" class="back-link">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to My Jobs
    </a>

    @if(session('success'))
    <div style="background:rgba(0,255,170,.1);border:1px solid rgba(0,255,170,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-green);font-size:.875rem;">
        ✓ {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:rgba(255,51,102,.1);border:1px solid rgba(255,51,102,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:#ff8099;font-size:.875rem;">
        {{ session('error') }}
    </div>
    @endif

    {{-- Hero --}}
    <div class="hero-card">
        <div class="hero-top">
            <div>
                <div class="job-title">{{ $job->service_type }}</div>
                <div style="font-size:.8rem;color:var(--text-tertiary)">Job #{{ $job->job_number }}</div>
            </div>
            <span class="status-badge status-{{ $job->status }}">{{ ucfirst($job->status) }}</span>
        </div>
        <div class="job-meta-strip">
            <div class="meta-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 00-1-1h-2a1 1 0 00-1 1v5m4 0H9"/></svg>
                <span><strong>{{ $job->vehicle->full_name ?? 'N/A' }}</strong></span>
            </div>
            <div class="meta-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Budget: <strong>{{ $job->budgetLabel() }}</strong></span>
            </div>
            @if($job->preferred_date)
            <div class="meta-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Preferred: <strong>{{ \Carbon\Carbon::parse($job->preferred_date)->format('M d, Y') }}{{ $job->preferred_time ? ' · '.$job->preferred_time : '' }}</strong></span>
            </div>
            @endif
            <div class="meta-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>{{ $job->location_address ? \Str::limit($job->location_address, 50) : 'Location set' }}</span>
            </div>
            <div class="meta-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Posted {{ $job->created_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>

    <div class="main-layout">
        <div>
            {{-- Accepted offer banner --}}
            @if($acceptedOffer)
            <div class="accepted-banner">
                <div class="icon">🎉</div>
                <h3>Offer Accepted!</h3>
                <p style="color:var(--text-secondary);font-size:.875rem;">You accepted <strong>{{ $acceptedOffer->serviceProvider->name }}</strong>'s offer of <strong>${{ number_format($acceptedOffer->offered_price, 2) }}</strong>.<br>They will contact you to confirm the appointment.</p>
                @if($job->status === 'accepted')
                <form method="POST" action="{{ route('jobs.complete', $job) }}" style="margin-top:1rem;">
                    @csrf
                    <button type="submit" class="btn-success">✓ Mark as Completed</button>
                </form>
                @endif
            </div>
            @endif

            {{-- Offers section --}}
            <div class="card">
                <div class="card-title">
                    Offers Received
                    <span style="font-family:'Chakra Petch',sans-serif;font-size:.8rem;font-weight:400;color:var(--text-tertiary);margin-left:.5rem;">({{ $pendingOffers->count() + ($acceptedOffer ? 1 : 0) + $rejectedOffers->count() }} total)</span>
                </div>

                @if($job->isOpen() && $pendingOffers->isEmpty() && !$acceptedOffer)
                <div class="waiting-hint">
                    <div class="spinner"></div>
                    <p>Waiting for providers to send offers...<br>This usually takes a few minutes.</p>
                </div>

                @elseif($pendingOffers->isNotEmpty() && $job->isOpen())
                @foreach($pendingOffers as $offer)
                @php $prov = $offer->serviceProvider; @endphp
                <div class="offer-card">
                    <div class="offer-top">
                        <div>
                            <div class="provider-name">{{ $prov->name }}</div>
                            <div class="provider-meta">
                                @if($prov->is_verified)
                                <span class="provider-badge badge-verified">
                                    <svg width="10" height="10" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                                    Verified
                                </span>
                                @endif
                                <span class="provider-badge badge-type">{{ ucfirst(str_replace('_',' ',$prov->type)) }}</span>
                                @if($prov->rating > 0)
                                <span style="display:inline-flex;align-items:center;gap:.35rem;font-size:.72rem;color:var(--accent-warning);">
                                    ★ {{ number_format($prov->rating,1) }} ({{ $prov->total_reviews }})
                                </span>
                                @endif
                            </div>
                            @if(isset($offer->distance))
                            <div class="offer-distance">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                {{ $offer->distance }} miles away
                            </div>
                            @endif
                        </div>
                        <div style="text-align:right;">
                            <div class="offer-price">${{ number_format($offer->offered_price, 2) }}</div>
                        </div>
                    </div>

                    <div class="offer-details">
                        <div class="offer-detail-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ \Carbon\Carbon::parse($offer->available_date)->format('M d, Y') }}
                        </div>
                        <div class="offer-detail-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $offer->available_time }}
                        </div>
                        @if($offer->estimated_duration)
                        <div class="offer-detail-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            ~{{ $offer->estimated_duration }} min
                        </div>
                        @endif
                        <div class="offer-detail-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            {{ $prov->city }}, {{ $prov->state }}
                        </div>
                    </div>

                    @if($offer->message)
                    <div class="offer-message">"{{ $offer->message }}"</div>
                    @endif

                    <div style="margin-top:1.25rem;display:flex;gap:.75rem;align-items:center;">
                        <form method="POST" action="{{ route('jobs.accept-offer', [$job, $offer]) }}">
                            @csrf
                            <button type="submit" class="btn-accept"
                                onclick="return confirm('Accept this offer from {{ $prov->name }} for ${{ number_format($offer->offered_price,2) }}?')">
                                ✓ Accept This Offer
                            </button>
                        </form>
                        <a href="{{ route('providers.show', $prov) }}" class="btn-danger" style="background:transparent;color:var(--accent-cyan);border-color:rgba(0,212,255,.3);">
                            View Profile →
                        </a>
                    </div>
                </div>
                @endforeach

                @elseif($acceptedOffer && $pendingOffers->isEmpty())
                {{-- Just show the accepted offer details above, nothing else pending --}}
                @endif

                {{-- Rejected offers (collapsed) --}}
                @if($rejectedOffers->isNotEmpty())
                <details style="margin-top:1rem;">
                    <summary style="cursor:pointer;font-size:.825rem;color:var(--text-tertiary);list-style:none;display:flex;align-items:center;gap:.5rem;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        {{ $rejectedOffers->count() }} other {{ $rejectedOffers->count() === 1 ? 'offer' : 'offers' }} (not selected)
                    </summary>
                    <div style="margin-top:1rem;">
                        @foreach($rejectedOffers as $offer)
                        <div class="offer-card rejected">
                            <div class="offer-top">
                                <div class="provider-name">{{ $offer->serviceProvider->name }}</div>
                                <div class="offer-price" style="font-size:1.25rem;">${{ number_format($offer->offered_price, 2) }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </details>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div>
            <div class="sidebar-card">
                <h3>Job Details</h3>
                <div class="kv-row"><span class="k">Job #</span><span class="v">{{ $job->job_number }}</span></div>
                <div class="kv-row"><span class="k">Service</span><span class="v">{{ $job->service_type }}</span></div>
                <div class="kv-row"><span class="k">Vehicle</span><span class="v">{{ $job->vehicle->full_name ?? 'N/A' }}</span></div>
                <div class="kv-row"><span class="k">Budget</span><span class="v">{{ $job->budgetLabel() }}</span></div>
                @if($job->preferred_date)
                <div class="kv-row"><span class="k">Date</span><span class="v">{{ \Carbon\Carbon::parse($job->preferred_date)->format('M d, Y') }}</span></div>
                @endif
                @if($job->preferred_time)
                <div class="kv-row"><span class="k">Time</span><span class="v">{{ $job->preferred_time }}</span></div>
                @endif
                <div class="kv-row"><span class="k">Radius</span><span class="v">{{ $job->radius }} miles</span></div>
                <div class="kv-row"><span class="k">Offers</span><span class="v" style="color:var(--accent-warning);">{{ $job->offers->count() }}</span></div>
            </div>

            <div class="sidebar-card">
                <h3>Description</h3>
                <p class="desc-text">{{ $job->description }}</p>
                @if($job->customer_notes)
                <p class="desc-text" style="margin-top:.875rem;padding-top:.875rem;border-top:1px solid var(--border-color);"><em>{{ $job->customer_notes }}</em></p>
                @endif
            </div>

            @if($job->isOpen())
            <div class="sidebar-card">
                <h3>Actions</h3>
                @if($job->expires_at)
                <div class="expire-timer">
                    ⏳ Expires {{ $job->expires_at->diffForHumans() }}
                </div>
                @endif
                <form method="POST" action="{{ route('jobs.cancel', $job) }}" style="margin-top:1rem;">
                    @csrf
                    <button type="submit" class="btn-danger" style="width:100%;justify-content:center;"
                        onclick="return confirm('Are you sure you want to cancel this job post?')">
                        ✕ Cancel Job Post
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection