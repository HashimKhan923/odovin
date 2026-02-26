@extends('provider.layouts.app')
@section('title', 'My Work Queue')

@section('content')
<style>
.pg { padding:2rem; }
.page-title { font-family:'Orbitron',sans-serif; font-size:1.5rem; font-weight:800; margin-bottom:.35rem; }
.page-title span { color:var(--accent-cyan); }
.stats-row { display:grid; grid-template-columns:repeat(5,1fr); gap:1rem; margin-bottom:2rem; }
.stat-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:14px; padding:1.25rem 1.5rem; text-align:center; }
.stat-num { font-family:'Orbitron',sans-serif; font-size:1.75rem; font-weight:800; }
.stat-lbl { font-size:.75rem; color:var(--text-tertiary); margin-top:.25rem; text-transform:uppercase; letter-spacing:.05em; }
.tab-row { display:flex; gap:.5rem; margin-bottom:1.5rem; flex-wrap:wrap; }
.tab-btn { padding:.5rem 1.25rem; border-radius:20px; border:1px solid var(--border-color); background:transparent; color:var(--text-secondary); font-family:'Chakra Petch',sans-serif; font-size:.8rem; font-weight:600; text-decoration:none; transition:all .3s; }
.tab-btn.active, .tab-btn:hover { border-color:var(--accent-cyan); color:var(--accent-cyan); background:rgba(0,212,255,.08); }
.filter-bar { background:var(--card-bg); border:1px solid var(--border-color); border-radius:12px; padding:.875rem 1.25rem; margin-bottom:1.5rem; display:flex; gap:.75rem; }
.filter-select { padding:.5rem .875rem; background:rgba(0,212,255,.05); border:1px solid var(--border-color); border-radius:8px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.85rem; }
.work-list { display:flex; flex-direction:column; gap:1rem; }
.work-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.5rem; display:grid; grid-template-columns:1fr auto; gap:1rem; align-items:start; transition:all .3s; }
.work-card:hover { border-color:rgba(0,212,255,.3); transform:translateY(-1px); }
.work-card::before { content:''; display:block; width:3px; position:absolute; top:0; bottom:0; left:0; border-radius:16px 0 0 16px; }
.work-card { position:relative; overflow:hidden; }
.ws-pending     .left-bar { background:var(--accent-warning); }
.ws-confirmed   .left-bar { background:var(--accent-cyan); }
.ws-in_progress .left-bar { background:#a855f7; }
.ws-completed   .left-bar { background:var(--accent-green); }
.ws-cancelled   .left-bar { background:#ff3366; }
.left-bar { position:absolute; top:0; bottom:0; left:0; width:3px; }
.job-type { font-family:'Orbitron',sans-serif; font-size:1rem; font-weight:700; margin-bottom:.5rem; }
.job-num  { font-size:.72rem; color:var(--text-tertiary); font-family:'Chakra Petch',sans-serif; }
.meta-row { display:flex; gap:1rem; flex-wrap:wrap; margin-top:.625rem; font-size:.8rem; color:var(--text-secondary); }
.meta-item { display:flex; align-items:center; gap:.35rem; }
.pill { display:inline-flex; align-items:center; gap:.35rem; padding:.3rem .75rem; border-radius:20px; font-size:.72rem; font-weight:700; white-space:nowrap; }
.pill-pending     { background:rgba(255,170,0,.12); color:#ffaa00; border:1px solid rgba(255,170,0,.3); }
.pill-confirmed   { background:rgba(0,212,255,.12); color:#00d4ff; border:1px solid rgba(0,212,255,.3); }
.pill-in_progress { background:rgba(168,85,247,.12); color:#a855f7; border:1px solid rgba(168,85,247,.3); }
.pill-completed   { background:rgba(0,255,170,.12); color:#00ffaa; border:1px solid rgba(0,255,170,.3); }
.pill-cancelled   { background:rgba(255,51,102,.12); color:#ff3366; border:1px solid rgba(255,51,102,.3); }
.price { font-family:'Orbitron',sans-serif; font-size:1.5rem; font-weight:800; color:var(--accent-warning); }
.btn-view { display:inline-flex; align-items:center; gap:.5rem; padding:.6rem 1.25rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:8px; color:#000; font-family:'Orbitron',sans-serif; font-weight:700; font-size:.75rem; cursor:pointer; text-decoration:none; margin-top:.875rem; transition:all .3s; }
.btn-view:hover { transform:translateY(-1px); box-shadow:0 4px 15px rgba(0,212,255,.4); }
.empty-state { text-align:center; padding:5rem 2rem; color:var(--text-tertiary); }
@media(max-width:600px) { .stats-row { grid-template-columns:repeat(2,1fr); } .work-card { grid-template-columns:1fr; } }
</style>

<div class="pg">
    <div style="margin-bottom:1.5rem;">
        <div class="page-title">My Work <span>Queue</span></div>
        <div style="color:var(--text-tertiary);font-size:.85rem;margin-top:.25rem;">Track and manage your accepted jobs</div>
    </div>

    @if(session('success'))
    <div style="background:rgba(0,255,170,.1);border:1px solid rgba(0,255,170,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-green);font-size:.875rem;">✓ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div style="background:rgba(255,51,102,.1);border:1px solid rgba(255,51,102,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:#ff8099;font-size:.875rem;">{{ session('error') }}</div>
    @endif

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-card"><div class="stat-num" style="color:var(--accent-warning);">{{ $stats['pending'] }}</div><div class="stat-lbl">Pending</div></div>
        <div class="stat-card"><div class="stat-num" style="color:var(--accent-cyan);">{{ $stats['confirmed'] }}</div><div class="stat-lbl">Confirmed</div></div>
        <div class="stat-card"><div class="stat-num" style="color:#a855f7;">{{ $stats['in_progress'] }}</div><div class="stat-lbl">In Progress</div></div>
        <div class="stat-card"><div class="stat-num" style="color:var(--accent-green);">{{ $stats['completed'] }}</div><div class="stat-lbl">Completed</div></div>
        <div class="stat-card"><div class="stat-num" style="color:var(--accent-green);">${{ number_format($stats['total_earned'], 0) }}</div><div class="stat-lbl">Total Earned</div></div>
    </div>

    {{-- Tabs --}}
    <div class="tab-row">
        <a href="{{ route('provider.jobs.index') }}" class="tab-btn">🗺 Job Board</a>
        <a href="{{ route('provider.jobs.my-offers') }}" class="tab-btn">📋 My Offers</a>
        <a href="{{ route('provider.jobs.work.index') }}" class="tab-btn active">🔧 Work Queue</a>
    </div>

    {{-- Filter --}}
    <div class="filter-bar">
        <form method="GET" style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;">
            <select name="work_status" class="filter-select" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                @foreach(['pending','confirmed','in_progress','completed','cancelled'] as $s)
                <option value="{{ $s }}" {{ request('work_status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Work Cards --}}
    <div class="work-list">
        @forelse($offers as $offer)
        @php $job = $offer->jobPost; $ws = $job->work_status ?? 'pending'; @endphp
        <div class="work-card ws-{{ $ws }}">
            <div class="left-bar"></div>
            <div>
                <div style="display:flex;align-items:flex-start;gap:1rem;flex-wrap:wrap;">
                    <div>
                        <div class="job-type">{{ $job->service_type }}</div>
                        <div class="job-num">Job #{{ $job->job_number }}</div>
                    </div>
                    <span class="pill pill-{{ $ws }}">{{ ucfirst(str_replace('_',' ',$ws)) }}</span>
                    @if($job->rating)
                    <span style="display:inline-flex;align-items:center;gap:.35rem;font-size:.8rem;color:#ffaa00;">★ {{ $job->rating }}/5 reviewed</span>
                    @endif
                </div>
                <div class="meta-row">
                    <span class="meta-item">🚗 {{ $job->vehicle->year }} {{ $job->vehicle->make }} {{ $job->vehicle->model }}</span>
                    <span class="meta-item">👤 {{ $job->user->name }}</span>
                    @if($job->preferred_date)<span class="meta-item">📅 {{ \Carbon\Carbon::parse($job->preferred_date)->format('M d, Y') }}</span>@endif
                    <span class="meta-item">💰 Your offer: <strong>${{ number_format($offer->offered_price,2) }}</strong></span>
                    @if($job->final_cost)<span class="meta-item">✅ Final: <strong>${{ number_format($job->final_cost,2) }}</strong></span>@endif
                </div>
                <a href="{{ route('provider.jobs.work.show', $job) }}" class="btn-view">Manage Job →</a>
            </div>
            <div style="text-align:right;">
                <div class="price">${{ number_format($offer->offered_price,2) }}</div>
                <div style="font-size:.72rem;color:var(--text-tertiary);margin-top:.35rem;">{{ $offer->created_at->diffForHumans() }}</div>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:.2;margin-bottom:1rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <h3 style="font-family:'Orbitron',sans-serif;font-size:1rem;color:var(--text-secondary);margin-bottom:.5rem;">No Jobs in Queue</h3>
            <p>Accepted jobs will appear here once customers choose your offer.</p>
            <a href="{{ route('provider.jobs.index') }}" style="display:inline-block;margin-top:1rem;color:var(--accent-cyan);text-decoration:none;font-size:.875rem;font-weight:600;">Browse Open Jobs →</a>
        </div>
        @endforelse
    </div>

    @if($offers->hasPages())
    <div style="margin-top:2rem;">{{ $offers->links() }}</div>
    @endif
</div>
@endsection