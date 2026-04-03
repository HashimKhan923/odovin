@extends('provider.layouts.app')
@section('title', 'Service Diagnostics')
@section('content')
<style>
:root[data-theme="dark"] {
    --card-bg:rgba(26,32,48,.85); --border-color:rgba(0,212,255,.1);
    --input-bg:rgba(0,212,255,.05); --text-primary:#fff;
    --text-secondary:rgba(255,255,255,.7); --text-tertiary:rgba(255,255,255,.45);
}
:root[data-theme="light"] {
    --card-bg:rgba(255,255,255,.9); --border-color:rgba(0,0,0,.1);
    --input-bg:rgba(0,0,0,.03); --text-primary:#1a1f36;
    --text-secondary:rgba(26,31,54,.7); --text-tertiary:rgba(26,31,54,.45);
}
.pg { max-width:1100px; margin:0 auto; padding:2rem 1.5rem; }
.page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:2rem; flex-wrap:wrap; gap:1rem; }
.page-title { font-family:'Orbitron',sans-serif; font-size:1.5rem; font-weight:800; }
.page-title span { color:#ff6600; }
/* Stats */
.stats-row { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1.75rem; }
.stat-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:14px; padding:1.25rem 1.5rem; position:relative; overflow:hidden; }
.stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; }
.stat-card.open::before   { background:linear-gradient(90deg,#ff6600,#ff3366); }
.stat-card.resolved::before { background:linear-gradient(90deg,#00d4ff,#00ffaa); }
.stat-card.critical::before { background:linear-gradient(90deg,#ff3366,#ff0044); }
.stat-num   { font-family:'Orbitron',sans-serif; font-size:2rem; font-weight:800; line-height:1; margin-bottom:.25rem; }
.stat-label { font-size:.75rem; color:var(--text-tertiary); text-transform:uppercase; letter-spacing:.06em; }
/* Filters */
.filter-bar { display:flex; gap:.75rem; margin-bottom:1.5rem; flex-wrap:wrap; }
.filter-pill { padding:.4rem .875rem; border-radius:20px; border:1px solid var(--border-color); background:var(--input-bg); color:var(--text-secondary); font-size:.78rem; font-weight:600; cursor:pointer; text-decoration:none; transition:all .25s; }
.filter-pill:hover,.filter-pill.active { border-color:#ff6600; color:#ff9944; background:rgba(255,102,0,.08); }
/* Issue cards */
.issue-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:14px; padding:1.5rem; margin-bottom:1rem; transition:all .3s; position:relative; overflow:hidden; }
.issue-card:hover { border-color:rgba(255,102,0,.3); box-shadow:0 4px 20px rgba(0,0,0,.2); }
.issue-card.safety { border-color:rgba(255,51,102,.3); }
.issue-card.safety::before { content:'⚠️ SAFETY CRITICAL'; position:absolute; top:0; right:0; background:rgba(255,51,102,.9); color:#fff; font-size:.65rem; font-weight:700; padding:.25rem .75rem; border-radius:0 14px 0 8px; letter-spacing:.06em; }
.issue-header { display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; margin-bottom:.875rem; }
.issue-title { font-weight:700; font-size:1rem; color:var(--text-primary); margin-bottom:.3rem; }
.issue-meta { font-size:.78rem; color:var(--text-tertiary); }
.sev-badge { display:inline-flex; align-items:center; gap:.3rem; font-size:.72rem; font-weight:700; padding:.2rem .6rem; border-radius:6px; }
.status-badge { display:inline-flex; align-items:center; font-size:.72rem; font-weight:700; padding:.25rem .7rem; border-radius:6px; }
.issue-body { font-size:.825rem; color:var(--text-secondary); line-height:1.6; margin-bottom:1rem; }
.issue-footer { display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; padding-top:.875rem; border-top:1px solid var(--border-color); }
.cost-est { font-size:.78rem; color:var(--text-tertiary); display:flex; align-items:center; gap:.35rem; }
/* Status update form */
.status-form { display:flex; gap:.5rem; align-items:center; flex-wrap:wrap; }
.status-select { padding:.4rem .75rem; background:var(--input-bg); border:1px solid var(--border-color); border-radius:8px; color:var(--text-primary); font-size:.78rem; font-family:'Chakra Petch',sans-serif; cursor:pointer; }
.status-select:focus { outline:none; border-color:#ff6600; }
.btn-update-status { padding:.4rem .875rem; background:rgba(255,102,0,.12); border:1px solid rgba(255,102,0,.3); border-radius:8px; color:#ff9944; font-size:.78rem; font-weight:600; cursor:pointer; transition:all .3s; white-space:nowrap; }
.btn-update-status:hover { background:rgba(255,102,0,.2); border-color:#ff6600; }
/* Note modal */
.note-toggle { font-size:.75rem; color:var(--accent-cyan); cursor:pointer; background:none; border:none; padding:0; text-decoration:underline; }
.note-field { margin-top:.5rem; width:100%; padding:.5rem .75rem; background:var(--input-bg); border:1px solid var(--border-color); border-radius:8px; color:var(--text-primary); font-size:.8rem; font-family:'Chakra Petch',sans-serif; resize:none; min-height:60px; }
.note-field:focus { outline:none; border-color:#ff6600; }
.empty-state { text-align:center; padding:4rem 2rem; color:var(--text-tertiary); }
.empty-state .icon { font-size:3rem; margin-bottom:1rem; opacity:.5; }
@media(max-width:700px) { .stats-row { grid-template-columns:1fr; } }
</style>

<div class="pg">
    <div class="page-header">
        <div>
            <div class="page-title">Vehicle <span>Issues</span></div>
            <p style="color:var(--text-tertiary);font-size:.875rem;margin-top:.25rem;">Issues you flagged during service inspections — visible to vehicle owners</p>
        </div>
    </div>

    @if(session('success'))
    <div style="background:rgba(0,255,170,.1);border:1px solid rgba(0,255,170,.3);border-radius:10px;padding:.875rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-green);font-size:.875rem;">✓ {{ session('success') }}</div>
    @endif

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-card open">
            <div class="stat-num" style="color:#ff6600;">{{ $stats['open'] }}</div>
            <div class="stat-label">Open Issues</div>
        </div>
        <div class="stat-card critical">
            <div class="stat-num" style="color:#ff3366;">{{ $stats['critical'] }}</div>
            <div class="stat-label">Critical / Urgent</div>
        </div>
        <div class="stat-card resolved">
            <div class="stat-num" style="color:var(--accent-green);">{{ $stats['resolved'] }}</div>
            <div class="stat-label">Resolved</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filter-bar">
        <a href="{{ route('provider.service-diagnostics.index') }}" class="filter-pill {{ !request('status') && !request('severity') ? 'active' : '' }}">All</a>
        <a href="{{ route('provider.service-diagnostics.index', ['status'=>'open']) }}" class="filter-pill {{ request('status')==='open' ? 'active' : '' }}">🔴 Open</a>
        <a href="{{ route('provider.service-diagnostics.index', ['status'=>'in_progress']) }}" class="filter-pill {{ request('status')==='in_progress' ? 'active' : '' }}">🔵 In Progress</a>
        <a href="{{ route('provider.service-diagnostics.index', ['status'=>'monitoring']) }}" class="filter-pill {{ request('status')==='monitoring' ? 'active' : '' }}">🟣 Monitoring</a>
        <a href="{{ route('provider.service-diagnostics.index', ['status'=>'resolved']) }}" class="filter-pill {{ request('status')==='resolved' ? 'active' : '' }}">✅ Resolved</a>
        <span style="border-left:1px solid var(--border-color);margin:0 .25rem;"></span>
        <a href="{{ route('provider.service-diagnostics.index', ['severity'=>'critical']) }}" class="filter-pill {{ request('severity')==='critical' ? 'active' : '' }}">🔴 Critical Only</a>
    </div>

    {{-- Issue list --}}
    @forelse($issues as $issue)
    @php
        $sevColors = ['low'=>'#00ffaa','medium'=>'#ffaa00','high'=>'#ff6600','critical'=>'#ff3366'];
        $sevBgs    = ['low'=>'rgba(0,255,170,.1)','medium'=>'rgba(255,170,0,.1)','high'=>'rgba(255,102,0,.1)','critical'=>'rgba(255,51,102,.1)'];
        $statColors= ['open'=>'#ff3366','acknowledged'=>'#ffaa00','in_progress'=>'#00d4ff','monitoring'=>'#aa88ff','resolved'=>'#00ffaa','ignored'=>'#555577'];
        $sc = $sevColors[$issue->severity] ?? '#888';
        $sb = $sevBgs[$issue->severity] ?? 'rgba(136,136,136,.1)';
        $stc= $statColors[$issue->status] ?? '#888';
    @endphp
    <div class="issue-card {{ $issue->is_safety_critical ? 'safety' : '' }}">
        <div class="issue-header">
            <div>
                <div class="issue-title">{{ $issue->category_icon }} {{ $issue->title }}</div>
                <div class="issue-meta">
                    {{ $issue->vehicle->year }} {{ $issue->vehicle->make }} {{ $issue->vehicle->model }}
                    @if($issue->location) · {{ $issue->location }} @endif
                    · {{ $issue->created_at->format('M d, Y') }}
                    @if($issue->serviceRecord) · <a href="{{ route('provider.service-records.show', $issue->serviceRecord) }}" style="color:var(--accent-cyan);text-decoration:none;">Service Record #{{ $issue->serviceRecord->id }}</a>@endif
                </div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.4rem;">
                <span class="sev-badge" style="background:{{ $sb }};color:{{ $sc }};border:1px solid {{ $sc . '40' }};">
                    {{ ucfirst($issue->severity) }}
                </span>
                <span class="status-badge" style="background:{{ $stc . '18' }};color:{{ $stc }};border:1px solid {{ $stc . '40' }};">
                    {{ $issue->status_label }}
                </span>
            </div>
        </div>

        <div class="issue-body">{{ $issue->description }}</div>

        @if($issue->resolution_notes && $issue->status === 'resolved')
        <div style="padding:.75rem 1rem;background:rgba(0,255,170,.05);border:1px solid rgba(0,255,170,.15);border-radius:8px;font-size:.8rem;color:var(--text-secondary);margin-bottom:.875rem;">
            <strong style="color:var(--accent-green);">Resolution:</strong> {{ $issue->resolution_notes }}
        </div>
        @endif

        <div class="issue-footer">
            <div class="cost-est">
                @if($issue->estimated_cost_min || $issue->estimated_cost_max)
                💰 Est. repair: <strong style="color:var(--text-primary);">{{ $issue->cost_range }}</strong>
                @else
                💰 No cost estimate provided
                @endif
            </div>

            {{-- Status update inline form --}}
            <form method="POST" action="{{ route('provider.service-diagnostics.updateStatus', $issue) }}" id="form-{{ $issue->id }}">
                @csrf @method('PATCH')
                <div class="status-form">
                    <select name="status" class="status-select" onchange="toggleResolutionNote({{ $issue->id }}, this.value)">
                        @foreach(['open'=>'Open','acknowledged'=>'Acknowledged','in_progress'=>'In Progress','monitoring'=>'Monitoring','resolved'=>'Resolved','ignored'=>'Ignored'] as $val => $label)
                        <option value="{{ $val }}" {{ $issue->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="note-toggle" onclick="toggleNote({{ $issue->id }})">+ add note</button>
                    <button type="submit" class="btn-update-status">Update</button>
                </div>
                <div id="note-area-{{ $issue->id }}" style="display:none;margin-top:.5rem;">
                    <textarea name="status_notes" class="note-field" placeholder="Optional note about this status change..."></textarea>
                    <div id="res-note-{{ $issue->id }}" style="display:none;margin-top:.35rem;">
                        <textarea name="resolution_notes" class="note-field" placeholder="How was this resolved? Parts replaced, repairs done..."></textarea>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <div class="icon">🔍</div>
        <div style="font-weight:700;font-size:1.1rem;color:var(--text-secondary);margin-bottom:.5rem;">No issues found</div>
        <div>Issues you flag during service records will appear here.</div>
    </div>
    @endforelse

    {{ $issues->withQueryString()->links() }}
</div>

<script>
function toggleNote(id) {
    const area = document.getElementById(`note-area-${id}`);
    area.style.display = area.style.display === 'none' ? 'block' : 'none';
}
function toggleResolutionNote(id, status) {
    const area = document.getElementById(`note-area-${id}`);
    const resNote = document.getElementById(`res-note-${id}`);
    if (status === 'resolved') {
        area.style.display = 'block';
        resNote.style.display = 'block';
    } else {
        resNote.style.display = 'none';
    }
}
</script>
@endsection