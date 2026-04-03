@extends('layouts.app')
@section('title', 'Service History')

@section('content')
<style>
.pg { max-width:1000px; margin:0 auto; padding:2rem 1.5rem; }
.page-title { font-family:'Orbitron',sans-serif; font-size:1.4rem; font-weight:800; margin-bottom:.25rem; }
.page-title span { color:var(--accent-cyan); }
.stats-row { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.75rem; }
.stat-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:14px; padding:1.25rem 1.5rem; }
.stat-num { font-family:'Orbitron',sans-serif; font-size:1.6rem; font-weight:800; color:var(--accent-cyan); }
.stat-lbl { font-size:.72rem; color:var(--text-tertiary); text-transform:uppercase; letter-spacing:.06em; margin-top:.2rem; }
.filter-bar { background:var(--card-bg); border:1px solid var(--border-color); border-radius:12px; padding:1rem 1.25rem; margin-bottom:1.5rem; display:flex; gap:.875rem; flex-wrap:wrap; align-items:center; }
.filter-select { padding:.5rem .875rem; background:var(--input-bg); border:1px solid var(--border-color); border-radius:8px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.825rem; }
.filter-select:focus { outline:none; border-color:var(--accent-cyan); }
.btn-filter { padding:.5rem 1rem; background:rgba(0,212,255,.1); border:1px solid rgba(0,212,255,.25); border-radius:8px; color:var(--accent-cyan); font-size:.8rem; font-weight:600; cursor:pointer; transition:all .25s; }
.btn-filter:hover { background:rgba(0,212,255,.2); }
.record-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:14px; padding:1.5rem; margin-bottom:1rem; transition:border-color .25s; text-decoration:none; display:block; }
.record-card:hover { border-color:rgba(0,212,255,.35); }
.record-top { display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; margin-bottom:.875rem; }
.record-type { font-family:'Orbitron',sans-serif; font-size:.9rem; font-weight:700; color:var(--text-primary); }
.record-date { font-size:.78rem; color:var(--text-tertiary); margin-top:.2rem; }
.record-cost { font-family:'Orbitron',sans-serif; font-size:1.2rem; font-weight:800; color:var(--accent-warning); }
.record-meta { display:flex; gap:.75rem; flex-wrap:wrap; font-size:.78rem; color:var(--text-secondary); }
.meta-pill { display:inline-flex; align-items:center; gap:.3rem; padding:.2rem .625rem; background:rgba(0,212,255,.06); border:1px solid rgba(0,212,255,.12); border-radius:6px; }
.diag-badge { display:inline-flex; align-items:center; gap:.3rem; padding:.2rem .625rem; background:rgba(255,102,0,.08); border:1px solid rgba(255,102,0,.2); border-radius:6px; color:#ff9944; font-size:.72rem; font-weight:700; }
.diag-badge.safety { background:rgba(255,51,102,.1); border-color:rgba(255,51,102,.3); color:#ff8099; }
.empty-state { text-align:center; padding:4rem 2rem; color:var(--text-tertiary); }
.empty-state .icon { font-size:3rem; margin-bottom:1rem; opacity:.4; }
.tab-row { display:flex; gap:.5rem; margin-bottom:1.5rem; }
.tab-link { padding:.5rem 1.25rem; border-radius:20px; border:1px solid var(--border-color); color:var(--text-secondary); font-size:.8rem; font-weight:600; text-decoration:none; transition:all .25s; }
.tab-link:hover { border-color:var(--accent-cyan); color:var(--accent-cyan); }
.tab-link.active { background:rgba(0,212,255,.12); border-color:var(--accent-cyan); color:var(--accent-cyan); }
@media(max-width:640px) { .stats-row { grid-template-columns:1fr 1fr; } }
</style>

<div class="pg">
    <div class="page-title">Service <span>History</span></div>
    <p style="color:var(--text-tertiary);font-size:.825rem;margin-bottom:1.5rem;">Full service records and diagnostic findings from all your vehicles.</p>

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-num">{{ $stats['total_services'] }}</div>
            <div class="stat-lbl">Total Services</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:var(--accent-warning);">${{ number_format($stats['total_spent'], 0) }}</div>
            <div class="stat-lbl">Total Spent</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:{{ $stats['open_diagnostics'] > 0 ? '#ff6600' : 'var(--accent-green)' }};">{{ $stats['open_diagnostics'] }}</div>
            <div class="stat-lbl">Open Findings</div>
        </div>
        <div class="stat-card">
            <div class="stat-num">{{ $stats['this_year'] }}</div>
            <div class="stat-lbl">This Year</div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="tab-row">
        <a href="{{ route('service-history.index') }}" class="tab-link active">📋 Service Records</a>
        <a href="{{ route('service-history.diagnostics') }}" class="tab-link">
            🔍 Diagnostics
            @if($stats['open_diagnostics'] > 0)
            <span style="margin-left:.4rem;background:#ff6600;color:#fff;border-radius:10px;font-size:.62rem;padding:1px 5px;">{{ $stats['open_diagnostics'] }}</span>
            @endif
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('service-history.index') }}" class="filter-bar">
        <select name="vehicle_id" class="filter-select" onchange="this.form.submit()">
            <option value="">All Vehicles</option>
            @foreach($vehicles as $v)
            <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>
                {{ $v->year }} {{ $v->make }} {{ $v->model }}
            </option>
            @endforeach
        </select>
        <select name="service_type" class="filter-select" onchange="this.form.submit()">
            <option value="">All Service Types</option>
            @foreach(['Oil Change','Tire Rotation','Brake Service','Battery Replacement','Full Inspection','Engine Diagnostics','AC Service','Transmission Service','Other'] as $t)
            <option value="{{ $t }}" {{ request('service_type') === $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>
        @if(request()->hasAny(['vehicle_id','service_type']))
        <a href="{{ route('service-history.index') }}" style="font-size:.78rem;color:var(--text-tertiary);text-decoration:none;">✕ Clear</a>
        @endif
    </form>

    {{-- Records --}}
    @forelse($records as $record)
    @php $hasSafety = $record->serviceDiagnostics->where('is_safety_critical', true)->where('status','!=','resolved')->count(); @endphp
    <a href="{{ route('service-history.show', $record) }}" class="record-card">
        <div class="record-top">
            <div>
                <div class="record-type">{{ $record->service_type }}</div>
                <div class="record-date">
                    {{ $record->vehicle->year }} {{ $record->vehicle->make }} {{ $record->vehicle->model }}
                    · {{ $record->service_date?->format('M d, Y') }}
                    @if($record->serviceProvider) · {{ $record->serviceProvider->business_name ?? $record->serviceProvider->name }} @endif
                </div>
            </div>
            <div style="text-align:right;">
                @if($record->cost)
                <div class="record-cost">${{ number_format($record->cost, 2) }}</div>
                @endif
                @if($record->mileage_at_service)
                <div style="font-size:.72rem;color:var(--text-tertiary);margin-top:.2rem;">{{ number_format($record->mileage_at_service) }} mi</div>
                @endif
            </div>
        </div>
        <div class="record-meta">
            @if($record->invoice_number)
            <span class="meta-pill">🧾 #{{ $record->invoice_number }}</span>
            @endif
            @if($record->next_service_date)
            <span class="meta-pill">📅 Next: {{ $record->next_service_date->format('M d, Y') }}</span>
            @endif
            @if($record->serviceDiagnostics->count())
            @if($hasSafety)
            <span class="diag-badge safety">⚠️ Safety Issue</span>
            @else
            <span class="diag-badge">🔍 {{ $record->serviceDiagnostics->count() }} finding{{ $record->serviceDiagnostics->count() > 1 ? 's' : '' }}</span>
            @endif
            @endif
        </div>
        @if($record->description)
        <p style="font-size:.8rem;color:var(--text-tertiary);margin-top:.625rem;line-height:1.5;">{{ Str::limit($record->description, 120) }}</p>
        @endif
    </a>
    @empty
    <div class="empty-state">
        <div class="icon">🔧</div>
        <div style="font-size:.925rem;font-weight:600;margin-bottom:.5rem;">No service records yet</div>
        <div style="font-size:.825rem;">Service records will appear here after a provider completes a job on your vehicle.</div>
    </div>
    @endforelse

    {{-- Pagination --}}
    @if($records->hasPages())
    <div style="margin-top:1.5rem;">{{ $records->links() }}</div>
    @endif
</div>
@endsection