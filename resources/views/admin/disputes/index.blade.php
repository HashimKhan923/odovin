@extends('admin.layouts.app')
@section('title', 'Disputes')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Dispute Resolution</div>
        <div class="page-sub">Review and resolve payment disputes between consumers and providers</div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:1.5rem;">✓ {{ session('success') }}</div>
@endif

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:1rem;margin-bottom:1.5rem;">
    @foreach([
        ['Open',          $stats['open'],           '#ffaa00', 'open'],
        ['Under Review',  $stats['under_review'],   '#00d4ff', 'under_review'],
        ['Resolved',      $stats['resolved'],        '#00ffaa', ''],
        ['Total',         $stats['total'],           'var(--text-secondary)', ''],
        ['Frozen ($)',     '$'.number_format($stats['frozen_amount']/100,2), '#ff8099', ''],
    ] as [$label, $val, $color, $filter])
    <a href="{{ $filter ? route('admin.disputes.index', ['status'=>$filter]) : route('admin.disputes.index') }}"
       style="background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:1.1rem 1.25rem;text-align:center;text-decoration:none;display:block;">
        <div style="font-family:'Orbitron',sans-serif;font-size:1.5rem;font-weight:900;color:{{ $color }};">{{ $val }}</div>
        <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:.07em;color:var(--text-tertiary);margin-top:.2rem;">{{ $label }}</div>
    </a>
    @endforeach
</div>

{{-- Filters --}}
<div style="display:flex;gap:.5rem;margin-bottom:1.25rem;flex-wrap:wrap;align-items:center;">
    @foreach(['' => 'All', 'open' => 'Open', 'under_review' => 'Under Review', 'resolved_consumer' => 'Refunded', 'resolved_provider' => 'Released', 'closed' => 'Closed'] as $val => $label)
    <a href="{{ route('admin.disputes.index', array_filter(['status' => $val])) }}"
       class="btn {{ request('status') === $val || (!request('status') && $val === '') ? 'btn-primary' : 'btn-secondary' }} btn-sm">{{ $label }}</a>
    @endforeach
    <form method="GET" style="margin-left:auto;display:flex;gap:.5rem;">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Reference, job #, name..."
               style="padding:.45rem .875rem;background:rgba(0,212,255,.04);border:1px solid var(--border-color);border-radius:8px;color:var(--text-primary);font-family:'Chakra Petch',sans-serif;font-size:.82rem;outline:none;width:220px;">
        <button type="submit" class="btn btn-secondary btn-sm">Search</button>
    </form>
</div>

<div class="card">
    <div class="card-title" style="display:flex;justify-content:space-between;align-items:center;">
        <span>Disputes</span>
        @if($stats['open'] > 0)
        <span style="font-size:.75rem;color:var(--accent-warning);font-weight:600;">⚡ {{ $stats['open'] }} need attention</span>
        @endif
    </div>
    @if($disputes->isEmpty())
    <div style="text-align:center;padding:4rem 2rem;color:var(--text-tertiary);font-size:.875rem;">No disputes found.</div>
    @else
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:.82rem;">
            <thead>
                <tr style="border-bottom:1px solid var(--border-color);">
                    @foreach(['Reference','Consumer','Reason','Amount Frozen','Raised','Status','Assigned To','Action'] as $h)
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.7rem;text-transform:uppercase;letter-spacing:.07em;color:var(--text-tertiary);white-space:nowrap;">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($disputes as $d)
                <tr style="border-bottom:1px solid rgba(0,212,255,.04);" onmouseover="this.style.background='rgba(0,212,255,.02)'" onmouseout="this.style.background=''">
                    <td style="padding:.875rem 1rem;">
                        <div style="font-family:'Orbitron',sans-serif;font-size:.78rem;font-weight:700;color:var(--accent-cyan);">{{ $d->reference }}</div>
                        <div style="font-size:.72rem;color:var(--text-tertiary);">Job #{{ $d->job->job_number }}</div>
                    </td>
                    <td style="padding:.875rem 1rem;">
                        <div style="font-weight:600;color:var(--text-primary);">{{ $d->raisedBy->name }}</div>
                    </td>
                    <td style="padding:.875rem 1rem;color:var(--text-secondary);">{{ $d->reasonLabel() }}</td>
                    <td style="padding:.875rem 1rem;font-family:'Orbitron',sans-serif;font-weight:700;color:#ff8099;">
                        {{ $d->escrow?->formattedAmount() ?? '—' }}
                    </td>
                    <td style="padding:.875rem 1rem;color:var(--text-tertiary);white-space:nowrap;">{{ $d->created_at->diffForHumans() }}</td>
                    <td style="padding:.875rem 1rem;">
                        <span style="display:inline-flex;padding:.2rem .6rem;border-radius:12px;font-size:.72rem;font-weight:700;background:{{ $d->statusColor() }}1a;color:{{ $d->statusColor() }};border:1px solid {{ $d->statusColor() }}33;">{{ $d->statusLabel() }}</span>
                    </td>
                    <td style="padding:.875rem 1rem;color:var(--text-tertiary);font-size:.78rem;">{{ $d->assignee?->name ?? '—' }}</td>
                    <td style="padding:.875rem 1rem;">
                        <a href="{{ route('admin.disputes.show', $d) }}"
                           style="display:inline-flex;align-items:center;gap:.35rem;padding:.4rem .875rem;background:{{ $d->isOpen() ? 'rgba(255,170,0,.1)' : 'rgba(0,212,255,.08)' }};border:1px solid {{ $d->isOpen() ? 'rgba(255,170,0,.25)' : 'rgba(0,212,255,.2)' }};border-radius:8px;color:{{ $d->isOpen() ? 'var(--accent-warning)' : 'var(--accent-cyan)' }};font-size:.75rem;text-decoration:none;">
                            {{ $d->isOpen() ? '⚡ Review' : 'View' }} →
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top:1.25rem;">{{ $disputes->links() }}</div>
    @endif
</div>
@endsection