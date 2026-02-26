@extends('layouts.app')
@section('title', 'My Job Posts')
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
.pg-container { max-width:1200px; margin:0 auto; padding:2rem 1.5rem; }
.page-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:2rem; flex-wrap:wrap; gap:1rem; }
.page-title { font-family:'Orbitron',sans-serif; font-size:1.75rem; font-weight:800; }
.page-title span { color:var(--accent-cyan); }
.btn-primary { display:inline-flex; align-items:center; gap:.5rem; padding:.75rem 1.5rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:10px; color:#000; font-family:'Orbitron',sans-serif; font-weight:700; font-size:.8rem; letter-spacing:.05em; cursor:pointer; text-decoration:none; transition:all .3s; box-shadow:0 4px 15px rgba(0,212,255,.3); }
.btn-primary:hover { transform:translateY(-2px); box-shadow:0 6px 25px rgba(0,212,255,.5); }
.stats-row { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:2rem; }
.stat-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:14px; padding:1.25rem 1.5rem; text-align:center; position:relative; overflow:hidden; }
.stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; }
.stat-card.open::before   { background:var(--accent-warning); }
.stat-card.accepted::before { background:var(--accent-green); }
.stat-card.completed::before { background:var(--accent-cyan); }
.stat-num { font-family:'Orbitron',sans-serif; font-size:2rem; font-weight:900; }
.stat-lbl { font-size:.75rem; color:var(--text-tertiary); text-transform:uppercase; letter-spacing:.08em; margin-top:.25rem; }
.filter-bar { background:var(--card-bg); border:1px solid var(--border-color); border-radius:14px; padding:1rem 1.5rem; margin-bottom:1.5rem; display:flex; gap:1rem; flex-wrap:wrap; align-items:center; }
.filter-select { padding:.5rem .875rem; background:var(--input-bg); border:1px solid var(--border-color); border-radius:8px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.85rem; }
.jobs-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(340px,1fr)); gap:1.25rem; }
.job-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.5rem; position:relative; overflow:hidden; transition:all .3s; }
.job-card:hover { border-color:rgba(0,212,255,.3); transform:translateY(-2px); }
.job-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; }
.job-card.status-open::before     { background:var(--accent-warning); }
.job-card.status-accepted::before { background:var(--accent-green); }
.job-card.status-completed::before{ background:var(--accent-cyan); }
.job-card.status-cancelled::before{ background:rgba(255,255,255,.2); }
.job-card.status-expired::before  { background:var(--accent-danger); }
.job-top { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1rem; }
.job-type { font-family:'Orbitron',sans-serif; font-size:1rem; font-weight:700; }
.status-badge { padding:.3rem .75rem; border-radius:20px; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; }
.status-open      { background:rgba(255,170,0,.15); color:var(--accent-warning); border:1px solid rgba(255,170,0,.3); }
.status-accepted  { background:rgba(0,255,170,.15); color:var(--accent-green);   border:1px solid rgba(0,255,170,.3); }
.status-completed { background:rgba(0,212,255,.15); color:var(--accent-cyan);    border:1px solid rgba(0,212,255,.3); }
.status-cancelled { background:rgba(255,255,255,.07); color:var(--text-tertiary); border:1px solid rgba(255,255,255,.1); }
.status-expired   { background:rgba(255,51,102,.15); color:var(--accent-danger);  border:1px solid rgba(255,51,102,.3); }
.job-meta { font-size:.8rem; color:var(--text-secondary); margin-bottom:1rem; }
.job-meta div { display:flex; align-items:center; gap:.5rem; margin-bottom:.35rem; }
.job-meta svg { width:14px; height:14px; color:var(--accent-cyan); flex-shrink:0; }
.offer-count { display:flex; align-items:center; gap:.5rem; margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border-color); }
.offer-num { font-family:'Orbitron',sans-serif; font-size:1.5rem; font-weight:800; color:var(--accent-warning); }
.offer-lbl { font-size:.75rem; color:var(--text-tertiary); }
.btn-view { display:inline-flex; align-items:center; gap:.5rem; padding:.5rem 1.1rem; background:var(--input-bg); border:1px solid var(--border-color); border-radius:8px; color:var(--accent-cyan); font-size:.8rem; font-weight:600; text-decoration:none; transition:all .3s; }
.btn-view:hover { border-color:var(--accent-cyan); background:rgba(0,212,255,.1); }
.empty-state { text-align:center; padding:5rem 2rem; color:var(--text-tertiary); }
.empty-state svg { width:64px; height:64px; margin-bottom:1.5rem; opacity:.3; }
.empty-state h3 { font-family:'Orbitron',sans-serif; font-size:1.25rem; margin-bottom:.75rem; color:var(--text-secondary); }
@media(max-width:600px) { .stats-row { grid-template-columns:1fr 1fr; } .stats-row .stat-card:last-child { grid-column:1/-1; } }
</style>

