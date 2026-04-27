@php
    $isProvider = auth()->user()->serviceProvider !== null;
@endphp

@extends($isProvider ? 'provider.layouts.app' : 'layouts.app')
@section('title', 'My Disputes')

@section('content')
<style>
.dp-page { max-width: 860px; margin: 0 auto; padding: {{ $isProvider ? '0' : '2rem 1.5rem' }}; }
.page-title { font-family:'Orbitron',sans-serif; font-size:1.4rem; font-weight:800; margin-bottom:.35rem; }
.page-title span { color:#ff8099; }
.dp-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.5rem; margin-bottom:1rem; position:relative; overflow:hidden; transition:border-color .3s; }
.dp-card:hover { border-color:rgba(255,51,102,.2); }
.dp-card-top { position:absolute; top:0; left:0; right:0; height:2px; }
.status-open .dp-card-top          { background:var(--accent-warning); }
.status-under_review .dp-card-top  { background:var(--accent-cyan); }
.status-resolved_consumer .dp-card-top,
.status-resolved_provider .dp-card-top,
.status-resolved_split .dp-card-top { background:var(--accent-green); }
.status-closed .dp-card-top        { background:rgba(255,255,255,.1); }
.dp-layout { display:grid; grid-template-columns:1fr auto; gap:1rem; align-items:start; }
.dp-ref { font-family:'Orbitron',sans-serif; font-size:.75rem; color:var(--accent-cyan); margin-bottom:.25rem; }
.dp-reason { font-size:.95rem; font-weight:700; color:var(--text-primary); margin-bottom:.375rem; }
.dp-job { font-size:.8rem; color:var(--text-secondary); margin-bottom:.625rem; }
.dp-meta { display:flex; gap:1rem; flex-wrap:wrap; font-size:.75rem; color:var(--text-tertiary); margin-bottom:.75rem; }
.status-pill { display:inline-flex; align-items:center; gap:.35rem; padding:.22rem .7rem; border-radius:20px; font-size:.72rem; font-weight:700; }
.btn-view { display:inline-flex; align-items:center; gap:.4rem; padding:.5rem 1rem; background:rgba(255,51,102,.08); border:1px solid rgba(255,51,102,.2); border-radius:8px; color:#ff8099; font-size:.78rem; text-decoration:none; transition:all .2s; margin-top:.75rem; }
.btn-view:hover { background:rgba(255,51,102,.15); }
.frozen-amount { font-family:'Orbitron',sans-serif; font-size:1.25rem; font-weight:900; color:#ff8099; text-align:right; }
.frozen-label { font-size:.7rem; color:var(--text-tertiary); text-align:right; margin-top:.2rem; }
.empty-state { text-align:center; padding:5rem 2rem; color:var(--text-tertiary); }
</style>

<div class="dp-page">
    @if(session('success'))
    <div style="background:rgba(0,255,170,.1);border:1px solid rgba(0,255,170,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-green);font-size:.875rem;">✓ {{ session('success') }}</div>
    @endif

    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;margin-bottom:1.75rem;">
        <div>
            <div class="page-title">My <span>Disputes</span></div>
            <p style="font-size:.825rem;color:var(--text-tertiary);margin-top:.25rem;">
                {{ $isProvider ? 'Disputes raised on your jobs' : 'Disputes you have raised' }}
            </p>
        </div>
    </div>

    @if($disputes->isEmpty())
    <div class="empty-state">
        <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:.2;margin:0 auto 1rem;display:block;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
        </svg>
        <h3 style="font-family:'Orbitron',sans-serif;font-size:1rem;color:var(--text-secondary);margin-bottom:.625rem;">No disputes</h3>
        <p style="font-size:.875rem;">{{ $isProvider ? 'No disputes have been raised on your jobs.' : 'You have not raised any disputes.' }}</p>
    </div>
    @else
    @foreach($disputes as $dispute)
    <div class="dp-card status-{{ $dispute->status }}">
        <div class="dp-card-top"></div>
        <div class="dp-layout">
            <div>
                <div class="dp-ref">{{ $dispute->reference }}</div>
                <div class="dp-reason">{{ $dispute->reasonLabel() }}</div>
                <div class="dp-job">
                    Job #{{ $dispute->job->job_number }} · {{ $dispute->job->service_type }}
                    @if($isProvider)
                        · Consumer: {{ $dispute->raisedBy->name ?? '—' }}
                    @endif
                </div>
                <div class="dp-meta">
                    <span>Raised {{ $dispute->created_at->diffForHumans() }}</span>
                    <span>{{ $dispute->message_count }} message{{ $dispute->message_count !== 1 ? 's' : '' }}</span>
                    @if($dispute->last_message_at)
                    <span>Last reply {{ $dispute->last_message_at->diffForHumans() }}</span>
                    @endif
                </div>
                <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
                    <span class="status-pill" style="background:{{ $dispute->statusColor() }}1a;color:{{ $dispute->statusColor() }};border:1px solid {{ $dispute->statusColor() }}44;">
                        {{ $dispute->statusLabel() }}
                    </span>
                    @if($dispute->isActive())
                    <span style="font-size:.72rem;color:var(--accent-warning);font-weight:600;">⚡ Awaiting resolution</span>
                    @endif
                </div>
                <a href="{{ route('disputes.show', $dispute) }}" class="btn-view">
                    {{ $dispute->isActive() ? '💬 View & Respond' : 'View' }} →
                </a>
            </div>
            <div>
                @if($dispute->escrow)
                <div class="frozen-amount">{{ $dispute->escrow->formattedAmount() }}</div>
                <div class="frozen-label">
                    {{ $dispute->isResolved() ? 'resolved' : 'frozen' }}
                </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach

    <div style="margin-top:1.5rem;">{{ $disputes->links() }}</div>
    @endif
</div>
@endsection