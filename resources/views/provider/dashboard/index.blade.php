@extends('provider.layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
.dash-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1.25rem; margin-bottom:2rem; }
.stat-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.5rem; transition:all .3s; position:relative; overflow:hidden; }
.stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg, var(--accent-color, #00d4ff), transparent); }
.stat-card:hover { transform:translateY(-3px); border-color:var(--accent-color,#00d4ff); box-shadow:0 8px 30px rgba(0,212,255,.15); }
.stat-icon { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; margin-bottom:1rem; }
.stat-icon svg { width:22px; height:22px; }
.stat-value { font-family:'Orbitron',sans-serif; font-size:1.875rem; font-weight:800; margin-bottom:.25rem; }
.stat-label { font-size:.8rem; color:var(--text-tertiary); text-transform:uppercase; letter-spacing:.06em; }
.section-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; }
.section-title { font-family:'Orbitron',sans-serif; font-size:1rem; font-weight:700; }
.view-all { font-size:.8rem; color:var(--accent-cyan); text-decoration:none; }
.view-all:hover { text-decoration:underline; }
.card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.5rem; }
.two-col { display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.5rem; }
.job-row { display:flex; align-items:center; gap:1rem; padding:.875rem 0; border-bottom:1px solid var(--border-color); }
.job-row:last-child { border-bottom:none; }
.job-icon { width:38px; height:38px; border-radius:10px; flex-shrink:0; background:rgba(0,212,255,.1); display:flex; align-items:center; justify-content:center; color:var(--accent-cyan); }
.job-icon svg { width:18px; height:18px; }
.job-title { font-size:.875rem; font-weight:600; }
.job-sub { font-size:.75rem; color:var(--text-tertiary); margin-top:2px; }
.status-pill { margin-left:auto; padding:.25rem .75rem; border-radius:20px; font-size:.7rem; font-weight:700; text-transform:uppercase; white-space:nowrap; text-decoration:none; }
.pill-pending     { background:rgba(255,170,0,.15); color:#ffaa00; border:1px solid rgba(255,170,0,.3); }
.pill-confirmed   { background:rgba(0,212,255,.15); color:#00d4ff; border:1px solid rgba(0,212,255,.3); }
.pill-in_progress { background:rgba(168,85,247,.15); color:#a855f7; border:1px solid rgba(168,85,247,.3); }
.pill-completed   { background:rgba(0,255,170,.15); color:#00ffaa; border:1px solid rgba(0,255,170,.3); }
.pill-cancelled   { background:rgba(255,51,102,.15); color:#ff3366; border:1px solid rgba(255,51,102,.3); }
.review-row { display:flex; gap:1rem; padding:.875rem 0; border-bottom:1px solid var(--border-color); }
.review-row:last-child { border-bottom:none; }
.stars { display:flex; gap:2px; }
.stars svg { width:14px; height:14px; }
.review-text { font-size:.8rem; color:var(--text-secondary); margin-top:.25rem; line-height:1.5; }
.revenue-chart { display:flex; align-items:flex-end; gap:.5rem; height:120px; padding-top:.5rem; }
.rev-col { flex:1; display:flex; flex-direction:column; align-items:center; gap:.25rem; height:100%; }
.rev-track { flex:1; display:flex; align-items:flex-end; width:100%; }
.rev-bar { width:100%; border-radius:4px 4px 0 0; background:linear-gradient(180deg,var(--accent-cyan),rgba(0,212,255,.3)); min-height:4px; }
.rev-label { font-size:.65rem; color:var(--text-tertiary); }
@media(max-width:900px) { .two-col { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')

<div class="dash-grid">
    <div class="stat-card" style="--accent-color:#ffaa00;">
        <div class="stat-icon" style="background:rgba(255,170,0,.15);color:#ffaa00;"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <div class="stat-value" style="color:#ffaa00;">{{ $stats['pending'] + $stats['confirmed'] }}</div>
        <div class="stat-label">Active Jobs</div>
    </div>
    <div class="stat-card" style="--accent-color:#a855f7;">
        <div class="stat-icon" style="background:rgba(168,85,247,.15);color:#a855f7;"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
        <div class="stat-value" style="color:#a855f7;">{{ $stats['in_progress'] }}</div>
        <div class="stat-label">In Progress</div>
    </div>
    <div class="stat-card" style="--accent-color:#00ffaa;">
        <div class="stat-icon" style="background:rgba(0,255,170,.15);color:#00ffaa;"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <div class="stat-value" style="color:#00ffaa;">{{ $stats['completed'] }}</div>
        <div class="stat-label">Completed</div>
    </div>
    <div class="stat-card" style="--accent-color:#a855f7;">
        <div class="stat-icon" style="background:rgba(168,85,247,.15);color:#a855f7;"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <div class="stat-value" style="color:#a855f7;">${{ number_format($stats['monthly_revenue'], 0) }}</div>
        <div class="stat-label">Monthly Revenue</div>
    </div>
    <div class="stat-card" style="--accent-color:#ffaa00;">
        <div class="stat-icon" style="background:rgba(255,170,0,.15);color:#ffaa00;"><svg fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg></div>
        <div class="stat-value" style="color:#ffaa00;">{{ number_format($stats['avg_rating'], 1) }}</div>
        <div class="stat-label">Rating ({{ $stats['total_reviews'] }})</div>
    </div>
    <div class="stat-card" style="--accent-color:#00d4ff;">
        <div class="stat-icon" style="background:rgba(0,212,255,.15);color:#00d4ff;"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg></div>
        <div class="stat-value" style="color:#00d4ff;">{{ $stats['open_offers'] }}</div>
        <div class="stat-label">Open Offers</div>
    </div>
</div>

<div class="two-col">
    <div class="card">
        <div class="section-header">
            <div class="section-title">Revenue (6 Months)</div>
            <a href="{{ route('provider.analytics') }}" class="view-all">Full Analytics →</a>
        </div>
        @php $maxRev = max(array_column($revenueChart, 'revenue') ?: [1]); @endphp
        <div class="revenue-chart">
            @foreach($revenueChart as $item)
            <div class="rev-col">
                <div class="rev-track"><div class="rev-bar" style="height:{{ $maxRev > 0 ? max(4, ($item['revenue']/$maxRev)*100) : 4 }}%;" title="${{ number_format($item['revenue']) }}"></div></div>
                <div class="rev-label">{{ $item['month'] }}</div>
            </div>
            @endforeach
        </div>
        <div style="display:flex;justify-content:space-between;margin-top:1rem;font-size:.8rem;color:var(--text-tertiary);">
            <span>Total: ${{ number_format($stats['total_revenue']) }}</span>
            <span>{{ $stats['total_jobs'] }} completed jobs</span>
        </div>
    </div>

    <div class="card">
        <div class="section-header">
            <div class="section-title">Active Work Queue</div>
            <a href="{{ route('provider.jobs.work.index') }}" class="view-all">View All →</a>
        </div>
        @forelse($activeJobs as $job)
        <div class="job-row">
            <div class="job-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
            <div style="flex:1;">
                <div class="job-title">{{ $job->service_type }}</div>
                <div class="job-sub">{{ $job->vehicle->full_name ?? 'N/A' }} · {{ $job->user->name ?? '—' }}</div>
            </div>
            <a href="{{ route('provider.jobs.work.show', $job) }}" class="status-pill pill-{{ $job->work_status }}">{{ str_replace('_',' ', $job->work_status) }}</a>
        </div>
        @empty
        <div style="text-align:center;padding:2rem;color:var(--text-tertiary);font-size:.875rem;">No active jobs right now</div>
        @endforelse
    </div>
</div>

<div class="two-col">
    <div class="card">
        <div class="section-header">
            <div class="section-title">Recent Jobs</div>
            <a href="{{ route('provider.jobs.work.index') }}" class="view-all">View All →</a>
        </div>
        @forelse($recentJobs as $job)
        <div class="job-row">
            <div class="job-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
            <div style="flex:1;">
                <div class="job-title">{{ $job->service_type }}</div>
                <div class="job-sub">{{ $job->vehicle->full_name ?? 'N/A' }} · {{ $job->created_at->format('M d') }}</div>
            </div>
            <span class="status-pill pill-{{ $job->work_status ?? 'pending' }}">{{ str_replace('_',' ', $job->work_status ?? 'pending') }}</span>
        </div>
        @empty
        <p style="color:var(--text-tertiary);font-size:.875rem;text-align:center;padding:2rem 0;">No jobs yet — start bidding on the Job Board</p>
        @endforelse
    </div>

    <div class="card">
        <div class="section-header">
            <div class="section-title">Recent Reviews</div>
            <span style="font-family:'Orbitron',sans-serif;font-size:.875rem;font-weight:700;color:#ffaa00;">{{ number_format($stats['avg_rating'],1) }} ★</span>
        </div>
        @forelse($recentReviews as $r)
        <div class="review-row">
            <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green));display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.875rem;color:#000;flex-shrink:0;">{{ substr($r->user->name ?? 'U', 0, 1) }}</div>
            <div style="flex:1;">
                <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.25rem;">
                    <div class="stars">@for($i=1;$i<=5;$i++)<svg fill="{{ $i<=$r->rating ? '#ffaa00' : 'rgba(255,255,255,.2)' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>@endfor</div>
                    <span style="font-size:.75rem;color:var(--text-tertiary);">{{ $r->updated_at->diffForHumans() }}</span>
                </div>
                <div class="review-text">{{ $r->review ? Str::limit($r->review, 100) : 'No written review' }}</div>
                <div style="font-size:.7rem;color:var(--text-tertiary);margin-top:.25rem;">{{ $r->service_type }}</div>
            </div>
        </div>
        @empty
        <p style="color:var(--text-tertiary);font-size:.875rem;text-align:center;padding:2rem 0;">No reviews yet</p>
        @endforelse
    </div>
</div>

@if(($stats['pending'] + $stats['confirmed']) > 0)
<div style="background:rgba(255,170,0,.08);border:1px solid rgba(255,170,0,.3);border-radius:16px;padding:1.25rem 1.5rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
    <div style="width:44px;height:44px;border-radius:12px;background:rgba(255,170,0,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;"><svg width="22" height="22" fill="none" stroke="#ffaa00" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg></div>
    <div style="flex:1;">
        <div style="font-weight:700;color:#ffaa00;margin-bottom:.25rem;">{{ $stats['pending'] + $stats['confirmed'] }} Job{{ ($stats['pending']+$stats['confirmed'])>1?'s':'' }} Waiting for Action</div>
        <div style="font-size:.8rem;color:var(--text-secondary);">Customers are waiting. Confirm and start work to build your reputation.</div>
    </div>
    <a href="{{ route('provider.jobs.work.index') }}" style="padding:.75rem 1.25rem;background:linear-gradient(135deg,#ffaa00,#ff8800);color:#000;border-radius:10px;font-weight:700;font-size:.8rem;text-decoration:none;white-space:nowrap;">Manage Work Queue</a>
</div>
@endif

@endsection