<div class="pg-container">
    <div class="page-header">
        <div>
            <div class="page-title">My <span>Job Posts</span></div>
            <p style="color:var(--text-tertiary);font-size:.875rem;margin-top:.5rem;">Post a service job and let providers compete with their best offers</p>
        </div>
        <a href="{{ route('jobs.create') }}" class="btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Post a Job
        </a>
    </div>

    @if(session('success'))
    <div style="background:rgba(0,255,170,.1);border:1px solid rgba(0,255,170,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-green);font-size:.875rem;">
        ✓ {{ session('success') }}
    </div>
    @endif

    <div class="stats-row">
        <div class="stat-card open">
            <div class="stat-num" style="color:var(--accent-warning)">{{ $stats['open'] }}</div>
            <div class="stat-lbl">Open</div>
        </div>
        <div class="stat-card accepted">
            <div class="stat-num" style="color:var(--accent-green)">{{ $stats['accepted'] }}</div>
            <div class="stat-lbl">Accepted</div>
        </div>
        <div class="stat-card completed">
            <div class="stat-num" style="color:var(--accent-cyan)">{{ $stats['completed'] }}</div>
            <div class="stat-lbl">Completed</div>
        </div>
    </div>

    <div class="filter-bar">
        <form method="GET" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:center;width:100%;">
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                @foreach(['open','accepted','completed','cancelled','expired'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if($jobs->isEmpty())
    <div class="empty-state">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        <h3>No Job Posts Yet</h3>
        <p>Post a job and let nearby service providers send you their best offers.</p>
        <a href="{{ route('jobs.create') }}" class="btn-primary" style="margin-top:1.5rem;">Post Your First Job</a>
    </div>
    @else
    <div class="jobs-grid">
        @foreach($jobs as $job)
        <div class="job-card status-{{ $job->status }}">
            <div class="job-top">
                <div class="job-type">{{ $job->service_type }}</div>
                <span class="status-badge status-{{ $job->status }}">{{ ucfirst($job->status) }}</span>
            </div>
            <div class="job-meta">
                <div>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 00-1-1h-2a1 1 0 00-1 1v5m4 0H9"/></svg>
                    {{ $job->vehicle->full_name ?? 'N/A' }}
                </div>
                <div>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $job->budgetLabel() }}
                </div>
                @if($job->preferred_date)
                <div>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ \Carbon\Carbon::parse($job->preferred_date)->format('M d, Y') }}
                </div>
                @endif
                @if($job->isOpen())
                <div style="color:var(--accent-warning);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Expires {{ $job->expires_at->diffForHumans() }}
                </div>
                @endif
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border-color);">
                <div class="offer-count">
                    <div class="offer-num">{{ $job->offers->count() }}</div>
                    <div class="offer-lbl">offer{{ $job->offers->count() !== 1 ? 's' : '' }}<br>received</div>
                </div>
                <a href="{{ route('jobs.show', $job) }}" class="btn-view">
                    View Details
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <div style="margin-top:2rem;">
        {{ $jobs->links() }}
    </div>
    @endif
</div>
@endsection