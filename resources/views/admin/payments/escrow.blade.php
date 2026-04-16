@extends('admin.layouts.app')
@section('title','Escrow & Payments')
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
    <div>
        <div class="page-title">Escrow & Payments</div>
        <div class="page-sub">Job payment escrow — held, released, and refunded funds</div>
    </div>
    <a href="{{ route('admin.payments.subscriptions') }}" class="btn btn-secondary">Subscriptions →</a>
</div>

<div class="stats-row">
    <div class="mini-stat" style="--sc:#6772e5"><div class="mini-stat-val" style="color:#6772e5">${{ number_format($stats['held_amount']/100,2) }}</div><div class="mini-stat-lbl">Held ({{ $stats['held_count'] }})</div></div>
    <div class="mini-stat" style="--sc:#00ffaa"><div class="mini-stat-val" style="color:#00ffaa">${{ number_format($stats['released_amount']/100,2) }}</div><div class="mini-stat-lbl">Total Released</div></div>
    <div class="mini-stat" style="--sc:#ff3366"><div class="mini-stat-val" style="color:#ff3366">${{ number_format($stats['refunded_amount']/100,2) }}</div><div class="mini-stat-lbl">Total Refunded</div></div>
    <div class="mini-stat" style="--sc:#00d4ff"><div class="mini-stat-val" style="color:#00d4ff">${{ number_format($stats['platform_fees']/100,2) }}</div><div class="mini-stat-lbl">Platform Fees</div></div>
    @if($stats['overdue_count'] > 0)
    <div class="mini-stat" style="--sc:#ff3366;border-color:rgba(255,51,102,.4);"><div class="mini-stat-val" style="color:#ff3366">{{ $stats['overdue_count'] }}</div><div class="mini-stat-lbl" style="color:#ff8099;">⚠ Overdue</div></div>
    @endif
</div>

<form method="GET" class="filter-bar">
    <div class="filter-group">
        <span class="filter-label">Search</span>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Job #, user…" class="form-input" style="padding:.55rem .875rem;font-size:.8rem;">
    </div>
    <div class="filter-group" style="min-width:120px;">
        <span class="filter-label">Status</span>
        <select name="status" class="form-select" style="padding:.55rem .875rem;font-size:.8rem;">
            <option value="">All</option>
            <option value="held" @selected(request('status')=='held')>Held</option>
            <option value="released" @selected(request('status')=='released')>Released</option>
            <option value="refunded" @selected(request('status')=='refunded')>Refunded</option>
        </select>
    </div>
    <div style="display:flex;align-items:flex-end;gap:.5rem;">
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        @if(request()->hasAny(['search','status']))
        <a href="{{ route('admin.payments.escrow') }}" class="btn btn-secondary btn-sm">Clear</a>
        @endif
    </div>
</form>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Job #</th>
                <th>Service</th>
                <th>Customer</th>
                <th>Provider</th>
                <th>Total</th>
                <th>Platform Fee</th>
                <th>Provider Gets</th>
                <th>Status</th>
                <th>Held At</th>
                <th>Released / Refunded</th>
                <th>Stripe PI</th>
            </tr>
        </thead>
        <tbody>
            @forelse($escrows as $escrow)
            @php $job = $escrow->jobPost; @endphp
            <tr @if($escrow->isOverdue()) style="background:rgba(255,51,102,.04);" @endif>
                <td>
                    @if($job)
                    <a href="{{ route('admin.jobs.show', $job) }}" style="font-family:'Orbitron',sans-serif;font-size:.78rem;color:var(--accent-cyan);text-decoration:none;">{{ $job->job_number }}</a>
                    @else<span style="color:var(--text-tertiary);">—</span>@endif
                    @if($escrow->isOverdue())<div style="font-size:.68rem;color:#ff3366;font-weight:700;margin-top:2px;">⚠ OVERDUE</div>@endif
                </td>
                <td style="font-size:.875rem;">{{ $job?->service_type ?? '—' }}</td>
                <td style="font-size:.875rem;">{{ $job?->user?->name ?? '—' }}</td>
                <td style="font-size:.875rem;">{{ $job?->assignedProvider?->name ?? '—' }}</td>
                <td><span style="font-family:'Orbitron',sans-serif;font-size:.95rem;font-weight:700;color:var(--accent-cyan);">{{ $escrow->formattedAmount() }}</span></td>
                <td style="font-size:.875rem;color:#ffaa00;">${{ number_format($escrow->platform_fee/100,2) }}</td>
                <td style="font-size:.875rem;color:var(--accent-green);">${{ number_format($escrow->providerAmount()/100,2) }}</td>
                <td>
                    @php $clr = match($escrow->status) { 'held'=>'#6772e5','released'=>'#00ffaa','refunded'=>'#ff3366',default=>'#ffaa00' }; @endphp
                    <span style="font-size:.72rem;font-weight:700;color:{{ $clr }};background:{{ $clr }}22;border:1px solid {{ $clr }}44;padding:.25rem .65rem;border-radius:20px;">{{ strtoupper($escrow->status) }}</span>
                </td>
                <td style="font-size:.78rem;color:var(--text-tertiary);">{{ $escrow->held_at?->format('M d, Y') ?? '—' }}</td>
                <td style="font-size:.78rem;color:var(--text-tertiary);">
                    @if($escrow->released_at) {{ $escrow->released_at->format('M d, Y') }}
                    @elseif($escrow->refunded_at) {{ $escrow->refunded_at->format('M d, Y') }}
                    @elseif($escrow->release_at) Due: {{ $escrow->release_at->format('M d') }}
                    @else — @endif
                </td>
                <td><span style="font-family:monospace;font-size:.7rem;color:var(--text-tertiary);" title="{{ $escrow->stripe_payment_intent_id }}">{{ $escrow->stripe_payment_intent_id ? substr($escrow->stripe_payment_intent_id,0,20).'…' : '—' }}</span></td>
            </tr>
            @empty
            <tr><td colspan="11" style="text-align:center;padding:3rem;color:var(--text-tertiary);">No escrow records found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($escrows->hasPages())
<div style="margin-top:1.5rem;display:flex;justify-content:center;">{{ $escrows->links() }}</div>
@endif

@endsection