@extends('admin.layouts.app')
@section('title','Dashboard')
@push('styles')
<style>
.dash-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1.25rem;margin-bottom:2rem;}
.row-2{display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;margin-bottom:1.5rem;}
.row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;}
.job-row{display:flex;align-items:center;gap:1rem;padding:.875rem 0;border-bottom:1px solid var(--border-color);}
.job-row:last-child{border-bottom:none;}
.jk-icon{width:38px;height:38px;border-radius:10px;flex-shrink:0;background:rgba(0,212,255,.1);display:flex;align-items:center;justify-content:center;color:var(--accent-cyan);}
.jk-icon svg{width:18px;height:18px;}
.jk-title{font-size:.875rem;font-weight:600;}
.jk-sub{font-size:.72rem;color:var(--text-tertiary);margin-top:2px;}
.pill-open{background:rgba(0,212,255,.15);color:#00d4ff;border:1px solid rgba(0,212,255,.3);}
.pill-accepted,.pill-in_progress{background:rgba(168,85,247,.15);color:#a855f7;border:1px solid rgba(168,85,247,.3);}
.pill-completed{background:rgba(0,255,170,.15);color:#00ffaa;border:1px solid rgba(0,255,170,.3);}
.pill-cancelled,.pill-expired{background:rgba(255,51,102,.15);color:#ff3366;border:1px solid rgba(255,51,102,.3);}
.pill-pending{background:rgba(255,170,0,.15);color:#ffaa00;border:1px solid rgba(255,170,0,.3);}
.rev-chart{display:flex;align-items:flex-end;gap:.5rem;height:100px;margin-top:1rem;}
.rev-col{flex:1;display:flex;flex-direction:column;align-items:center;gap:.25rem;}
.rev-bar{width:100%;border-radius:4px 4px 0 0;background:linear-gradient(180deg,var(--accent-cyan),rgba(0,212,255,.2));min-height:4px;}
.rev-lbl{font-size:.6rem;color:var(--text-tertiary);}
.user-row{display:flex;align-items:center;gap:.875rem;padding:.75rem 0;border-bottom:1px solid var(--border-color);}
.user-row:last-child{border-bottom:none;}
.ua{width:34px;height:34px;border-radius:8px;background:linear-gradient(135deg,var(--accent),var(--accent-alt));display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;flex-shrink:0;}
.u-name{font-size:.875rem;font-weight:600;}
.u-meta{font-size:.72rem;color:var(--text-tertiary);margin-top:1px;}
.metric-row{display:flex;justify-content:space-between;align-items:center;padding:.75rem 0;border-bottom:1px solid var(--border-color);}
.metric-row:last-child{border-bottom:none;}
.metric-lbl{font-size:.8rem;color:var(--text-secondary);}
.metric-val{font-family:'Orbitron',sans-serif;font-size:.95rem;font-weight:700;}
.overdue-badge{display:inline-flex;align-items:center;gap:.3rem;background:rgba(255,51,102,.12);border:1px solid rgba(255,51,102,.3);border-radius:6px;padding:.15rem .5rem;font-size:.65rem;color:#ff3366;font-weight:700;}
@media(max-width:900px){.row-2,.row-3{grid-template-columns:1fr;}}
</style>
@endpush
@section('content')

{{-- 8-stat KPI grid --}}
<div class="dash-grid">
    <div class="stat-card" style="--accent-color:#a855f7">
        <div class="stat-icon" style="background:rgba(168,85,247,.12);color:#a855f7">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </div>
        <div class="stat-value">{{ $stats['users_total'] }}</div>
        <div class="stat-label">Total Users</div>
        <div style="font-size:.7rem;color:var(--accent-green);margin-top:.35rem;">+{{ $stats['users_new_week'] }} this week</div>
    </div>
    <div class="stat-card" style="--accent-color:#00d4ff">
        <div class="stat-icon" style="background:rgba(0,212,255,.12);color:#00d4ff">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 1h1m8-1h3M4 16h1m12-9l2 2v3h-3M9 6h5"/></svg>
        </div>
        <div class="stat-value">{{ $stats['vehicles_total'] }}</div>
        <div class="stat-label">Total Vehicles</div>
    </div>
    <div class="stat-card" style="--accent-color:#00ffaa">
        <div class="stat-icon" style="background:rgba(0,255,170,.12);color:#00ffaa">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <div class="stat-value">{{ $stats['providers_total'] }}</div>
        <div class="stat-label">Providers</div>
        <div style="font-size:.7rem;color:#00d4ff;margin-top:.35rem;">{{ $stats['providers_verified'] }} verified</div>
    </div>
    <div class="stat-card" style="--accent-color:#ffaa00">
        <div class="stat-icon" style="background:rgba(255,170,0,.12);color:#ffaa00">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div class="stat-value">{{ $stats['jobs_total'] }}</div>
        <div class="stat-label">Job Posts</div>
        <div style="font-size:.7rem;color:#ffaa00;margin-top:.35rem;">{{ $stats['jobs_open'] }} open</div>
    </div>
    <div class="stat-card" style="--accent-color:#6772e5">
        <div class="stat-icon" style="background:rgba(103,114,229,.12);color:#6772e5">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <div class="stat-value">${{ number_format($stats['escrow_held']/100,0) }}</div>
        <div class="stat-label">Escrow Held</div>
        @if($stats['escrow_overdue'] > 0)
            <div class="overdue-badge" style="margin-top:.35rem;">⚠ {{ $stats['escrow_overdue'] }} overdue</div>
        @else
            <div style="font-size:.7rem;color:var(--text-tertiary);margin-top:.35rem;">{{ $stats['escrow_held_count'] }} active</div>
        @endif
    </div>
    <div class="stat-card" style="--accent-color:#00ffaa">
        <div class="stat-icon" style="background:rgba(0,255,170,.12);color:#00ffaa">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-value">${{ number_format($stats['platform_fees']/100,0) }}</div>
        <div class="stat-label">Platform Fees</div>
        <div style="font-size:.7rem;color:var(--text-tertiary);margin-top:.35rem;">all-time earned</div>
    </div>
    <div class="stat-card" style="--accent-color:#a855f7">
        <div class="stat-icon" style="background:rgba(168,85,247,.12);color:#a855f7">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
        </div>
        <div class="stat-value">{{ $stats['subs_active'] }}</div>
        <div class="stat-label">Active Subs</div>
        @if($stats['subs_past_due'] > 0)
            <div class="overdue-badge" style="margin-top:.35rem;">⚠ {{ $stats['subs_past_due'] }} past due</div>
        @else
            <div style="font-size:.7rem;color:#00d4ff;margin-top:.35rem;">{{ $stats['subs_trialing'] }} trialing</div>
        @endif
    </div>
    <div class="stat-card" style="--accent-color:#ff3366">
        <div class="stat-icon" style="background:rgba(255,51,102,.12);color:#ff3366">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div class="stat-value">{{ $stats['recalls_open'] }}</div>
        <div class="stat-label">Open Recalls</div>
    </div>
</div>

{{-- Row 2: Recent jobs + right panels --}}
<div class="row-2">
    <div class="card">
        <div class="section-header">
            <div class="section-title">Recent Job Posts</div>
            <a href="{{ route('admin.jobs.index') }}" class="view-all">View all →</a>
        </div>
        @forelse($recentJobs as $job)
        <div class="job-row">
            <div class="jk-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div style="flex:1;min-width:0;">
                <div class="jk-title">{{ $job->service_type }}</div>
                <div class="jk-sub">#{{ $job->job_number }} · {{ $job->user->name ?? '—' }}</div>
            </div>
            <div style="text-align:right;flex-shrink:0;">
                <span class="pill pill-{{ $job->status }}">{{ $job->status }}</span>
                <div class="jk-sub" style="margin-top:4px;">{{ $job->created_at?->diffForHumans() }}</div>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:3rem;color:var(--text-tertiary);">No job posts yet</div>
        @endforelse
    </div>

    <div style="display:flex;flex-direction:column;gap:1.5rem;">
        {{-- Escrow Revenue chart --}}
        <div class="card">
            <div class="section-header">
                <div class="section-title">Escrow Revenue (6mo)</div>
                <a href="{{ route('admin.payments.escrow') }}" class="view-all">Escrow →</a>
            </div>
            @php $maxRev = max(array_column($revenueChart,'revenue') ?: [1]); @endphp
            <div class="rev-chart">
                @foreach($revenueChart as $item)
                <div class="rev-col">
                    <div class="rev-bar" style="height:{{ $maxRev > 0 ? max(4,($item['revenue']/$maxRev)*100) : 4 }}%;"></div>
                    <div class="rev-lbl">{{ $item['month'] }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Job Status bars --}}
        <div class="card">
            <div class="section-title" style="margin-bottom:1rem;">Job Status</div>
            @php
                $jobStatuses = [['open','#00d4ff',$stats['jobs_open']],['in progress','#a855f7',$stats['jobs_in_progress']],['completed','#00ffaa',$stats['jobs_completed']]];
                $jt = max(1,$stats['jobs_total']);
            @endphp
            @foreach($jobStatuses as [$label,$color,$count])
            <div style="margin-bottom:.875rem;">
                <div style="display:flex;justify-content:space-between;font-size:.78rem;margin-bottom:.375rem;">
                    <span style="color:var(--text-secondary);text-transform:capitalize;">{{ $label }}</span>
                    <span style="font-weight:700;color:{{ $color }}">{{ $count }}</span>
                </div>
                <div style="background:rgba(255,255,255,.06);border-radius:4px;height:6px;overflow:hidden;">
                    <div style="width:{{ round(($count/$jt)*100) }}%;height:100%;background:{{ $color }};border-radius:4px;"></div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Plan breakdown --}}
        <div class="card">
            <div class="section-header">
                <div class="section-title">Subscriptions</div>
                <a href="{{ route('admin.payments.subscriptions') }}" class="view-all">Manage →</a>
            </div>
            @foreach($planBreakdown as $plan)
            <div class="metric-row">
                <span class="metric-lbl">{{ $plan->name }}</span>
                <span class="metric-val" style="color:{{ $plan->slug==='premium'?'#a855f7':($plan->slug==='pro'?'#00d4ff':'var(--text-tertiary)') }}">{{ $plan->active_count }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Row 3: Users + Providers + Quick Actions --}}
<div class="row-3">
    <div class="card">
        <div class="section-header">
            <div class="section-title">New Users</div>
            <a href="{{ route('admin.users.index') }}" class="view-all">View all →</a>
        </div>
        @forelse(\App\Models\User::where('user_type','user')->latest()->limit(6)->get() as $u)
        <div class="user-row">
            <div class="ua">{{ substr($u->name,0,1) }}</div>
            <div>
                <div class="u-name">{{ $u->name }}</div>
                <div class="u-meta">{{ $u->email }}</div>
            </div>
            <div style="margin-left:auto;font-size:.7rem;color:var(--text-tertiary);">{{ $u->created_at?->diffForHumans() }}</div>
        </div>
        @empty
        <div style="text-align:center;padding:2rem;color:var(--text-tertiary);">No users yet</div>
        @endforelse
    </div>

    <div class="card">
        <div class="section-header">
            <div class="section-title">Top Providers</div>
            <a href="{{ route('admin.providers.index') }}" class="view-all">View all →</a>
        </div>
        @forelse(\App\Models\ServiceProvider::withCount('bookings')->orderByDesc('rating')->limit(6)->get() as $p)
        <div class="user-row">
            <div class="ua" style="background:linear-gradient(135deg,#00d4ff,#00ffaa);color:#000">{{ substr($p->name,0,1) }}</div>
            <div>
                <div class="u-name">{{ $p->name }}</div>
                <div class="u-meta">{{ $p->bookings_count }} bookings · ★ {{ number_format($p->rating,1) }}</div>
            </div>
            <div style="margin-left:auto;">
                @if($p->is_verified)<span style="font-size:.65rem;color:#00d4ff;">✓</span>@endif
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:2rem;color:var(--text-tertiary);">No providers yet</div>
        @endforelse
    </div>

    <div class="card">
        <div class="section-header"><div class="section-title">Quick Actions</div></div>
        <div style="display:flex;flex-direction:column;gap:.75rem;">
            <a href="{{ route('admin.jobs.index') }}" class="btn btn-secondary" style="justify-content:center;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Manage Job Posts
            </a>
            <a href="{{ route('admin.payments.escrow') }}" class="btn btn-secondary" style="justify-content:center;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Escrow &amp; Payments
            </a>
            <a href="{{ route('admin.payments.subscriptions') }}" class="btn btn-secondary" style="justify-content:center;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                Subscriptions
            </a>
            <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-secondary" style="justify-content:center;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Plan Settings
            </a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary" style="justify-content:center;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create User
            </a>
        </div>
    </div>
</div>

@endsection