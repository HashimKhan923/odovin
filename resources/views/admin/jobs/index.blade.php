@extends('admin.layouts.app')
@section('title','Job Posts')
@push('styles')
<style>
.filter-group{display:flex;flex-direction:column;gap:.35rem;flex:1;min-width:140px;}
.filter-label{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-tertiary);}
.stats-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:1rem;margin-bottom:1.5rem;}
.mini-stat{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:1rem 1.25rem;position:relative;overflow:hidden;}
.mini-stat::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,var(--sc),transparent);}
.mini-stat-val{font-family:'Orbitron',sans-serif;font-size:1.4rem;font-weight:800;}
.mini-stat-lbl{font-size:.72rem;color:var(--text-tertiary);text-transform:uppercase;letter-spacing:.06em;margin-top:.2rem;}
</style>
@endpush
@section('content')

<div class="page-header">
    <div><div class="page-title">Job Posts</div><div class="page-sub">All service job postings from users</div></div>
</div>

<div class="stats-row">
    <div class="mini-stat" style="--sc:#ffaa00"><div class="mini-stat-val" style="color:#ffaa00">{{ $stats['total'] }}</div><div class="mini-stat-lbl">Total</div></div>
    <div class="mini-stat" style="--sc:#00d4ff"><div class="mini-stat-val" style="color:#00d4ff">{{ $stats['open'] }}</div><div class="mini-stat-lbl">Open</div></div>
    <div class="mini-stat" style="--sc:#a855f7"><div class="mini-stat-val" style="color:#a855f7">{{ $stats['accepted'] }}</div><div class="mini-stat-lbl">Accepted</div></div>
    <div class="mini-stat" style="--sc:#00ffaa"><div class="mini-stat-val" style="color:#00ffaa">{{ $stats['completed'] }}</div><div class="mini-stat-lbl">Completed</div></div>
    <div class="mini-stat" style="--sc:#6772e5"><div class="mini-stat-val" style="color:#6772e5">${{ number_format($stats['escrow_held']/100,0) }}</div><div class="mini-stat-lbl">Escrow Held ({{ $stats['escrow_count'] }})</div></div>
</div>

<form method="GET" class="filter-bar">
    <div class="filter-group">
        <span class="filter-label">Search</span>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Job #, type, user…" class="form-input" style="padding:.55rem .875rem;font-size:.8rem;">
    </div>
    <div class="filter-group" style="min-width:120px;">
        <span class="filter-label">Status</span>
        <select name="status" class="form-select" style="padding:.55rem .875rem;font-size:.8rem;">
            <option value="">All</option>
            <option value="open" @selected(request('status')=='open')>Open</option>
            <option value="accepted" @selected(request('status')=='accepted')>Accepted</option>
            <option value="expired" @selected(request('status')=='expired')>Expired</option>
            <option value="cancelled" @selected(request('status')=='cancelled')>Cancelled</option>
        </select>
    </div>
    <div class="filter-group" style="min-width:130px;">
        <span class="filter-label">Work Status</span>
        <select name="work_status" class="form-select" style="padding:.55rem .875rem;font-size:.8rem;">
            <option value="">All</option>
            <option value="pending" @selected(request('work_status')=='pending')>Pending</option>
            <option value="confirmed" @selected(request('work_status')=='confirmed')>Confirmed</option>
            <option value="in_progress" @selected(request('work_status')=='in_progress')>In Progress</option>
            <option value="completed" @selected(request('work_status')=='completed')>Completed</option>
            <option value="cancelled" @selected(request('work_status')=='cancelled')>Cancelled</option>
        </select>
    </div>
    <div class="filter-group" style="min-width:120px;">
        <span class="filter-label">Payment</span>
        <select name="payment_status" class="form-select" style="padding:.55rem .875rem;font-size:.8rem;">
            <option value="">All</option>
            <option value="unpaid" @selected(request('payment_status')=='unpaid')>Unpaid</option>
            <option value="held" @selected(request('payment_status')=='held')>Held</option>
            <option value="released" @selected(request('payment_status')=='released')>Released</option>
            <option value="refunded" @selected(request('payment_status')=='refunded')>Refunded</option>
        </select>
    </div>
    <div style="display:flex;align-items:flex-end;gap:.5rem;">
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        @if(request()->hasAny(['search','status','work_status','payment_status']))
        <a href="{{ route('admin.jobs.index') }}" class="btn btn-secondary btn-sm">Clear</a>
        @endif
    </div>
</form>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Job #</th>
                <th>Service Type</th>
                <th>User</th>
                <th>Provider</th>
                <th>Budget</th>
                <th>Status</th>
                <th>Work Status</th>
                <th>Payment</th>
                <th>Posted</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jobs as $job)
            <tr>
                <td><span style="font-family:'Orbitron',sans-serif;font-size:.78rem;color:var(--accent-cyan);">{{ $job->job_number }}</span></td>
                <td>
                    <div style="font-weight:600;font-size:.875rem;">{{ $job->service_type }}</div>
                    <div style="font-size:.72rem;color:var(--text-tertiary);">{{ $job->offers->count() }} offer(s)</div>
                </td>
                <td>
                    <div style="font-size:.875rem;">{{ $job->user->name ?? '—' }}</div>
                    <div style="font-size:.72rem;color:var(--text-tertiary);">{{ $job->user->email ?? '' }}</div>
                </td>
                <td style="font-size:.875rem;">{{ $job->assignedProvider->name ?? '—' }}</td>
                <td style="font-size:.875rem;">{{ $job->budgetLabel() }}</td>
                <td><span class="pill pill-{{ $job->status }}">{{ $job->status }}</span></td>
                <td>
                    @if($job->work_status)
                        <span class="pill pill-{{ $job->work_status }}">{{ str_replace('_',' ',$job->work_status) }}</span>
                    @else
                        <span style="color:var(--text-tertiary);font-size:.8rem;">—</span>
                    @endif
                </td>
                <td>
                    @if($job->payment_status)
                        @php $pclr = match($job->payment_status) { 'held'=>'#6772e5','released'=>'#00ffaa','refunded'=>'#ff3366',default=>'#ffaa00' }; @endphp
                        <span style="font-size:.75rem;font-weight:700;color:{{ $pclr }}">{{ ucfirst($job->payment_status) }}</span>
                    @else
                        <span style="color:var(--text-tertiary);font-size:.8rem;">—</span>
                    @endif
                </td>
                <td style="font-size:.78rem;color:var(--text-tertiary);">{{ $job->created_at?->format('M d, Y') }}</td>
                <td>
                    <div style="display:flex;gap:.375rem;">
                        <a href="{{ route('admin.jobs.show', $job) }}" class="act act-view">View</a>
                        <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}" onsubmit="return confirm('Delete this job?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="act act-del">Del</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="10" style="text-align:center;padding:3rem;color:var(--text-tertiary);">No job posts found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($jobs->hasPages())
<div style="margin-top:1.5rem;display:flex;justify-content:center;">{{ $jobs->links() }}</div>
@endif

@endsection