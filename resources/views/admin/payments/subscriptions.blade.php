@extends('admin.layouts.app')
@section('title','Provider Subscriptions')
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
        <div class="page-title">Provider Subscriptions</div>
        <div class="page-sub">All provider subscription records and billing status</div>
    </div>
    <div style="display:flex;gap:.75rem;">
        <a href="{{ route('admin.payments.escrow') }}" class="btn btn-secondary">Escrow</a>
        <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-primary">Manage Plans</a>
    </div>
</div>

<div class="stats-row">
    <div class="mini-stat" style="--sc:#00ffaa"><div class="mini-stat-val" style="color:#00ffaa">{{ $stats['active'] }}</div><div class="mini-stat-lbl">Active</div></div>
    <div class="mini-stat" style="--sc:#00d4ff"><div class="mini-stat-val" style="color:#00d4ff">{{ $stats['trialing'] }}</div><div class="mini-stat-lbl">Trialing</div></div>
    <div class="mini-stat" style="--sc:#ff3366">
        @if($stats['past_due'] > 0)
        <div class="mini-stat-val" style="color:#ff3366">{{ $stats['past_due'] }}</div><div class="mini-stat-lbl" style="color:#ff8099;">⚠ Past Due</div>
        @else
        <div class="mini-stat-val" style="color:var(--text-tertiary)">0</div><div class="mini-stat-lbl">Past Due</div>
        @endif
    </div>
    <div class="mini-stat" style="--sc:rgba(255,255,255,.2)"><div class="mini-stat-val" style="color:var(--text-tertiary)">{{ $stats['canceled'] }}</div><div class="mini-stat-lbl">Canceled</div></div>
    <div class="mini-stat" style="--sc:#a855f7"><div class="mini-stat-val" style="color:#a855f7">${{ number_format($stats['mrr']/100,0) }}</div><div class="mini-stat-lbl">MRR (est.)</div></div>
</div>

<form method="GET" class="filter-bar">
    <div class="filter-group">
        <span class="filter-label">Search</span>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Provider name…" class="form-input" style="padding:.55rem .875rem;font-size:.8rem;">
    </div>
    <div class="filter-group" style="min-width:120px;">
        <span class="filter-label">Status</span>
        <select name="status" class="form-select" style="padding:.55rem .875rem;font-size:.8rem;">
            <option value="">All</option>
            <option value="active" @selected(request('status')=='active')>Active</option>
            <option value="trialing" @selected(request('status')=='trialing')>Trialing</option>
            <option value="past_due" @selected(request('status')=='past_due')>Past Due</option>
            <option value="canceled" @selected(request('status')=='canceled')>Canceled</option>
        </select>
    </div>
    <div class="filter-group" style="min-width:110px;">
        <span class="filter-label">Plan</span>
        <select name="plan" class="form-select" style="padding:.55rem .875rem;font-size:.8rem;">
            <option value="">All Plans</option>
            <option value="basic" @selected(request('plan')=='basic')>Basic</option>
            <option value="pro" @selected(request('plan')=='pro')>Pro</option>
            <option value="premium" @selected(request('plan')=='premium')>Premium</option>
        </select>
    </div>
    <div style="display:flex;align-items:flex-end;gap:.5rem;">
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        @if(request()->hasAny(['search','status','plan']))
        <a href="{{ route('admin.payments.subscriptions') }}" class="btn btn-secondary btn-sm">Clear</a>
        @endif
    </div>
</form>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Provider</th>
                <th>Plan</th>
                <th>Billing</th>
                <th>Status</th>
                <th>Bids Used</th>
                <th>Period</th>
                <th>Trial Ends</th>
                <th>Canceled At</th>
                <th>Stripe Sub ID</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subscriptions as $sub)
            <tr @if($sub->isPastDue()) style="background:rgba(255,51,102,.04);" @endif>
                <td>
                    <div style="font-weight:600;font-size:.875rem;">{{ $sub->provider?->name ?? '—' }}</div>
                    <div style="font-size:.72rem;color:var(--text-tertiary);">{{ $sub->provider?->email ?? '' }}</div>
                </td>
                <td>
                    @if($sub->plan)
                    @php $pc = match($sub->plan->slug) { 'premium'=>'#a855f7','pro'=>'#00d4ff',default=>'var(--text-tertiary)' }; @endphp
                    <span style="font-family:'Orbitron',sans-serif;font-size:.78rem;font-weight:700;color:{{ $pc }};">{{ strtoupper($sub->plan->name) }}</span>
                    @else<span style="color:var(--text-tertiary);">—</span>@endif
                </td>
                <td>
                    <span style="font-size:.78rem;font-weight:600;color:var(--text-secondary);text-transform:capitalize;">{{ $sub->billing_interval ?? '—' }}</span>
                    @if($sub->plan && $sub->billing_interval)
                    <div style="font-size:.7rem;color:var(--text-tertiary);">
                        {{ $sub->billing_interval === 'yearly' ? '$'.number_format($sub->plan->price_yearly/100,2).'/yr' : '$'.number_format($sub->plan->price_monthly/100,2).'/mo' }}
                    </div>
                    @endif
                </td>
                <td>
                    @php $sc = match($sub->status) { 'active'=>['#00ffaa','rgba(0,255,170,.15)'],'trialing'=>['#00d4ff','rgba(0,212,255,.15)'],'past_due'=>['#ff3366','rgba(255,51,102,.15)'],'canceled'=>['rgba(255,255,255,.4)','rgba(255,255,255,.06)'],default=>['#ffaa00','rgba(255,170,0,.15)'] }; @endphp
                    <span style="font-size:.72rem;font-weight:700;color:{{ $sc[0] }};background:{{ $sc[1] }};padding:.25rem .65rem;border-radius:20px;border:1px solid {{ $sc[0] }}44;">{{ strtoupper($sub->status) }}</span>
                </td>
                <td style="font-size:.875rem;">
                    @if($sub->plan && !$sub->plan->hasUnlimitedBids())
                        {{ $sub->bids_used_this_month }} / {{ $sub->plan->job_bids_per_month }}
                    @else<span style="color:var(--text-tertiary);">∞</span>@endif
                </td>
                <td style="font-size:.78rem;color:var(--text-tertiary);">
                    @if($sub->current_period_start && $sub->current_period_end)
                        {{ $sub->current_period_start->format('M d') }} – {{ $sub->current_period_end->format('M d, Y') }}
                    @else—@endif
                </td>
                <td style="font-size:.78rem;color:{{ $sub->isTrialing()?'#00d4ff':'var(--text-tertiary)' }};">{{ $sub->trial_ends_at?->format('M d, Y') ?? '—' }}</td>
                <td style="font-size:.78rem;color:#ff8099;">{{ $sub->canceled_at?->format('M d, Y') ?? '—' }}</td>
                <td><span style="font-family:monospace;font-size:.7rem;color:var(--text-tertiary);" title="{{ $sub->stripe_subscription_id }}">{{ $sub->stripe_subscription_id ? substr($sub->stripe_subscription_id,0,20).'…' : '—' }}</span></td>
                <td>
                    @if($sub->provider)
                    <a href="{{ route('admin.providers.show', $sub->provider) }}" class="act act-view">Provider</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="10" style="text-align:center;padding:3rem;color:var(--text-tertiary);">No subscriptions found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($subscriptions->hasPages())
<div style="margin-top:1.5rem;display:flex;justify-content:center;">{{ $subscriptions->links() }}</div>
@endif

@endsection