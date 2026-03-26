@extends('layouts.app')
@section('title', 'Trip Log')
@section('content')
<style>
:root[data-theme="dark"] {
    --page-bg:#0a0e1a; --card-bg:rgba(26,32,48,0.85); --border-color:rgba(0,212,255,0.1);
    --text-primary:#fff; --text-secondary:rgba(255,255,255,0.7); --text-tertiary:rgba(255,255,255,0.45);
    --accent-cyan:#00d4ff; --accent-green:#00ffaa; --accent-warning:#ffaa00; --accent-danger:#ff3366;
    --input-bg:rgba(0,212,255,0.05);
}
:root[data-theme="light"] {
    --page-bg:#f8fafc; --card-bg:rgba(255,255,255,0.95); --border-color:rgba(0,0,0,0.1);
    --text-primary:#1a1f36; --text-secondary:rgba(26,31,54,0.7); --text-tertiary:rgba(26,31,54,0.45);
    --accent-cyan:#0066ff; --accent-green:#00cc88; --accent-warning:#ff9500; --accent-danger:#ff3366;
    --input-bg:rgba(0,0,0,0.03);
}
.pg { max-width:1400px; margin:0 auto; padding:2rem 1.5rem; }
.page-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:2rem; flex-wrap:wrap; gap:1rem; }
.page-title h1 { font-family:'Orbitron',sans-serif; font-size:2.25rem; font-weight:800;
    background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green));
    -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; margin:0 0 .25rem; }
.page-title p { color:var(--text-tertiary); font-size:.875rem; margin:0; }
.header-actions { display:flex; gap:.75rem; flex-wrap:wrap; align-items:center; }
.btn { padding:.75rem 1.25rem; border-radius:10px; font-size:.875rem; font-weight:600;
    text-decoration:none; display:inline-flex; align-items:center; gap:.5rem; transition:all .3s; cursor:pointer; border:none; }
.btn-primary { background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); color:#000;
    box-shadow:0 4px 15px rgba(0,212,255,.3); }
.btn-primary:hover { transform:translateY(-2px); box-shadow:0 6px 25px rgba(0,212,255,.5); color:#000; }
.btn-secondary { background:var(--input-bg); border:1px solid var(--border-color); color:var(--text-secondary); }
.btn-secondary:hover { border-color:var(--accent-cyan); color:var(--text-primary); }
.btn-danger-sm { background:rgba(255,51,102,.1); border:1px solid rgba(255,51,102,.25); color:var(--accent-danger);
    padding:.4rem .75rem; font-size:.75rem; border-radius:8px; }
.btn-danger-sm:hover { background:rgba(255,51,102,.2); }

/* Stats */
.stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:1rem; margin-bottom:2rem; }
.stat-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px;
    padding:1.25rem 1.5rem; position:relative; overflow:hidden; }
.stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; }
.stat-card.cyan::before  { background:linear-gradient(90deg,var(--accent-cyan),var(--accent-green)); }
.stat-card.green::before { background:linear-gradient(90deg,var(--accent-green),var(--accent-cyan)); }
.stat-card.warn::before  { background:linear-gradient(90deg,var(--accent-warning),#ff6b35); }
.stat-card.blue::before  { background:linear-gradient(90deg,#6366f1,var(--accent-cyan)); }
.stat-card.month::before { background:linear-gradient(90deg,var(--accent-danger),var(--accent-warning)); }
.stat-icon { font-size:1.75rem; margin-bottom:.5rem; }
.stat-value { font-family:'Orbitron',sans-serif; font-size:1.5rem; font-weight:800;
    color:var(--text-primary); line-height:1; margin-bottom:.25rem; }
.stat-label { font-size:.75rem; color:var(--text-tertiary); text-transform:uppercase; letter-spacing:.05em; }

/* Filter */
.filter-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:14px;
    padding:1.25rem 1.5rem; margin-bottom:1.5rem; }
.filter-row { display:flex; gap:.75rem; flex-wrap:wrap; align-items:flex-end; }
.filter-group { display:flex; flex-direction:column; gap:.35rem; min-width:140px; flex:1; }
.filter-label { font-size:.72rem; font-weight:600; color:var(--text-tertiary); text-transform:uppercase; letter-spacing:.06em; }
.filter-input, .filter-select { padding:.6rem .9rem; background:var(--input-bg); border:1px solid var(--border-color);
    border-radius:8px; color:var(--text-primary); font-size:.825rem; font-family:'Chakra Petch',sans-serif;
    transition:all .3s; }
.filter-input:focus, .filter-select:focus { outline:none; border-color:var(--accent-cyan); }
.filter-select option { background:#121827; }

/* Table */
.table-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; overflow:hidden; }
.table-header { display:flex; justify-content:space-between; align-items:center;
    padding:1.25rem 1.5rem; border-bottom:1px solid var(--border-color); }
.table-title { font-family:'Orbitron',sans-serif; font-size:.9rem; font-weight:700; }
.table-count { font-size:.8rem; color:var(--text-tertiary); }
table { width:100%; border-collapse:collapse; }
th { padding:.875rem 1.25rem; text-align:left; font-size:.72rem; font-weight:700; color:var(--text-tertiary);
    text-transform:uppercase; letter-spacing:.06em; border-bottom:1px solid var(--border-color); white-space:nowrap; }
td { padding:.875rem 1.25rem; border-bottom:1px solid var(--border-color); font-size:.875rem; color:var(--text-secondary); vertical-align:middle; }
tr:last-child td { border-bottom:none; }
tr:hover td { background:rgba(0,212,255,.03); }
.purpose-pill { display:inline-flex; align-items:center; gap:.35rem; padding:.3rem .75rem;
    border-radius:20px; font-size:.72rem; font-weight:700; }
.purpose-business { background:rgba(0,212,255,.12); color:var(--accent-cyan); border:1px solid rgba(0,212,255,.2); }
.purpose-personal { background:rgba(0,255,170,.12); color:var(--accent-green); border:1px solid rgba(0,255,170,.2); }
.purpose-commute  { background:rgba(255,170,0,.12); color:var(--accent-warning); border:1px solid rgba(255,170,0,.2); }
.route-cell { max-width:220px; }
.route-from { font-size:.75rem; color:var(--text-tertiary); }
.route-to   { font-size:.875rem; color:var(--text-primary); font-weight:600; }
.distance-val { font-family:'Orbitron',sans-serif; font-size:.95rem; font-weight:700; color:var(--text-primary); }
.distance-unit { font-size:.7rem; color:var(--text-tertiary); }
.vehicle-chip { display:inline-flex; align-items:center; gap:.4rem; padding:.25rem .65rem;
    background:rgba(0,212,255,.07); border:1px solid var(--border-color); border-radius:8px; font-size:.75rem; }
.action-row { display:flex; gap:.5rem; align-items:center; }
.btn-edit-sm { background:var(--input-bg); border:1px solid var(--border-color); color:var(--text-secondary);
    padding:.4rem .75rem; font-size:.75rem; border-radius:8px; text-decoration:none; transition:all .3s; display:inline-flex; align-items:center; gap:.3rem; }
.btn-edit-sm:hover { border-color:var(--accent-cyan); color:var(--accent-cyan); }
.empty-state { padding:4rem 2rem; text-align:center; }
.empty-icon { font-size:3rem; margin-bottom:1rem; opacity:.4; }
.empty-text { color:var(--text-tertiary); font-size:.875rem; }
.pagination-wrap { padding:1rem 1.5rem; border-top:1px solid var(--border-color);
    display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; }
.pagination-info { font-size:.8rem; color:var(--text-tertiary); }
.notes-cell { max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
    font-size:.78rem; color:var(--text-tertiary); font-style:italic; }
@media(max-width:768px){
    .stats-grid { grid-template-columns:1fr 1fr; }
    .filter-row { flex-direction:column; }
    .table-card { overflow-x:auto; }
}
</style>

<div class="pg">
    {{-- Header --}}
    <div class="page-header">
        <div class="page-title">
            <h1>Trip Log</h1>
            <p>Track every mile — business, personal, and commute</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('trips.export', request()->query()) }}" class="btn btn-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export
            </a>
            <a href="{{ route('trips.create') }}" class="btn btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Log Trip
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card cyan">
            <div class="stat-icon">🛣️</div>
            <div class="stat-value">{{ number_format($stats['total_miles']) }}</div>
            <div class="stat-label">Total Miles</div>
        </div>
        <div class="stat-card green">
            <div class="stat-icon">📋</div>
            <div class="stat-value">{{ number_format($stats['total_trips']) }}</div>
            <div class="stat-label">Total Trips</div>
        </div>
        <div class="stat-card blue">
            <div class="stat-icon">💼</div>
            <div class="stat-value">{{ number_format($stats['business_miles']) }}</div>
            <div class="stat-label">Business Miles</div>
        </div>
        <div class="stat-card green">
            <div class="stat-icon">🏠</div>
            <div class="stat-value">{{ number_format($stats['personal_miles']) }}</div>
            <div class="stat-label">Personal Miles</div>
        </div>
        <div class="stat-card warn">
            <div class="stat-icon">🚗</div>
            <div class="stat-value">{{ number_format($stats['commute_miles']) }}</div>
            <div class="stat-label">Commute Miles</div>
        </div>
        <div class="stat-card month">
            <div class="stat-icon">📅</div>
            <div class="stat-value">{{ number_format($stats['this_month']) }}</div>
            <div class="stat-label">This Month</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('trips.index') }}">
            <div class="filter-row">
                <div class="filter-group">
                    <label class="filter-label">Vehicle</label>
                    <select name="vehicle_id" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Vehicles</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>
                                {{ $v->year }} {{ $v->make }} {{ $v->model }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Purpose</label>
                    <select name="purpose" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Purposes</option>
                        <option value="business" {{ request('purpose') === 'business' ? 'selected' : '' }}>💼 Business</option>
                        <option value="personal" {{ request('purpose') === 'personal' ? 'selected' : '' }}>🏠 Personal</option>
                        <option value="commute"  {{ request('purpose') === 'commute'  ? 'selected' : '' }}>🚗 Commute</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">From</label>
                    <input type="date" name="start_date" class="filter-input" value="{{ request('start_date') }}" onchange="this.form.submit()">
                </div>
                <div class="filter-group">
                    <label class="filter-label">To</label>
                    <input type="date" name="end_date" class="filter-input" value="{{ request('end_date') }}" onchange="this.form.submit()">
                </div>
                @if(request()->hasAny(['vehicle_id','purpose','start_date','end_date']))
                <div class="filter-group" style="justify-content:flex-end;">
                    <a href="{{ route('trips.index') }}" class="btn btn-secondary" style="height:fit-content;">✕ Clear</a>
                </div>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-card">
        <div class="table-header">
            <span class="table-title">Trip History</span>
            <span class="table-count">{{ $trips->total() }} trips · {{ number_format($trips->sum('distance')) }} mi shown</span>
        </div>

        @if($trips->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">🗺️</div>
            <div class="route-to" style="margin-bottom:.5rem;">No trips found</div>
            <div class="empty-text">
                @if(request()->hasAny(['vehicle_id','purpose','start_date','end_date']))
                    No trips match your filters. <a href="{{ route('trips.index') }}" style="color:var(--accent-cyan);">Clear filters</a>
                @else
                    Start tracking your miles. <a href="{{ route('trips.create') }}" style="color:var(--accent-cyan);">Log your first trip →</a>
                @endif
            </div>
        </div>
        @else
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Route</th>
                        <th>Vehicle</th>
                        <th>Purpose</th>
                        <th>Distance</th>
                        <th>Odometer</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trips as $trip)
                    <tr>
                        <td style="white-space:nowrap;">
                            <div style="font-weight:600;color:var(--text-primary);">{{ $trip->trip_date->format('M d, Y') }}</div>
                            <div style="font-size:.72rem;color:var(--text-tertiary);">{{ $trip->trip_date->format('l') }}</div>
                        </td>
                        <td class="route-cell">
                            @if($trip->start_location)
                            <div class="route-from">📍 {{ $trip->start_location }}</div>
                            @endif
                            <div class="route-to">🏁 {{ $trip->destination }}</div>
                        </td>
                        <td>
                            <span class="vehicle-chip">
                                🚗 {{ $trip->vehicle->year }} {{ $trip->vehicle->make }} {{ $trip->vehicle->model }}
                            </span>
                        </td>
                        <td>
                            <span class="purpose-pill purpose-{{ $trip->purpose }}">
                                {{ $trip->purpose_icon }} {{ $trip->formatted_purpose }}
                            </span>
                        </td>
                        <td>
                            <span class="distance-val">{{ number_format($trip->distance) }}</span>
                            <span class="distance-unit"> mi</span>
                        </td>
                        <td style="font-size:.78rem;color:var(--text-tertiary);">
                            {{ number_format($trip->start_odometer) }} → {{ number_format($trip->end_odometer) }}
                        </td>
                        <td><div class="notes-cell" title="{{ $trip->notes }}">{{ $trip->notes ?: '—' }}</div></td>
                        <td>
                            <div class="action-row">
                                <a href="{{ route('trips.edit', $trip) }}" class="btn-edit-sm">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('trips.destroy', $trip) }}" onsubmit="return confirm('Delete this trip?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger-sm">✕</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($trips->hasPages())
        <div class="pagination-wrap">
            <span class="pagination-info">
                Showing {{ $trips->firstItem() }}–{{ $trips->lastItem() }} of {{ $trips->total() }}
            </span>
            {{ $trips->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection