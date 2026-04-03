@extends('layouts.app')
@section('title', 'Vehicle Diagnostics')

@section('content')
<style>
.pg { max-width:1000px; margin:0 auto; padding:2rem 1.5rem; }
.page-title { font-family:'Orbitron',sans-serif; font-size:1.4rem; font-weight:800; margin-bottom:.25rem; }
.page-title span { color:var(--accent-cyan); }
.stats-row { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.75rem; }
.stat-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:14px; padding:1.25rem 1.5rem; }
.stat-num { font-family:'Orbitron',sans-serif; font-size:1.6rem; font-weight:800; }
.stat-lbl { font-size:.72rem; color:var(--text-tertiary); text-transform:uppercase; letter-spacing:.06em; margin-top:.2rem; }
.tab-row { display:flex; gap:.5rem; margin-bottom:1.5rem; }
.tab-link { padding:.5rem 1.25rem; border-radius:20px; border:1px solid var(--border-color); color:var(--text-secondary); font-size:.8rem; font-weight:600; text-decoration:none; transition:all .25s; }
.tab-link:hover { border-color:var(--accent-cyan); color:var(--accent-cyan); }
.tab-link.active { background:rgba(0,212,255,.12); border-color:var(--accent-cyan); color:var(--accent-cyan); }
.filter-bar { background:var(--card-bg); border:1px solid var(--border-color); border-radius:12px; padding:1rem 1.25rem; margin-bottom:1.5rem; display:flex; gap:.875rem; flex-wrap:wrap; align-items:center; }
.filter-pill { padding:.375rem .875rem; border-radius:20px; border:1px solid var(--border-color); color:var(--text-secondary); font-size:.78rem; font-weight:600; text-decoration:none; transition:all .25s; }
.filter-pill:hover { border-color:var(--accent-cyan); color:var(--accent-cyan); }
.filter-pill.active { background:rgba(0,212,255,.12); border-color:var(--accent-cyan); color:var(--accent-cyan); }
.filter-select { padding:.5rem .875rem; background:var(--input-bg); border:1px solid var(--border-color); border-radius:8px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.825rem; }
.diag-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:14px; padding:1.5rem; margin-bottom:1rem; transition:border-color .25s; }
.diag-card.safety-critical { border-color:rgba(255,51,102,.4); }
.badge { display:inline-flex; align-items:center; font-size:.7rem; font-weight:700; padding:.25rem .625rem; border-radius:6px; white-space:nowrap; }
.empty-state { text-align:center; padding:4rem 2rem; color:var(--text-tertiary); }
@media(max-width:640px) { .stats-row { grid-template-columns:1fr 1fr; } }
</style>

