@extends('provider.layouts.app')
@section('title', 'Manage Bookings')

@push('styles')
<style>
.filter-bar { background:var(--card-bg); border:1px solid var(--border-color); border-radius:14px; padding:1.25rem 1.5rem; margin-bottom:1.5rem; display:flex; gap:1rem; flex-wrap:wrap; align-items:flex-end; }
.filter-group { display:flex; flex-direction:column; gap:.375rem; }
.filter-label { font-size:.75rem; font-weight:600; color:var(--text-tertiary); text-transform:uppercase; letter-spacing:.06em; }
.filter-input, .filter-select { padding:.625rem 1rem; background:var(--input-bg); border:1px solid var(--border-color); border-radius:10px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.875rem; min-width:160px; }
.filter-input:focus, .filter-select:focus { outline:none; border-color:var(--accent-cyan); }
.filter-select option { background:rgba(18,24,39,1); }
.btn { display:inline-flex; align-items:center; gap:.5rem; padding:.625rem 1.25rem; border-radius:10px; font-weight:600; font-size:.875rem; cursor:pointer; border:none; font-family:'Chakra Petch',sans-serif; text-decoration:none; transition:all .3s; }
.btn-primary { background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); color:#000; }
.btn-secondary { background:rgba(0,212,255,.1); color:var(--text-primary); border:1px solid var(--border-color); }
.btn-secondary:hover { border-color:var(--accent-cyan); }
.stats-row { display:flex; gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap; }
.mini-stat { background:var(--card-bg); border:1px solid var(--border-color); border-radius:12px; padding:.875rem 1.25rem; flex:1; min-width:110px; text-align:center; }
.mini-val { font-family:'Orbitron',sans-serif; font-size:1.5rem; font-weight:800; }
.mini-label { font-size:.7rem; color:var(--text-tertiary); text-transform:uppercase; letter-spacing:.06em; margin-top:.25rem; }
.table-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; overflow:hidden; }
table { width:100%; border-collapse:collapse; }
th { text-align:left; padding:1rem 1.25rem; font-size:.7rem; text-transform:uppercase; letter-spacing:.08em; color:var(--text-tertiary); border-bottom:2px solid var(--border-color); font-weight:700; }
td { padding:.875rem 1.25rem; border-bottom:1px solid rgba(0,212,255,.05); vertical-align:middle; }
tr:hover td { background:rgba(0,212,255,.025); }
.booking-num { font-family:'Orbitron',sans-serif; font-size:.75rem; font-weight:700; color:var(--accent-cyan); }
.service-name { font-weight:600; font-size:.875rem; }
.vehicle-info { font-size:.75rem; color:var(--text-tertiary); margin-top:2px; }
.status-pill { padding:.25rem .75rem; border-radius:20px; font-size:.7rem; font-weight:700; text-transform:uppercase; white-space:nowrap; }
.pill-pending    { background:rgba(255,170,0,.15); color:#ffaa00; border:1px solid rgba(255,170,0,.3); }
.pill-confirmed  { background:rgba(0,212,255,.15); color:#00d4ff; border:1px solid rgba(0,212,255,.3); }
.pill-in_progress{ background:rgba(168,85,247,.15); color:#a855f7; border:1px solid rgba(168,85,247,.3); }
.pill-completed  { background:rgba(0,255,170,.15); color:#00ffaa; border:1px solid rgba(0,255,170,.3); }
.pill-cancelled  { background:rgba(255,51,102,.15); color:#ff3366; border:1px solid rgba(255,51,102,.3); }
.action-link { display:inline-flex; align-items:center; gap:.375rem; padding:.4rem .875rem; background:rgba(0,212,255,.08); border:1px solid rgba(0,212,255,.2); border-radius:8px; font-size:.75rem; font-weight:600; color:var(--accent-cyan); text-decoration:none; transition:all .25s; }
.action-link:hover { background:rgba(0,212,255,.18); }
.empty { padding:4rem; text-align:center; color:var(--text-tertiary); }
.empty svg { width:60px; height:60px; margin:0 auto .875rem; display:block; opacity:.3; }
</style>
@endpush

@section('content')

<div class="stats-row">
    <div class="mini-stat"><div class="mini-val" style="color:#ffaa00;">{{ $stats['pending'] }}</div><div class="mini-label">Pending</div></div>
    <div class="mini-stat"><div class="mini-val" style="color:#00d4ff;">{{ $stats['confirmed'] }}</div><div class="mini-label">Confirmed</div></div>
    <div class="mini-stat"><div class="mini-val" style="color:#a855f7;">{{ $stats['in_progress'] }}</div><div class="mini-label">In Progress</div></div>
    <div class="mini-stat"><div class="mini-val" style="color:#00ffaa;">{{ $stats['completed'] }}</div><div class="mini-label">Completed</div></div>
</div>

<div class="filter-bar">
    <form method="GET" style="display:contents;">
        <div class="filter-group">
            <label class="filter-label">Search</label>
            <input type="text" name="search" class="filter-input" placeholder="Booking #, customer, service..." value="{{ request('search') }}">
        </div>
        <div class="filter-group">
            <label class="filter-label">Status</label>
            <select name="status" class="filter-select">
                <option value="">All Status</option>
                @foreach(['pending','confirmed','in_progress','completed','cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">From</label>
            <input type="date" name="date_from" class="filter-input" value="{{ request('date_from') }}">
        </div>
        <div class="filter-group">
            <label class="filter-label">To</label>
            <input type="date" name="date_to" class="filter-input" value="{{ request('date_to') }}">
        </div>
        <div class="filter-group">
            <label class="filter-label">Sort</label>
            <select name="sort" class="filter-select">
                <option value="scheduled_date_desc" {{ request('sort','scheduled_date_desc') === 'scheduled_date_desc' ? 'selected' : '' }}>Newest Date</option>
                <option value="scheduled_date_asc"  {{ request('sort') === 'scheduled_date_asc'  ? 'selected' : '' }}>Oldest Date</option>
                <option value="created_desc"         {{ request('sort') === 'created_desc'         ? 'selected' : '' }}>Recently Created</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if(request()->hasAny(['search','status','date_from','date_to','sort']))
        <a href="{{ route('provider.bookings.index') }}" class="btn btn-secondary">Clear</a>
        @endif
    </form>
    <a href="{{ route('provider.bookings.calendar') }}" class="btn btn-secondary" style="margin-left:auto;">📅 Calendar</a>
</div>

<div class="table-card">
    @if($bookings->isEmpty())
    <div class="empty">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        <p>No bookings match your filters</p>
    </div>
    @else
    <table>
        <thead>
            <tr>
                <th>Booking #</th>
                <th>Service</th>
                <th>Customer</th>
                <th>Scheduled</th>
                <th>Est. Cost</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings as $b)
            <tr>
                <td><div class="booking-num">{{ $b->booking_number }}</div></td>
                <td>
                    <div class="service-name">{{ $b->service_type }}</div>
                    <div class="vehicle-info">{{ $b->vehicle->full_name ?? 'N/A' }}</div>
                </td>
                <td style="font-size:.875rem;">{{ $b->user->name ?? 'Unknown' }}</td>
                <td style="font-size:.875rem;white-space:nowrap;">{{ $b->scheduled_date->format('M d, Y · H:i') }}</td>
                <td style="font-size:.875rem;">{{ $b->estimated_cost ? '$'.number_format($b->estimated_cost, 2) : '—' }}</td>
                <td><span class="status-pill pill-{{ $b->status }}">{{ str_replace('_',' ',$b->status) }}</span></td>
                <td>
                    <a href="{{ route('provider.bookings.show', $b) }}" class="action-link">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Manage
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="padding:1rem 1.5rem;">{{ $bookings->withQueryString()->links() }}</div>
    @endif
</div>

@endsection