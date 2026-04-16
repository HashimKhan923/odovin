@extends('admin.layouts.app')
@section('title', 'Reports')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Reports & Analytics</div>
        <div class="page-sub">Platform-wide reporting and data exports</div>
    </div>
</div>

{{-- Report cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.25rem;margin-bottom:2rem;">

    <a href="{{ route('admin.reports.overview') }}" class="card" style="text-decoration:none;cursor:pointer;transition:all .3s;" onmouseover="this.style.borderColor='rgba(168,85,247,.4)'" onmouseout="this.style.borderColor='var(--border-color)'">
        <div style="width:48px;height:48px;border-radius:12px;background:rgba(168,85,247,.15);color:#a855f7;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <div style="font-family:'Orbitron',sans-serif;font-size:.9rem;font-weight:700;margin-bottom:.5rem;">Overview Report</div>
        <div style="font-size:.8rem;color:var(--text-tertiary);line-height:1.6;">General statistics and platform trends across all time periods</div>
        <div style="margin-top:1.25rem;font-size:.78rem;color:#a855f7;font-weight:600;display:flex;align-items:center;gap:.375rem;">
            View Report
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>
    </a>

    <a href="{{ route('admin.reports.users') }}" class="card" style="text-decoration:none;cursor:pointer;transition:all .3s;" onmouseover="this.style.borderColor='rgba(0,212,255,.4)'" onmouseout="this.style.borderColor='var(--border-color)'">
        <div style="width:48px;height:48px;border-radius:12px;background:rgba(0,212,255,.15);color:#00d4ff;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </div>
        <div style="font-family:'Orbitron',sans-serif;font-size:.9rem;font-weight:700;margin-bottom:.5rem;">User Report</div>
        <div style="font-size:.8rem;color:var(--text-tertiary);line-height:1.6;">User registrations, activity trends, and growth over time</div>
        <div style="margin-top:1.25rem;font-size:.78rem;color:#00d4ff;font-weight:600;display:flex;align-items:center;gap:.375rem;">
            View Report
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>
    </a>

    <a href="{{ route('admin.reports.revenue') }}" class="card" style="text-decoration:none;cursor:pointer;transition:all .3s;" onmouseover="this.style.borderColor='rgba(0,255,170,.4)'" onmouseout="this.style.borderColor='var(--border-color)'">
        <div style="width:48px;height:48px;border-radius:12px;background:rgba(0,255,170,.15);color:#00ffaa;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div style="font-family:'Orbitron',sans-serif;font-size:.9rem;font-weight:700;margin-bottom:.5rem;">Revenue Report</div>
        <div style="font-size:.8rem;color:var(--text-tertiary);line-height:1.6;">Escrow payouts, platform fees earned, and subscription MRR</div>
        <div style="margin-top:1.25rem;font-size:.78rem;color:#00ffaa;font-weight:600;display:flex;align-items:center;gap:.375rem;">
            View Report
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>
    </a>

    <a href="{{ route('admin.reports.vehicles') }}" class="card" style="text-decoration:none;cursor:pointer;transition:all .3s;" onmouseover="this.style.borderColor='rgba(255,170,0,.4)'" onmouseout="this.style.borderColor='var(--border-color)'">
        <div style="width:48px;height:48px;border-radius:12px;background:rgba(255,170,0,.15);color:#ffaa00;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 1h1m8-1h3M4 16h1m12-9l2 2v3h-3M9 6h5"/></svg>
        </div>
        <div style="font-family:'Orbitron',sans-serif;font-size:.9rem;font-weight:700;margin-bottom:.5rem;">Vehicle Report</div>
        <div style="font-size:.8rem;color:var(--text-tertiary);line-height:1.6;">Fleet stats, make/model distribution, fuel types and recalls</div>
        <div style="margin-top:1.25rem;font-size:.78rem;color:#ffaa00;font-weight:600;display:flex;align-items:center;gap:.375rem;">
            View Report
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>
    </a>

    <a href="{{ route('admin.jobs.index') }}" class="card" style="text-decoration:none;cursor:pointer;transition:all .3s;" onmouseover="this.style.borderColor='rgba(103,114,229,.4)'" onmouseout="this.style.borderColor='var(--border-color)'">
        <div style="width:48px;height:48px;border-radius:12px;background:rgba(103,114,229,.15);color:#6772e5;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div style="font-family:'Orbitron',sans-serif;font-size:.9rem;font-weight:700;margin-bottom:.5rem;">Job Posts</div>
        <div style="font-size:.8rem;color:var(--text-tertiary);line-height:1.6;">All job posts — filter by status, payment state and work progress</div>
        <div style="margin-top:1.25rem;font-size:.78rem;color:#6772e5;font-weight:600;display:flex;align-items:center;gap:.375rem;">
            View All Jobs
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>
    </a>

    <a href="{{ route('admin.payments.escrow') }}" class="card" style="text-decoration:none;cursor:pointer;transition:all .3s;" onmouseover="this.style.borderColor='rgba(255,51,102,.4)'" onmouseout="this.style.borderColor='var(--border-color)'">
        <div style="width:48px;height:48px;border-radius:12px;background:rgba(255,51,102,.15);color:#ff3366;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <div style="font-family:'Orbitron',sans-serif;font-size:.9rem;font-weight:700;margin-bottom:.5rem;">Escrow & Payments</div>
        <div style="font-size:.8rem;color:var(--text-tertiary);line-height:1.6;">Held, released and refunded escrow with full Stripe details</div>
        <div style="margin-top:1.25rem;font-size:.78rem;color:#ff3366;font-weight:600;display:flex;align-items:center;gap:.375rem;">
            View Escrow
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>
    </a>

</div>

{{-- Platform summary strip --}}
<div class="card">
    <div class="card-title">Live Platform Summary</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:0;">
        @php
            $summary = [
                ['Users',         \App\Models\User::where('user_type','user')->count(),                                    '#a855f7'],
                ['Providers',     \App\Models\ServiceProvider::count(),                                                    '#00d4ff'],
                ['Vehicles',      \App\Models\Vehicle::count(),                                                            '#ffaa00'],
                ['Job Posts',     \App\Models\ServiceJobPost::count(),                                                     '#6772e5'],
                ['Offers',        \App\Models\ServiceJobOffer::count(),                                                    '#00d4ff'],
                ['Escrow Held',   '$'.number_format(\App\Models\JobEscrow::where('status','held')->sum('amount')/100,0),   '#ff3366'],
                ['Fees Earned',   '$'.number_format(\App\Models\JobEscrow::where('status','released')->sum('platform_fee')/100,0), '#00ffaa'],
                ['Active Subs',   \App\Models\ProviderSubscription::where('status','active')->count(),                    '#a855f7'],
            ];
        @endphp
        @foreach($summary as $i => [$label, $val, $color])
        <div style="text-align:center;padding:1.5rem 1rem;{{ $i < count($summary)-1 ? 'border-right:1px solid var(--border-color);' : '' }}">
            <div style="font-family:'Orbitron',sans-serif;font-size:1.4rem;font-weight:800;color:{{ $color }};">{{ $val }}</div>
            <div style="font-size:.7rem;color:var(--text-tertiary);text-transform:uppercase;letter-spacing:.07em;margin-top:.35rem;">{{ $label }}</div>
        </div>
        @endforeach
    </div>
</div>

@endsection