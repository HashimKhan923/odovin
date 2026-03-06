

@extends('admin.layouts.app')
@section('title','Dashboard')
@push('styles')
<style>
.dash-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1.25rem;margin-bottom:2rem;}
.row-2{display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;margin-bottom:1.5rem;}
.row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;}
.booking-row{display:flex;align-items:center;gap:1rem;padding:.875rem 0;border-bottom:1px solid var(--border-color);}
.booking-row:last-child{border-bottom:none;}
.bk-icon{width:38px;height:38px;border-radius:10px;flex-shrink:0;background:rgba(168,85,247,.1);
    display:flex;align-items:center;justify-content:center;color:var(--accent);}
.bk-icon svg{width:18px;height:18px;}
.bk-title{font-size:.875rem;font-weight:600;}
.bk-sub{font-size:.72rem;color:var(--text-tertiary);margin-top:2px;}
.pill{display:inline-flex;padding:.22rem .7rem;border-radius:20px;font-size:.65rem;font-weight:700;text-transform:uppercase;}
.pill-pending{background:rgba(255,170,0,.15);color:#ffaa00;border:1px solid rgba(255,170,0,.3);}
.pill-confirmed{background:rgba(0,212,255,.15);color:#00d4ff;border:1px solid rgba(0,212,255,.3);}
.pill-in_progress{background:rgba(168,85,247,.15);color:#a855f7;border:1px solid rgba(168,85,247,.3);}
.pill-completed{background:rgba(0,255,170,.15);color:#00ffaa;border:1px solid rgba(0,255,170,.3);}
.pill-cancelled{background:rgba(255,51,102,.15);color:#ff3366;border:1px solid rgba(255,51,102,.3);}
.rev-chart{display:flex;align-items:flex-end;gap:.5rem;height:100px;margin-top:1rem;}
.rev-col{flex:1;display:flex;flex-direction:column;align-items:center;gap:.25rem;}
.rev-bar{width:100%;border-radius:4px 4px 0 0;background:linear-gradient(180deg,var(--accent),rgba(168,85,247,.3));min-height:4px;transition:height .8s;}
.rev-lbl{font-size:.6rem;color:var(--text-tertiary);}
.user-row{display:flex;align-items:center;gap:.875rem;padding:.75rem 0;border-bottom:1px solid var(--border-color);}
.user-row:last-child{border-bottom:none;}
.ua{width:34px;height:34px;border-radius:8px;background:linear-gradient(135deg,var(--accent),var(--accent-alt));
    display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;flex-shrink:0;}
.u-name{font-size:.875rem;font-weight:600;}
.u-meta{font-size:.72rem;color:var(--text-tertiary);margin-top:1px;}
.metric-row{display:flex;justify-content:space-between;align-items:center;padding:.75rem 0;border-bottom:1px solid var(--border-color);}
.metric-row:last-child{border-bottom:none;}
.metric-lbl{font-size:.8rem;color:var(--text-secondary);}
.metric-val{font-family:'Orbitron',sans-serif;font-size:.95rem;font-weight:700;}
@media(max-width:900px){.row-2,.row-3{grid-template-columns:1fr;}}
</style>
@endpush
@section('content')

{{-- Stats --}}
<div class="dash-grid">
    <div class="stat-card" style="--accent-color:#a855f7">
        <div class="stat-icon" style="background:rgba(168,85,247,.12);color:#a855f7">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </div>
        <div class="stat-value">{{ $stats['users_total'] }}</div>
        <div class="stat-label">Total Users</div>
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
        <div class="stat-label">Service Providers</div>
    </div>
    <div class="stat-card" style="--accent-color:#ffaa00">
        <div class="stat-icon" style="background:rgba(255,170,0,.12);color:#ffaa00">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div class="stat-value">{{ $stats['bookings_total'] }}</div>
        <div class="stat-label">Total Bookings</div>
    </div>
    <div class="stat-card" style="--accent-color:#ff3366">
        <div class="stat-icon" style="background:rgba(255,51,102,.12);color:#ff3366">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div class="stat-value">{{ $stats['recalls_open'] }}</div>
        <div class="stat-label">Open Recalls</div>
    </div>
</div>

{{-- Body --}}
<div class="row-2">
    {{-- Recent Bookings --}}
    <div class="card">
        <div class="section-header">
            <div class="section-title">Recent Bookings</div>
            <a href="{{ route('admin.bookings.index') }}" class="view-all">View all →</a>
        </div>
        @forelse(\App\Models\ServiceBooking::with(['user','serviceProvider'])->latest()->limit(8)->get() as $b)
        <div class="booking-row">
            <div class="bk-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div style="flex:1;min-width:0;">
                <div class="bk-title">{{ $b->service_type }}</div>
                <div class="bk-sub">{{ $b->user->name ?? '—' }} · {{ $b->serviceProvider->name ?? '—' }}</div>
            </div>
            <div style="text-align:right;flex-shrink:0;">
                <span class="pill pill-{{ $b->status }}">{{ str_replace('_',' ',$b->status) }}</span>
                <div class="bk-sub" style="margin-top:4px;">{{ $b->scheduled_date->format('M d, Y') }}</div>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:3rem;color:var(--text-tertiary);">No bookings yet</div>
        @endforelse
    </div>

    {{-- Quick stats --}}
    <div style="display:flex;flex-direction:column;gap:1.5rem;">
        <div class="card">
            <div class="section-title" style="margin-bottom:1rem;">Booking Status</div>
            @php
                $statuses = ['pending'=>'#ffaa00','confirmed'=>'#00d4ff','in_progress'=>'#a855f7','completed'=>'#00ffaa','cancelled'=>'#ff3366'];
                $total = max(1, \App\Models\ServiceBooking::count());
            @endphp
            @foreach($statuses as $status => $color)
            @php $count = \App\Models\ServiceBooking::where('status',$status)->count(); @endphp
            <div style="margin-bottom:.875rem;">
                <div style="display:flex;justify-content:space-between;font-size:.78rem;margin-bottom:.375rem;">
                    <span style="color:var(--text-secondary);text-transform:capitalize;">{{ str_replace('_',' ',$status) }}</span>
                    <span style="font-weight:700;color:{{ $color }}">{{ $count }}</span>
                </div>
                <div style="background:rgba(255,255,255,.06);border-radius:4px;height:6px;overflow:hidden;">
                    <div style="width:{{ $total ? round(($count/$total)*100) : 0 }}%;height:100%;background:{{ $color }};border-radius:4px;transition:width .8s;"></div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="card">
            <div class="section-title" style="margin-bottom:1rem;">Platform Metrics</div>
            @php
                $avgRating = \App\Models\ServiceProvider::where('total_reviews','>',0)->avg('rating');
                $pendingBookings = \App\Models\ServiceBooking::where('status','pending')->count();
                $completedToday = \App\Models\ServiceBooking::where('status','completed')->whereDate('updated_at',today())->count();
            @endphp
            <div class="metric-row">
                <span class="metric-lbl">Avg Provider Rating</span>
                <span class="metric-val" style="color:#ffaa00">{{ number_format($avgRating ?? 0, 1) }} ★</span>
            </div>
            <div class="metric-row">
                <span class="metric-lbl">Pending Bookings</span>
                <span class="metric-val" style="color:#ff3366">{{ $pendingBookings }}</span>
            </div>
            <div class="metric-row">
                <span class="metric-lbl">Completed Today</span>
                <span class="metric-val" style="color:#00ffaa">{{ $completedToday }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Recent Users & Providers --}}
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
            <div style="margin-left:auto;font-size:.7rem;color:var(--text-tertiary);">{{ $u->created_at?->diffForHumans() ?? '—' }}</div>
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
        @forelse(\App\Models\ServiceProvider::with('user')->withCount('bookings')->orderByDesc('rating')->limit(6)->get() as $p)
        <div class="user-row">
            <div class="ua" style="background:linear-gradient(135deg,#00d4ff,#00ffaa);color:#000">{{ substr($p->name,0,1) }}</div>
            <div>
                <div class="u-name">{{ $p->name }}</div>
                <div class="u-meta">{{ $p->bookings_count }} bookings · ★ {{ number_format($p->rating,1) }}</div>
            </div>
            <div style="margin-left:auto;">
                @if($p->is_verified)<span style="font-size:.65rem;color:#00d4ff;">✓ Verified</span>@endif
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:2rem;color:var(--text-tertiary);">No providers yet</div>
        @endforelse
    </div>
    <div class="card">
        <div class="section-header">
            <div class="section-title">Quick Actions</div>
        </div>
        <div style="display:flex;flex-direction:column;gap:.75rem;">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary" style="justify-content:center;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create User
            </a>
            <a href="{{ route('admin.providers.create') }}" class="btn btn-secondary" style="justify-content:center;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Provider
            </a>
            <a href="{{ route('admin.reports.overview') }}" class="btn btn-secondary" style="justify-content:center;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                View Reports
            </a>
            <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary" style="justify-content:center;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                System Settings
            </a>
        </div>
    </div>
</div>

@endsection