@extends('provider.layouts.app')
@section('title', 'Job Board')
@section('content')
<style>
.pg-container { padding:2rem; }
.page-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:2rem; flex-wrap:wrap; gap:1rem; }
.page-title { font-family:'Orbitron',sans-serif; font-size:1.5rem; font-weight:800; }
.page-title span { color:var(--accent-cyan); }
.page-sub { color:var(--text-tertiary); font-size:.85rem; margin-top:.35rem; }
.tab-row { display:flex; gap:.5rem; margin-bottom:1.5rem; flex-wrap:wrap; }
.tab-btn { padding:.5rem 1.25rem; border-radius:20px; border:1px solid var(--border-color); background:transparent; color:var(--text-secondary); font-family:'Chakra Petch',sans-serif; font-size:.8rem; font-weight:600; cursor:pointer; text-decoration:none; transition:all .3s; }
.tab-btn.active, .tab-btn:hover { border-color:var(--accent-cyan); color:var(--accent-cyan); background:rgba(0,212,255,.08); }
.filter-bar { background:var(--card-bg); border:1px solid var(--border-color); border-radius:14px; padding:1rem 1.5rem; margin-bottom:1.5rem; display:flex; gap:1rem; flex-wrap:wrap; align-items:center; }
.filter-select, .filter-input { padding:.5rem .875rem; background:rgba(0,212,255,.05); border:1px solid var(--border-color); border-radius:8px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.85rem; }
.filter-btn { padding:.5rem 1.1rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:8px; color:#000; font-weight:700; font-size:.8rem; cursor:pointer; }
.jobs-list { display:flex; flex-direction:column; gap:1rem; }
.job-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.5rem; position:relative; overflow:hidden; transition:all .3s; }
.job-card:hover { border-color:rgba(0,212,255,.3); }
.job-card.already-offered { border-color:rgba(0,255,170,.2); background:rgba(0,255,170,.03); }
.job-card::before { content:''; position:absolute; top:0; left:0; bottom:0; width:3px; }
.job-card.urgency-high::before   { background:var(--accent-danger); }
.job-card.urgency-medium::before { background:var(--accent-warning); }
.job-card.urgency-low::before    { background:var(--accent-cyan); }
.job-layout { display:grid; grid-template-columns:1fr auto; gap:1.5rem; align-items:start; }
.job-type { font-family:'Orbitron',sans-serif; font-size:1.1rem; font-weight:700; margin-bottom:.5rem; }
.job-budget { font-family:'Orbitron',sans-serif; font-size:1.5rem; font-weight:800; color:var(--accent-warning); white-space:nowrap; }
.job-budget .note { font-family:'Chakra Petch',sans-serif; font-size:.7rem; font-weight:400; color:var(--text-tertiary); }
.meta-strip { display:flex; gap:1.5rem; flex-wrap:wrap; margin-top:.875rem; font-size:.825rem; color:var(--text-secondary); }
.meta-item { display:flex; align-items:center; gap:.4rem; }
.meta-item svg { width:14px; height:14px; color:var(--accent-cyan); }
.job-desc { font-size:.825rem; color:var(--text-secondary); line-height:1.6; margin-top:.875rem; }
.action-row { display:flex; gap:.75rem; align-items:center; margin-top:1.25rem; flex-wrap:wrap; }
.btn-offer { display:inline-flex; align-items:center; gap:.5rem; padding:.625rem 1.25rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:8px; color:#000; font-family:'Orbitron',sans-serif; font-weight:700; font-size:.75rem; cursor:pointer; text-decoration:none; transition:all .3s; }
.btn-offer:hover { transform:translateY(-1px); box-shadow:0 4px 15px rgba(0,212,255,.4); }
.btn-view { display:inline-flex; align-items:center; gap:.5rem; padding:.625rem 1.1rem; background:rgba(0,212,255,.08); border:1px solid rgba(0,212,255,.25); border-radius:8px; color:var(--accent-cyan); font-size:.8rem; font-weight:600; text-decoration:none; transition:all .3s; }
.badge-offered { display:inline-flex; align-items:center; gap:.4rem; padding:.375rem .875rem; background:rgba(0,255,170,.1); border:1px solid rgba(0,255,170,.3); border-radius:20px; font-size:.75rem; color:var(--accent-green); font-weight:600; }
.distance-pill { display:inline-flex; align-items:center; gap:.35rem; padding:.3rem .7rem; background:rgba(0,212,255,.08); border:1px solid rgba(0,212,255,.2); border-radius:20px; font-size:.75rem; color:var(--accent-cyan); }
.vehicle-badge { display:inline-flex; align-items:center; gap:.35rem; padding:.3rem .7rem; background:rgba(255,170,0,.08); border:1px solid rgba(255,170,0,.2); border-radius:20px; font-size:.75rem; color:var(--accent-warning); }
.urgency-badge { display:inline-flex; align-items:center; gap:.35rem; padding:.3rem .7rem; border-radius:20px; font-size:.7rem; font-weight:700; }
.urgency-high   { background:rgba(255,51,102,.12); color:#ff8099; border:1px solid rgba(255,51,102,.3); }
.urgency-medium { background:rgba(255,170,0,.12); color:var(--accent-warning); border:1px solid rgba(255,170,0,.3); }
.empty-state { text-align:center; padding:5rem 2rem; color:var(--text-tertiary); }
.offer-count-badge { padding:.25rem .625rem; background:rgba(255,170,0,.1); border:1px solid rgba(255,170,0,.2); border-radius:8px; font-size:.72rem; color:var(--accent-warning); }
</style>

<div class="pg-container">
    <div class="page-header">
        <div>
            <div class="page-title">Job <span>Board</span></div>
            <div class="page-sub">Browse open service requests from nearby customers and submit your offers</div>
        </div>
    </div>

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

    <div class="tab-row">
        <a href="{{ route('provider.jobs.index') }}" class="tab-btn {{ !request('my') ? 'active' : '' }}">🗺 Open Jobs Nearby</a>
        <a href="{{ route('provider.jobs.my-offers') }}" class="tab-btn">📋 My Submitted Offers</a>
    </div>

    <div class="filter-bar">
        <form method="GET" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:center;width:100%;">
            <select name="service_type" class="filter-select" onchange="this.form.submit()">
                <option value="">All Service Types</option>
                @foreach($serviceTypes as $t)
                <option value="{{ $t }}" {{ request('service_type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
            <select name="radius" class="filter-select" onchange="this.form.submit()">
                @foreach([10,25,50,100] as $r)
                <option value="{{ $r }}" {{ request('radius', 50) == $r ? 'selected' : '' }}>Within {{ $r }} miles</option>
                @endforeach
            </select>
        </form>
    </div>

    @if($jobs->isEmpty())
    <div class="empty-state">
        <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-bottom:1rem;opacity:.25;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        <h3 style="font-family:'Orbitron',sans-serif;font-size:1.1rem;color:var(--text-secondary);margin-bottom:.75rem;">No Open Jobs Near You</h3>
        <p>When customers post jobs in your area, they'll appear here.<br>Make sure your location is set in your profile.</p>
    </div>
    @else
    <div class="jobs-list">
        @foreach($jobs as $job)
        @php
            $hoursLeft = $job->expires_at ? now()->diffInHours($job->expires_at, false) : 999;
            $urgency   = $hoursLeft < 3 ? 'high' : ($hoursLeft < 8 ? 'medium' : 'low');
            $alreadyOffered = in_array($job->id, $myOfferJobIds);
        @endphp
        <div class="job-card urgency-{{ $urgency }} {{ $alreadyOffered ? 'already-offered' : '' }}">
            <div class="job-layout">
                <div>
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
                        <div class="job-type">{{ $job->service_type }}</div>
                        <div style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;">
                            @if($urgency === 'high')
                            <span class="urgency-badge urgency-high">🔥 Expires soon</span>
                            @elseif($urgency === 'medium')
                            <span class="urgency-badge urgency-medium">⏳ {{ $hoursLeft }}h left</span>
                            @endif
                            @if($alreadyOffered)
                            <span class="badge-offered">✓ Offer Submitted</span>
                            @endif
                            @if($job->offers->count() > 0)
                            <span class="offer-count-badge">{{ $job->offers->count() }} offer{{ $job->offers->count() !== 1 ? 's' : '' }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="meta-strip">
                        @if(isset($job->distance))
                        <span class="distance-pill">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            {{ round($job->distance, 1) }} miles away
                        </span>
                        @endif
                        <span class="vehicle-badge">
                            {{ $job->vehicle->year }} {{ $job->vehicle->make }} {{ $job->vehicle->model }}
                        </span>
                        @if($job->preferred_date)
                        <span class="meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ \Carbon\Carbon::parse($job->preferred_date)->format('M d, Y') }}
                            {{ $job->preferred_time ? '· '.$job->preferred_time : '' }}
                        </span>
                        @endif
                    </div>
                    <p class="job-desc">{{ \Str::limit($job->description, 160) }}</p>
                    <div class="action-row">
                        @if(!$alreadyOffered)
                        <a href="{{ route('provider.jobs.show', $job) }}" class="btn-offer">
                            💰 Submit Offer
                        </a>
                        @endif
                        <a href="{{ route('provider.jobs.show', $job) }}" class="btn-view">
                            View Details →
                        </a>
                    </div>
                </div>
                <div style="text-align:right;min-width:100px;">
                    <div class="job-budget">{{ $job->budgetLabel() }}<div class="note">customer budget</div></div>
                    <div style="font-size:.75rem;color:var(--text-tertiary);margin-top:.5rem;">Posted {{ $job->created_at->diffForHumans() }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div style="margin-top:2rem;">{{ $jobs->links() }}</div>
    @endif
</div>
@endsection