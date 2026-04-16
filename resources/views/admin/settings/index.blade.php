@extends('admin.layouts.app')
@section('title', 'Settings')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">System Settings</div>
        <div class="page-sub">Platform configuration, cache management and system health</div>
    </div>
    <a href="{{ route('admin.settings.system-info') }}" class="btn btn-secondary">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
        Full System Info
    </a>
</div>

<div class="grid-2" style="margin-bottom:1.5rem;">

    {{-- System info --}}
    <div class="card">
        <div class="card-title">System Information</div>
        @php
            $sysInfo = [
                ['PHP Version',    phpversion(),             '#00d4ff'],
                ['Laravel',        app()->version(),         '#a855f7'],
                ['Environment',    app()->environment(),     app()->environment()==='production' ? '#00ffaa' : '#ffaa00'],
                ['Debug Mode',     config('app.debug') ? 'Enabled' : 'Disabled', config('app.debug') ? '#ff3366' : '#00ffaa'],
                ['Timezone',       config('app.timezone'),   'var(--text-secondary)'],
                ['App URL',        config('app.url'),        'var(--text-secondary)'],
            ];
        @endphp
        @foreach($sysInfo as [$label, $value, $color])
        <div style="display:flex;justify-content:space-between;align-items:center;padding:.875rem 0;border-bottom:1px solid var(--border-color);">
            <span style="font-size:.8rem;color:var(--text-tertiary);">{{ $label }}</span>
            <span style="font-size:.82rem;font-weight:700;color:{{ $color }};font-family:'Orbitron',sans-serif;max-width:60%;text-align:right;word-break:break-all;">{{ $value }}</span>
        </div>
        @endforeach
        <div style="margin-top:1.25rem;">
            <a href="{{ route('admin.settings.system-info') }}" class="btn btn-secondary btn-sm">View Full System Info →</a>
        </div>
    </div>

    {{-- Cache management --}}
    <div class="card">
        <div class="card-title">Cache Management</div>
        <div style="font-size:.875rem;color:var(--text-secondary);line-height:1.75;margin-bottom:1.5rem;">
            Clear application caches to resolve stale data issues or after making configuration changes. Caches will rebuild automatically on the next request.
        </div>
        <div style="background:rgba(255,51,102,.06);border:1px solid rgba(255,51,102,.2);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;">
            <div style="font-size:.78rem;font-weight:700;color:#ff8099;margin-bottom:.35rem;">⚠ Warning</div>
            <div style="font-size:.78rem;color:var(--text-secondary);">This clears config, route, view and application caches. Pages may load slightly slower until caches rebuild.</div>
        </div>
        <form method="POST" action="{{ route('admin.settings.clear-cache') }}">
            @csrf
            <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center;" onclick="return confirm('Clear all application caches?')">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Clear All Caches
            </button>
        </form>
    </div>

</div>

<div class="grid-2">

    {{-- Platform stats --}}
    <div class="card">
        <div class="card-title">Platform Stats</div>
        @php
            $pStats = [
                ['Total Users',         \App\Models\User::where('user_type','user')->count(),                         '#a855f7'],
                ['Service Providers',   \App\Models\ServiceProvider::count(),                                         '#00d4ff'],
                ['Verified Providers',  \App\Models\ServiceProvider::where('is_verified',true)->count(),              '#00d4ff'],
                ['Vehicles',            \App\Models\Vehicle::count(),                                                 '#ffaa00'],
                ['Job Posts',           \App\Models\ServiceJobPost::count(),                                          '#6772e5'],
                ['Open Jobs',           \App\Models\ServiceJobPost::where('status','open')->count(),                  '#ffaa00'],
                ['Active Subscriptions',\App\Models\ProviderSubscription::where('status','active')->count(),          '#a855f7'],
                ['Past Due Subs',       \App\Models\ProviderSubscription::where('status','past_due')->count(),        '#ff3366'],
                ['Escrow Held',         '$'.number_format(\App\Models\JobEscrow::where('status','held')->sum('amount')/100,0), '#6772e5'],
                ['Platform Fees Earned','$'.number_format(\App\Models\JobEscrow::where('status','released')->sum('platform_fee')/100,0), '#00ffaa'],
            ];
        @endphp
        @foreach($pStats as [$label, $value, $color])
        <div style="display:flex;justify-content:space-between;align-items:center;padding:.75rem 0;border-bottom:1px solid var(--border-color);">
            <span style="font-size:.82rem;color:var(--text-secondary);">{{ $label }}</span>
            <span style="font-family:'Orbitron',sans-serif;font-size:.9rem;font-weight:700;color:{{ $color }};">{{ $value }}</span>
        </div>
        @endforeach
    </div>

    {{-- Quick links --}}
    <div class="card">
        <div class="card-title">Quick Navigation</div>
        <div style="display:flex;flex-direction:column;gap:.625rem;">
            @php
                $links = [
                    ['admin.subscription-plans.index', '#a855f7', 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z', 'Subscription Plan Settings', 'Manage plan pricing and Stripe product IDs'],
                    ['admin.payments.escrow', '#6772e5', 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'Escrow Management', 'View held, released and refunded payments'],
                    ['admin.payments.subscriptions', '#00d4ff', 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z', 'Provider Subscriptions', 'Browse all active, trialing and past-due subscriptions'],
                    ['admin.jobs.index', '#ffaa00', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'Job Posts', 'Manage all job postings and their statuses'],
                    ['admin.providers.index', '#00ffaa', 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'Service Providers', 'Manage provider accounts, verification and status'],
                    ['admin.reports.index', '#a855f7', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'Reports & Analytics', 'Platform overview, user and revenue reports'],
                ];
            @endphp
            @foreach($links as [$route, $color, $path, $title, $desc])
            <a href="{{ route($route) }}" style="display:flex;align-items:center;gap:.875rem;padding:.875rem 1rem;background:rgba(255,255,255,.02);border:1px solid var(--border-color);border-radius:10px;text-decoration:none;transition:all .2s;"
                onmouseover="this.style.borderColor='{{ $color }}44';this.style.background='{{ $color }}0a'"
                onmouseout="this.style.borderColor='var(--border-color)';this.style.background='rgba(255,255,255,.02)'">
                <div style="width:36px;height:36px;border-radius:9px;background:{{ $color }}1a;color:{{ $color }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/></svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:.82rem;font-weight:600;color:var(--text-primary);">{{ $title }}</div>
                    <div style="font-size:.72rem;color:var(--text-tertiary);margin-top:.15rem;">{{ $desc }}</div>
                </div>
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--text-tertiary);flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @endforeach
        </div>
    </div>

</div>

@endsection