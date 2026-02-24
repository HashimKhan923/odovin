@extends('provider.layouts.app')
@section('title', 'Analytics')

@push('styles')
<style>
.kpi-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1.25rem; margin-bottom:2rem; }
.kpi { background:var(--card-bg); border:1px solid var(--border-color); border-radius:14px; padding:1.5rem; }
.kpi-val { font-family:'Orbitron',sans-serif; font-size:1.875rem; font-weight:800; margin-bottom:.25rem; }
.kpi-label { font-size:.75rem; color:var(--text-tertiary); text-transform:uppercase; letter-spacing:.06em; }
.card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.75rem; margin-bottom:1.5rem; }
.card-title { font-family:'Orbitron',sans-serif; font-size:1rem; font-weight:700; margin-bottom:1.25rem; }
.rev-chart { display:flex; align-items:flex-end; gap:.375rem; height:160px; }
.rev-col { flex:1; display:flex; flex-direction:column; align-items:center; gap:.375rem; height:100%; }
.rev-track { flex:1; display:flex; align-items:flex-end; width:100%; }
.rev-bar { width:100%; border-radius:4px 4px 0 0; background:linear-gradient(180deg,var(--accent-cyan),rgba(0,212,255,.3)); min-height:4px; }
.rev-lbl { font-size:.6rem; color:var(--text-tertiary); text-align:center; }
.two-col { display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; }
.svc-row { display:flex; align-items:center; gap:.875rem; padding:.75rem 0; border-bottom:1px solid rgba(0,212,255,.05); }
.svc-row:last-child { border-bottom:none; }
.svc-name { flex:1; font-size:.875rem; }
.svc-bar-wrap { width:100px; }
.svc-track { height:6px; background:rgba(255,255,255,.06); border-radius:3px; overflow:hidden; }
.svc-fill { height:100%; background:linear-gradient(90deg,var(--accent-cyan),var(--accent-green)); border-radius:3px; }
.svc-cnt { font-size:.8rem; font-weight:700; color:var(--accent-cyan); width:30px; text-align:right; flex-shrink:0; }
.rat-row { display:flex; align-items:center; gap:.75rem; margin-bottom:.625rem; }
.rat-lbl { width:28px; font-size:.8rem; font-weight:700; color:#ffaa00; text-align:right; flex-shrink:0; }
.rat-track { flex:1; height:8px; background:rgba(255,255,255,.06); border-radius:4px; overflow:hidden; }
.rat-fill { height:100%; background:#ffaa00; border-radius:4px; }
.rat-cnt { width:30px; font-size:.75rem; color:var(--text-tertiary); text-align:right; flex-shrink:0; }
table { width:100%; border-collapse:collapse; }
th { text-align:left; padding:.75rem 1rem; font-size:.7rem; text-transform:uppercase; letter-spacing:.08em; color:var(--text-tertiary); border-bottom:1px solid var(--border-color); font-weight:700; }
td { padding:.75rem 1rem; border-bottom:1px solid rgba(0,212,255,.04); font-size:.875rem; }
@media(max-width:768px) { .two-col { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')

@php
$totalRevenue  = array_sum(array_column($months, 'revenue'));
$totalBookings = array_sum(array_column($months, 'count'));
$maxRevenue    = max(array_column($months, 'revenue') ?: [1]);
$totalRated    = array_sum($ratingDist);
@endphp

<div class="kpi-grid">
    <div class="kpi">
        <div class="kpi-val" style="color:#a855f7;">${{ number_format($totalRevenue) }}</div>
        <div class="kpi-label">Total Revenue (12mo)</div>
    </div>
    <div class="kpi">
        <div class="kpi-val" style="color:#00d4ff;">{{ $totalBookings }}</div>
        <div class="kpi-label">Total Bookings (12mo)</div>
    </div>
    <div class="kpi">
        <div class="kpi-val" style="color:#ff3366;">{{ $cancelRate }}%</div>
        <div class="kpi-label">Cancellation Rate</div>
    </div>
    <div class="kpi">
        <div class="kpi-val" style="color:#ffaa00;">{{ number_format($provider->rating, 1) }} ★</div>
        <div class="kpi-label">Overall Rating</div>
    </div>
</div>

<div class="card">
    <div class="card-title">Revenue — Last 12 Months</div>
    <div class="rev-chart">
        @foreach($months as $m)
        <div class="rev-col">
            <div class="rev-track">
                <div class="rev-bar" style="height:{{ $maxRevenue > 0 ? max(4, ($m['revenue']/$maxRevenue)*100) : 4 }}%;" title="{{ $m['label'] }}: ${{ number_format($m['revenue']) }}"></div>
            </div>
            <div class="rev-lbl">{{ substr($m['label'], 0, 3) }}</div>
        </div>
        @endforeach
    </div>
    <div style="display:flex;justify-content:space-between;margin-top:1rem;font-size:.8rem;color:var(--text-tertiary);">
        <span>Monthly average: ${{ $totalBookings > 0 ? number_format($totalRevenue / 12) : 0 }}</span>
        <span>Best month: ${{ number_format(max(array_column($months, 'revenue'))) }}</span>
    </div>
</div>

<div class="two-col">
    <div class="card">
        <div class="card-title">Service Popularity</div>
        @php $maxSvc = $services->max('count') ?: 1; @endphp
        @forelse($services as $svc)
        <div class="svc-row">
            <div class="svc-name">{{ $svc->service_type }}</div>
            <div class="svc-bar-wrap">
                <div class="svc-track">
                    <div class="svc-fill" style="width:{{ max(4, ($svc->count/$maxSvc)*100) }}%;"></div>
                </div>
            </div>
            <div class="svc-cnt">{{ $svc->count }}</div>
        </div>
        @empty
        <p style="color:var(--text-tertiary);font-size:.875rem;text-align:center;padding:2rem 0;">No bookings yet</p>
        @endforelse
    </div>

    <div class="card">
        <div class="card-title">Rating Distribution</div>
        @foreach([5,4,3,2,1] as $star)
        <div class="rat-row">
            <div class="rat-lbl">{{ $star }}★</div>
            <div class="rat-track">
                <div class="rat-fill" style="width:{{ $totalRated > 0 ? max(0, ($ratingDist[$star] ?? 0)/$totalRated*100) : 0 }}%;"></div>
            </div>
            <div class="rat-cnt">{{ $ratingDist[$star] ?? 0 }}</div>
        </div>
        @endforeach
        <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border-color);display:flex;justify-content:space-between;font-size:.8rem;color:var(--text-tertiary);">
            <span>{{ $totalRated }} total reviews</span>
            <span style="color:#ffaa00;font-weight:700;">{{ number_format($provider->rating, 1) }}/5.0</span>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-title">Average Cost per Service</div>
    <table>
        <thead>
            <tr>
                <th>Service</th>
                <th style="text-align:right;">Bookings</th>
                <th style="text-align:right;">Avg Cost</th>
            </tr>
        </thead>
        <tbody>
            @foreach($services as $svc)
            <tr>
                <td>{{ $svc->service_type }}</td>
                <td style="text-align:right;color:var(--accent-cyan);font-weight:600;">{{ $svc->count }}</td>
                <td style="text-align:right;font-weight:600;">{{ $svc->avg_cost ? '$'.number_format($svc->avg_cost, 2) : '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection