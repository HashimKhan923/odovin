@extends('layouts.app')
@section('title', 'Quote — ' . $quote->service_type)

@section('content')
<style>
.qs-wrap { max-width: 760px; margin: 0 auto; padding: 2rem 1.5rem; }
.back-link { display:inline-flex; align-items:center; gap:.5rem; color:var(--accent-cyan); text-decoration:none; font-size:.875rem; font-weight:600; margin-bottom:1.5rem; transition:all .3s; }
.back-link:hover { gap:.875rem; }
.qs-hero { background:var(--card-bg); border:1px solid var(--border-color); border-radius:18px; padding:1.75rem; margin-bottom:1.25rem; position:relative; overflow:hidden; }
.qs-hero::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; }
.status-pending  .qs-hero::before { background:var(--accent-warning); }
.status-quoted   .qs-hero::before { background:linear-gradient(90deg,var(--accent-cyan),var(--accent-green)); }
.status-declined .qs-hero::before { background:rgba(255,51,102,.6); }
.qs-ref { font-size:.72rem; color:var(--text-tertiary); font-family:'Chakra Petch',sans-serif; margin-bottom:.35rem; }
.qs-title { font-family:'Orbitron',sans-serif; font-size:1.35rem; font-weight:800; margin-bottom:.625rem; }
.qs-badges { display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:1rem; }
.qs-badge { display:inline-flex; align-items:center; gap:.35rem; padding:.25rem .75rem; border-radius:20px; font-size:.72rem; font-weight:700; }
.card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.5rem; margin-bottom:1.25rem; }
.card-title { font-family:'Orbitron',sans-serif; font-size:.875rem; font-weight:700; margin-bottom:1.1rem; padding-bottom:.75rem; border-bottom:1px solid var(--border-color); }
.detail-row { display:flex; justify-content:space-between; font-size:.825rem; margin-bottom:.75rem; }
.detail-row .k { color:var(--text-tertiary); }
.detail-row .v { color:var(--text-primary); font-weight:600; text-align:right; max-width:60%; }
.desc-block { font-size:.875rem; color:var(--text-secondary); line-height:1.7; padding:1rem; background:rgba(0,212,255,.03); border-left:2px solid rgba(0,212,255,.2); border-radius:0 8px 8px 0; }