<div class="pg">
    <div class="page-title">Vehicle <span>Diagnostics</span></div>
    <p style="color:var(--text-tertiary);font-size:.825rem;margin-bottom:1.5rem;">Issues found by service providers during your vehicle servicing.</p>

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-num" style="color:#ff6600;">{{ $stats['open'] }}</div>
            <div class="stat-lbl">Open Issues</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:#ff3366;">{{ $stats['critical'] }}</div>
            <div class="stat-lbl">Critical</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:#ff8099;">{{ $stats['safety'] }}</div>
            <div class="stat-lbl">Safety Issues</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:var(--accent-green);">{{ $stats['resolved'] }}</div>
            <div class="stat-lbl">Resolved</div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="tab-row">
        <a href="{{ route('service-history.index') }}" class="tab-link">📋 Service Records</a>
        <a href="{{ route('service-history.diagnostics') }}" class="tab-link active">
            🔍 Diagnostics
            @if($stats['open'] > 0)
            <span style="margin-left:.4rem;background:#ff6600;color:#fff;border-radius:10px;font-size:.62rem;padding:1px 5px;">{{ $stats['open'] }}</span>
            @endif
        </a>
    </div>

    {{-- Filters --}}
    <div class="filter-bar">
        <a href="{{ route('service-history.diagnostics') }}" class="filter-pill {{ !request()->hasAny(['status','severity']) ? 'active' : '' }}">All</a>
        <a href="{{ route('service-history.diagnostics', ['status'=>'open']) }}" class="filter-pill {{ request('status')==='open' ? 'active' : '' }}">🔴 Open</a>
        <a href="{{ route('service-history.diagnostics', ['status'=>'in_progress']) }}" class="filter-pill {{ request('status')==='in_progress' ? 'active' : '' }}">🔵 In Progress</a>
        <a href="{{ route('service-history.diagnostics', ['status'=>'resolved']) }}" class="filter-pill {{ request('status')==='resolved' ? 'active' : '' }}">✅ Resolved</a>
        <a href="{{ route('service-history.diagnostics', ['severity'=>'critical']) }}" class="filter-pill {{ request('severity')==='critical' ? 'active' : '' }}">🔴 Critical</a>
        @if($vehicles->count() > 1)
        <form method="GET" style="margin:0;">
            @foreach(request()->except('vehicle_id') as $k => $v)
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endforeach
            <select name="vehicle_id" class="filter-select" onchange="this.form.submit()">
                <option value="">All Vehicles</option>
                @foreach($vehicles as $v)
                <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->year }} {{ $v->make }} {{ $v->model }}</option>
                @endforeach
            </select>
        </form>
        @endif
    </div>

    {{-- Diagnostics list --}}
    @forelse($diagnostics as $diag)
    @php
        $sevColor = ['low'=>'#00ffaa','medium'=>'#ffaa00','high'=>'#ff6600','critical'=>'#ff3366'][$diag->severity] ?? '#888';
        $sevBg    = ['low'=>'rgba(0,255,170,.1)','medium'=>'rgba(255,170,0,.1)','high'=>'rgba(255,102,0,.1)','critical'=>'rgba(255,51,102,.1)'][$diag->severity] ?? 'rgba(136,136,136,.1)';
        $stColor  = ['open'=>'#ff3366','acknowledged'=>'#ffaa00','in_progress'=>'#00d4ff','monitoring'=>'#aa88ff','resolved'=>'#00ffaa','ignored'=>'#888'][$diag->status] ?? '#888';
    @endphp
    <div class="diag-card {{ $diag->is_safety_critical ? 'safety-critical' : '' }}">
        @if($diag->is_safety_critical)
        <div style="font-size:.7rem;font-weight:700;color:#ff3366;margin-bottom:.625rem;">⚠️ SAFETY CRITICAL</div>
        @endif
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;margin-bottom:.75rem;">
            <div>
                <div style="font-weight:700;font-size:.925rem;color:var(--text-primary);margin-bottom:.2rem;">
                    {{ $diag->category_icon ?? '' }} {{ $diag->title }}
                </div>
                <div style="font-size:.78rem;color:var(--text-tertiary);">
                    {{ $diag->vehicle->year }} {{ $diag->vehicle->make }} {{ $diag->vehicle->model }}
                    · {{ ucfirst(str_replace('_', ' ', $diag->category)) }}
                    @if($diag->location) · {{ $diag->location }}@endif
                    · Found {{ $diag->created_at->format('M d, Y') }}
                    @if($diag->serviceProvider) · by {{ $diag->serviceProvider->business_name ?? $diag->serviceProvider->name }}@endif
                </div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.35rem;flex-shrink:0;">
                <span class="badge" style="background:{{ $sevBg }};color:{{ $sevColor }};border:1px solid {{ $sevColor . '40' }};">{{ ucfirst($diag->severity) }}</span>
                <span class="badge" style="background:{{ $stColor . '18' }};color:{{ $stColor }};border:1px solid {{ $stColor . '40' }};">{{ $diag->status_label ?? ucfirst(str_replace('_',' ',$diag->status)) }}</span>
            </div>
        </div>
        <p style="font-size:.825rem;color:var(--text-secondary);line-height:1.65;margin-bottom:.625rem;">{{ $diag->description }}</p>
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;">
            @if($diag->estimated_cost_min || $diag->estimated_cost_max)
            <span style="font-size:.78rem;color:var(--text-tertiary);">
                💰 Est. repair:
                <strong style="color:var(--text-primary);">
                    @if($diag->estimated_cost_min && $diag->estimated_cost_max)
                        ${{ number_format($diag->estimated_cost_min) }} – ${{ number_format($diag->estimated_cost_max) }}
                    @elseif($diag->estimated_cost_min)
                        From ${{ number_format($diag->estimated_cost_min) }}
                    @else
                        Up to ${{ number_format($diag->estimated_cost_max) }}
                    @endif
                </strong>
            </span>
            @endif
            @if($diag->serviceRecord)
            <a href="{{ route('service-history.show', $diag->serviceRecord) }}" style="font-size:.78rem;color:var(--accent-cyan);text-decoration:none;">View Service Record →</a>
            @endif
        </div>
        @if($diag->status === 'resolved' && $diag->resolution_notes)
        <div style="margin-top:.75rem;padding:.625rem .875rem;background:rgba(0,255,170,.05);border:1px solid rgba(0,255,170,.15);border-radius:8px;font-size:.8rem;color:var(--text-secondary);">
            <strong style="color:var(--accent-green);">✓ Resolved:</strong> {{ $diag->resolution_notes }}
        </div>
        @endif
    </div>
    @empty
    <div class="empty-state">
        <div style="font-size:3rem;margin-bottom:1rem;opacity:.4;">🔍</div>
        <div style="font-size:.925rem;font-weight:600;margin-bottom:.5rem;">No diagnostic findings</div>
        <div style="font-size:.825rem;">When providers flag issues during your service, they'll appear here.</div>
    </div>
    @endforelse

    @if($diagnostics->hasPages())
    <div style="margin-top:1.5rem;">{{ $diagnostics->links() }}</div>
    @endif
</div>
@endsection