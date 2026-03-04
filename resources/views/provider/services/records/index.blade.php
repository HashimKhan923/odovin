@extends('provider.layouts.app')
@section('title', 'Service Records')

@section('content')
<style>
.pg { padding:2rem; }
.page-header { display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem; margin-bottom:2rem; }
.page-title { font-family:'Orbitron',sans-serif; font-size:1.5rem; font-weight:800; }
.page-title span { color:var(--accent-cyan); }
.btn-new { display:inline-flex; align-items:center; gap:.5rem; padding:.75rem 1.5rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:10px; color:#000; font-family:'Orbitron',sans-serif; font-weight:700; font-size:.8rem; text-decoration:none; cursor:pointer; transition:all .3s; }
.btn-new:hover { transform:translateY(-1px); box-shadow:0 4px 15px rgba(0,212,255,.4); }
.stats-row { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1.5rem; }
.stat-box { background:var(--card-bg); border:1px solid var(--border-color); border-radius:14px; padding:1.25rem 1.5rem; text-align:center; }
.stat-num { font-family:'Orbitron',sans-serif; font-size:1.75rem; font-weight:800; }
.stat-lbl { font-size:.75rem; color:var(--text-tertiary); margin-top:.25rem; text-transform:uppercase; letter-spacing:.05em; }
.search-bar { background:var(--card-bg); border:1px solid var(--border-color); border-radius:12px; padding:.875rem 1.25rem; margin-bottom:1.5rem; display:flex; gap:.75rem; align-items:center; }
.search-input { flex:1; padding:.5rem .875rem; background:rgba(0,212,255,.05); border:1px solid var(--border-color); border-radius:8px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.875rem; }
.search-input:focus { outline:none; border-color:var(--accent-cyan); }
.btn-search { padding:.5rem 1rem; background:rgba(0,212,255,.1); border:1px solid rgba(0,212,255,.3); border-radius:8px; color:var(--accent-cyan); font-size:.8rem; font-weight:600; cursor:pointer; }
.records-table { width:100%; border-collapse:collapse; }
.records-table th { text-align:left; font-size:.75rem; color:var(--text-tertiary); text-transform:uppercase; letter-spacing:.06em; padding:.875rem 1rem; border-bottom:1px solid var(--border-color); font-weight:600; }
.records-table td { padding:.875rem 1rem; border-bottom:1px solid rgba(0,212,255,.05); font-size:.875rem; vertical-align:middle; }
.records-table tr:hover td { background:rgba(0,212,255,.03); }
.svc-type { font-weight:600; color:var(--text-primary); }
.vehicle-info { font-size:.8rem; color:var(--text-secondary); margin-top:.2rem; }
.cost-val { font-family:'Orbitron',sans-serif; font-size:.9rem; font-weight:700; color:var(--accent-green); }
.parts-pill { display:inline-block; padding:.15rem .5rem; background:rgba(0,212,255,.08); border:1px solid rgba(0,212,255,.2); border-radius:6px; font-size:.7rem; color:var(--accent-cyan); margin:.15rem; }
.action-btns { display:flex; gap:.5rem; }
.btn-edit { padding:.375rem .75rem; background:rgba(0,212,255,.08); border:1px solid rgba(0,212,255,.2); border-radius:7px; color:var(--accent-cyan); font-size:.75rem; font-weight:600; text-decoration:none; transition:all .2s; }
.btn-edit:hover { background:rgba(0,212,255,.15); }
.btn-del { padding:.375rem .75rem; background:rgba(255,51,102,.08); border:1px solid rgba(255,51,102,.2); border-radius:7px; color:#ff8099; font-size:.75rem; font-weight:600; cursor:pointer; transition:all .2s; }
.btn-del:hover { background:rgba(255,51,102,.15); }
.empty-state { text-align:center; padding:5rem 2rem; color:var(--text-tertiary); }
.table-wrap { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; overflow:hidden; }
@media(max-width:768px) { .stats-row{grid-template-columns:1fr 1fr} .records-table th:nth-child(4),.records-table td:nth-child(4){display:none} }
</style>

<div class="pg">
    <div class="page-header">
        <div>
            <div class="page-title">Service <span>Records</span></div>
            <div style="color:var(--text-tertiary);font-size:.85rem;margin-top:.25rem;">Your complete service history log</div>
        </div>
        <a href="{{ route('provider.service-records.create') }}" class="btn-new">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Record
        </a>
    </div>

    @if(session('success'))
    <div style="background:rgba(0,255,170,.1);border:1px solid rgba(0,255,170,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-green);font-size:.875rem;">✓ {{ session('success') }}</div>
    @endif

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-box"><div class="stat-num" style="color:var(--accent-cyan);">{{ $stats['total'] }}</div><div class="stat-lbl">Total Records</div></div>
        <div class="stat-box"><div class="stat-num" style="color:var(--accent-green);">{{ $stats['this_month'] }}</div><div class="stat-lbl">This Month</div></div>
        <div class="stat-box"><div class="stat-num" style="color:#a855f7;">${{ number_format($stats['total_revenue'], 0) }}</div><div class="stat-lbl">Total Revenue</div></div>
    </div>

    {{-- Search --}}
    <form method="GET" class="search-bar">
        <input type="text" name="search" class="search-input" placeholder="Search by service type or description..." value="{{ request('search') }}">
        <button type="submit" class="btn-search">Search</button>
        @if(request('search'))<a href="{{ route('provider.service-records.index') }}" style="font-size:.8rem;color:var(--text-tertiary);text-decoration:none;">Clear</a>@endif
    </form>

    {{-- Table --}}
    <div class="table-wrap">
        @if($records->isEmpty())
        <div class="empty-state">
            <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:.2;margin-bottom:1rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <h3 style="font-family:'Orbitron',sans-serif;font-size:1rem;color:var(--text-secondary);margin-bottom:.5rem;">No Records Yet</h3>
            <p style="margin-bottom:1.5rem;">Start logging your completed services to build your professional history.</p>
            <a href="{{ route('provider.service-records.create') }}" class="btn-new">Add Your First Record</a>
        </div>
        @else
        <table class="records-table">
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Vehicle</th>
                    <th>Date</th>
                    <th>Mileage</th>
                    <th>Cost</th>
                    <th>Parts</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $record)
                <tr>
                    <td>
                        <div class="svc-type">{{ $record->service_type }}</div>
                        @if($record->invoice_number)
                        <div style="font-size:.72rem;color:var(--text-tertiary);">Invoice: {{ $record->invoice_number }}</div>
                        @endif
                    </td>
                    <td>
                        @if($record->vehicle)
                        <div class="vehicle-info">{{ $record->vehicle->year }} {{ $record->vehicle->make }} {{ $record->vehicle->model }}</div>
                        @else
                        <div class="vehicle-info" style="color:var(--text-tertiary);">External vehicle</div>
                        @endif
                    </td>
                    <td>
                        <div>{{ $record->service_date->format('M d, Y') }}</div>
                        @if($record->next_service_date)
                        <div style="font-size:.72rem;color:var(--accent-warning);">Next: {{ $record->next_service_date->format('M d, Y') }}</div>
                        @endif
                    </td>
                    <td>{{ $record->mileage_at_service ? number_format($record->mileage_at_service) . ' mi' : '—' }}</td>
                    <td><div class="cost-val">{{ $record->cost ? '$'.number_format($record->cost, 2) : '—' }}</div></td>
                    <td>
                        @if($record->parts_replaced)
                            @foreach(array_slice((array)$record->parts_replaced, 0, 3) as $part)
                            <span class="parts-pill">{{ $part }}</span>
                            @endforeach
                            @if(count((array)$record->parts_replaced) > 3)
                            <span style="font-size:.7rem;color:var(--text-tertiary);">+{{ count((array)$record->parts_replaced) - 3 }} more</span>
                            @endif
                        @else
                        <span style="color:var(--text-tertiary);font-size:.8rem;">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-btns" style="justify-content:flex-end;">
                            <a href="{{ route('provider.service-records.edit', $record) }}" class="btn-edit">Edit</a>
                            <form method="POST" action="{{ route('provider.service-records.destroy', $record) }}" onsubmit="return confirm('Delete this record?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-del">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    @if($records->hasPages())
    <div style="margin-top:1.5rem;">{{ $records->links() }}</div>
    @endif
</div>
@endsection