/* Quote response block */
.quote-response { background:rgba(0,212,255,.05); border:1px solid rgba(0,212,255,.2); border-radius:16px; padding:1.5rem; margin-bottom:1.25rem; }
.qr-price-big { font-family:'Orbitron',sans-serif; font-size:2.5rem; font-weight:900; color:var(--accent-cyan); line-height:1; margin-bottom:.35rem; }
.qr-price-label { font-size:.78rem; color:var(--text-tertiary); margin-bottom:1.1rem; }
.qr-message { font-size:.875rem; color:var(--text-secondary); line-height:1.7; padding:1rem; background:rgba(0,212,255,.04); border-radius:10px; margin-bottom:1.1rem; }
.action-row { display:flex; gap:.875rem; flex-wrap:wrap; }
.btn-accept { flex:1; min-width:140px; padding:.875rem 1.25rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:10px; color:#000; font-family:'Orbitron',sans-serif; font-weight:800; font-size:.78rem; cursor:pointer; transition:all .3s; text-align:center; }
.btn-accept:hover { transform:translateY(-1px); box-shadow:0 4px 15px rgba(0,212,255,.35); }
.btn-decline { flex:1; min-width:120px; padding:.875rem; background:rgba(255,51,102,.08); border:1px solid rgba(255,51,102,.25); border-radius:10px; color:#ff8099; font-family:'Orbitron',sans-serif; font-weight:700; font-size:.78rem; cursor:pointer; transition:all .3s; }
.btn-decline:hover { background:rgba(255,51,102,.15); }

/* Timeline */
.timeline { position:relative; padding-left:2rem; }
.timeline::before { content:''; position:absolute; left:9px; top:8px; bottom:8px; width:2px; background:linear-gradient(to bottom, var(--accent-cyan), rgba(0,212,255,.1)); }
.tl-item { position:relative; margin-bottom:1.25rem; }
.tl-item:last-child { margin-bottom:0; }
.tl-dot { position:absolute; left:-1.625rem; top:4px; width:12px; height:12px; border-radius:50%; background:var(--accent-cyan); border:2px solid var(--bg-primary); box-shadow:0 0 8px rgba(0,212,255,.5); }
.tl-dot.inactive { background:rgba(255,255,255,.15); box-shadow:none; }
.tl-label { font-size:.82rem; font-weight:700; color:var(--text-primary); }
.tl-sub { font-size:.75rem; color:var(--text-tertiary); margin-top:.15rem; }
</style>

<div class="qs-wrap status-{{ $quote->status }}">
    <a href="{{ route('quotes.index') }}" class="back-link">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        My Quote Requests
    </a>

    @if(session('success'))
    <div style="background:rgba(0,255,170,.1);border:1px solid rgba(0,255,170,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-green);font-size:.875rem;">✓ {{ session('success') }}</div>
    @endif
    @if(session('info'))
    <div style="background:rgba(0,212,255,.08);border:1px solid rgba(0,212,255,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-cyan);font-size:.875rem;">ℹ {{ session('info') }}</div>
    @endif

    {{-- Hero --}}
    <div class="qs-hero">
        <div class="qs-ref">{{ $quote->reference }}</div>
        <div class="qs-title">{{ $quote->service_type }}</div>
        <div class="qs-badges">
            <span class="qs-badge" style="background:{{ $quote->statusColor() }}1a;color:{{ $quote->statusColor() }};border:1px solid {{ $quote->statusColor() }}44;">
                {{ $quote->statusLabel() }}
            </span>
            <span class="qs-badge" style="background:rgba(255,255,255,.06);color:var(--text-secondary);border:1px solid var(--border-color);">
                {{ $quote->urgencyLabel() }}
            </span>
            @if($quote->consumer_action === 'accepted')
            <span class="qs-badge" style="background:rgba(0,255,170,.1);color:var(--accent-green);border:1px solid rgba(0,255,170,.25);">✓ Accepted</span>
            @elseif($quote->consumer_action === 'declined')
            <span class="qs-badge" style="background:rgba(255,255,255,.05);color:var(--text-tertiary);border:1px solid var(--border-color);">Declined</span>
            @endif
        </div>
        <div style="font-size:.8rem;color:var(--text-tertiary);">
            Sent to <a href="{{ route('providers.show', $quote->provider) }}" style="color:var(--accent-cyan);text-decoration:none;font-weight:600;">{{ $quote->provider->name }}</a>
            · {{ $quote->created_at->format('M d, Y · g:i A') }}
        </div>
    </div>

    {{-- Provider quote response (when quoted) --}}
    @if($quote->isQuoted())
    <div class="quote-response">
        <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--accent-cyan);margin-bottom:.875rem;">
            💰 Quote from {{ $quote->provider->name }}
        </div>
        <div class="qr-price-big">${{ number_format($quote->quoted_price, 2) }}</div>
        <div class="qr-price-label">
            @if($quote->estimated_duration)Estimated {{ $quote->estimated_duration }} · @endif
            Responded {{ $quote->responded_at?->diffForHumans() }}
        </div>
        @if($quote->provider_message)
        <div class="qr-message">"{{ $quote->provider_message }}"</div>
        @endif

        @if(is_null($quote->consumer_action))
        <div class="action-row">
            <form method="POST" action="{{ route('quotes.accept', $quote) }}">
                @csrf
                <button type="submit" class="btn-accept"
                    onclick="return confirm('Accept this quote and create a job post for \${{ number_format($quote->quoted_price, 2) }}?')">
                    ✓ Accept Quote & Post Job
                </button>
            </form>
            <form method="POST" action="{{ route('quotes.decline', $quote) }}">
                @csrf
                <button type="submit" class="btn-decline">✕ Decline</button>
            </form>
        </div>
        <p style="font-size:.72rem;color:var(--text-tertiary);margin-top:.75rem;">
            Accepting will create a job post directly assigned to {{ $quote->provider->name }} at this price.
        </p>
        @elseif($quote->consumer_action === 'accepted' && $quote->convertedJob)
        <div style="background:rgba(0,255,170,.06);border:1px solid rgba(0,255,170,.2);border-radius:10px;padding:1rem;margin-top:.875rem;">
            <p style="font-size:.82rem;color:var(--accent-green);font-weight:700;margin-bottom:.35rem;">✓ Quote accepted — Job created</p>
            <a href="{{ route('jobs.show', $quote->convertedJob) }}" style="font-size:.8rem;color:var(--accent-cyan);text-decoration:none;">
                View Job #{{ $quote->convertedJob->job_number }} →
            </a>
        </div>
        @endif
    </div>

    {{-- Declined by provider --}}
    @elseif($quote->isDeclined())
    <div style="background:rgba(255,51,102,.05);border:1px solid rgba(255,51,102,.2);border-radius:14px;padding:1.5rem;margin-bottom:1.25rem;">
        <p style="font-weight:700;color:#ff8099;margin-bottom:.35rem;">Request Declined</p>
        @if($quote->provider_message)
        <p style="font-size:.82rem;color:var(--text-secondary);line-height:1.6;">{{ $quote->provider_message }}</p>
        @endif
        <a href="{{ route('providers.index') }}" style="display:inline-flex;align-items:center;gap:.5rem;margin-top:1rem;padding:.6rem 1.25rem;background:rgba(0,212,255,.08);border:1px solid rgba(0,212,255,.2);border-radius:8px;color:var(--accent-cyan);font-size:.8rem;text-decoration:none;">
            Browse Other Providers →
        </a>
    </div>

    {{-- Pending --}}
    @elseif($quote->isPending())
    <div style="background:rgba(255,170,0,.06);border:1px solid rgba(255,170,0,.2);border-radius:14px;padding:1.25rem 1.5rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:.875rem;">
        <span style="font-size:1.5rem;">⏳</span>
        <div>
            <p style="font-weight:700;color:var(--accent-warning);font-size:.875rem;margin-bottom:.2rem;">Awaiting Response</p>
            <p style="font-size:.78rem;color:var(--text-tertiary);">{{ $quote->provider->name }} has been notified. Expect a reply within 24 hours.</p>
        </div>
    </div>
    @endif

    {{-- Request details --}}
    <div class="card">
        <div class="card-title">Request Details</div>
        <div class="detail-row"><span class="k">Reference</span><span class="v">{{ $quote->reference }}</span></div>
        <div class="detail-row"><span class="k">Service</span><span class="v">{{ $quote->service_type }}</span></div>
        @if($quote->vehicle)<div class="detail-row"><span class="k">Vehicle</span><span class="v">{{ $quote->vehicle->year }} {{ $quote->vehicle->make }} {{ $quote->vehicle->model }}</span></div>@endif
        @if($quote->preferred_date)<div class="detail-row"><span class="k">Preferred Date</span><span class="v">{{ \Carbon\Carbon::parse($quote->preferred_date)->format('M d, Y') }}{{ $quote->preferred_time ? ' · ' . $quote->preferred_time : '' }}</span></div>@endif
        @if($quote->budgetLabel() !== 'Flexible')<div class="detail-row"><span class="k">Budget</span><span class="v">{{ $quote->budgetLabel() }}</span></div>@endif
        <div class="detail-row"><span class="k">Urgency</span><span class="v">{{ $quote->urgencyLabel() }}</span></div>
        <div class="detail-row"><span class="k">Expires</span><span class="v">{{ $quote->expires_at?->format('M d, Y') }}</span></div>
        <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border-color);">
            <div style="font-size:.72rem;color:var(--text-tertiary);margin-bottom:.5rem;text-transform:uppercase;letter-spacing:.05em;">Your Description</div>
            <div class="desc-block">{{ $quote->description }}</div>
        </div>
    </div>

    {{-- Timeline --}}
    <div class="card">
        <div class="card-title">Timeline</div>
        <div class="timeline">
            <div class="tl-item">
                <div class="tl-dot"></div>
                <div class="tl-label">Quote Request Sent</div>
                <div class="tl-sub">{{ $quote->created_at->format('M d, Y · g:i A') }}</div>
            </div>
            <div class="tl-item">
                <div class="tl-dot {{ $quote->responded_at ? '' : 'inactive' }}"></div>
                <div class="tl-label" style="{{ $quote->responded_at ? '' : 'color:var(--text-tertiary);' }}">Provider Responded</div>
                <div class="tl-sub">{{ $quote->responded_at ? $quote->responded_at->format('M d, Y · g:i A') : 'Pending...' }}</div>
            </div>
            <div class="tl-item">
                <div class="tl-dot {{ $quote->consumer_action_at ? '' : 'inactive' }}"></div>
                <div class="tl-label" style="{{ $quote->consumer_action_at ? '' : 'color:var(--text-tertiary);' }}">
                    @if($quote->consumer_action === 'accepted') Quote Accepted
                    @elseif($quote->consumer_action === 'declined') Quote Declined
                    @else Your Response @endif
                </div>
                <div class="tl-sub">{{ $quote->consumer_action_at ? $quote->consumer_action_at->format('M d, Y · g:i A') : 'Pending...' }}</div>
            </div>
            @if($quote->convertedJob)
            <div class="tl-item">
                <div class="tl-dot"></div>
                <div class="tl-label" style="color:var(--accent-green);">Job Posted</div>
                <div class="tl-sub"><a href="{{ route('jobs.show', $quote->convertedJob) }}" style="color:var(--accent-cyan);text-decoration:none;">Job #{{ $quote->convertedJob->job_number }}</a></div>
            </div>
            @endif
        </div>
    </div>

    {{-- Withdraw button (only if still pending) --}}
    @if($quote->isPending())
    <div style="text-align:center;margin-top:.5rem;">
        <form method="POST" action="{{ route('quotes.destroy', $quote) }}">
            @csrf @method('DELETE')
            <button type="submit" style="background:none;border:none;color:var(--text-tertiary);font-size:.78rem;cursor:pointer;text-decoration:underline;" onclick="return confirm('Withdraw this quote request?')">
                Withdraw request
            </button>
        </form>
    </div>
    @endif
</div>
@endsection