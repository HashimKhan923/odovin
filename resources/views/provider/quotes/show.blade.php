@extends('provider.layouts.app')
@section('title', 'Quote Request — ' . $quote->service_type)

@section('content')
<style>
.qps-wrap { max-width: 700px; }
.back-link { display:inline-flex; align-items:center; gap:.5rem; color:var(--accent-cyan); text-decoration:none; font-size:.875rem; font-weight:600; margin-bottom:1.5rem; transition:all .3s; }
.back-link:hover { gap:.875rem; }
.req-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:18px; padding:1.75rem; margin-bottom:1.25rem; position:relative; overflow:hidden; }
.req-card::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; background:linear-gradient(90deg,var(--accent-warning),rgba(255,170,0,.3)); }
.req-ref { font-size:.72rem; color:var(--text-tertiary); margin-bottom:.35rem; }
.req-title { font-family:'Orbitron',sans-serif; font-size:1.25rem; font-weight:800; margin-bottom:.625rem; }
.req-meta { display:flex; gap:1rem; flex-wrap:wrap; font-size:.8rem; color:var(--text-secondary); margin-bottom:1rem; padding-bottom:1rem; border-bottom:1px solid var(--border-color); }
.req-meta-item { display:flex; align-items:center; gap:.4rem; }
.req-desc { font-size:.875rem; color:var(--text-secondary); line-height:1.75; padding:1rem; background:rgba(0,212,255,.03); border-left:2px solid rgba(0,212,255,.2); border-radius:0 8px 8px 0; }
.card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.5rem; margin-bottom:1.25rem; }
.card-title { font-family:'Orbitron',sans-serif; font-size:.875rem; font-weight:700; margin-bottom:1.1rem; padding-bottom:.75rem; border-bottom:1px solid var(--border-color); }
.field-group { margin-bottom:1.1rem; }
.field-label { display:block; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--text-tertiary); margin-bottom:.45rem; }
.field-label span { color:#ff8099; }
.field-input, .field-textarea { width:100%; padding:.75rem 1rem; background:rgba(0,212,255,.04); border:1px solid var(--border-color); border-radius:10px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.875rem; transition:border-color .2s; }
.field-input:focus, .field-textarea:focus { outline:none; border-color:var(--accent-cyan); }
.field-textarea { resize:vertical; min-height:100px; line-height:1.6; }
.price-wrap { position:relative; }
.price-prefix { position:absolute; left:.875rem; top:50%; transform:translateY(-50%); color:var(--text-tertiary); font-weight:700; pointer-events:none; }
.price-wrap .field-input { padding-left:1.75rem; font-family:'Orbitron',sans-serif; font-size:1rem; }
.btn-send { width:100%; padding:.9rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:10px; color:#000; font-family:'Orbitron',sans-serif; font-weight:800; font-size:.8rem; cursor:pointer; transition:all .3s; }
.btn-send:hover { transform:translateY(-1px); box-shadow:0 4px 15px rgba(0,212,255,.3); }
.btn-decline-sm { width:100%; padding:.75rem; background:rgba(255,51,102,.08); border:1px solid rgba(255,51,102,.2); border-radius:10px; color:#ff8099; font-family:'Orbitron',sans-serif; font-weight:700; font-size:.75rem; cursor:pointer; transition:all .2s; margin-top:.625rem; }
.btn-decline-sm:hover { background:rgba(255,51,102,.14); }
.already-responded { background:rgba(0,212,255,.05); border:1px solid rgba(0,212,255,.2); border-radius:14px; padding:1.25rem 1.5rem; margin-bottom:1.25rem; }
.detail-row { display:flex; justify-content:space-between; font-size:.82rem; margin-bottom:.625rem; }
.detail-row .k { color:var(--text-tertiary); }
.detail-row .v { color:var(--text-primary); font-weight:600; }
</style>

<div class="qps-wrap">
    <a href="{{ route('provider.quotes.index') }}" class="back-link">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Quote Requests
    </a>

    @if(session('success'))
    <div style="background:rgba(0,255,170,.1);border:1px solid rgba(0,255,170,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-green);font-size:.875rem;">✓ {{ session('success') }}</div>
    @endif

    {{-- Customer's request --}}
    <div class="req-card">
        <div class="req-ref">{{ $quote->reference }}</div>
        <div class="req-title">{{ $quote->service_type }}</div>
        <div class="req-meta">
            <div class="req-meta-item">👤 <strong>{{ $quote->user->name }}</strong></div>
            @if($quote->vehicle)<div class="req-meta-item">🚗 {{ $quote->vehicle->year }} {{ $quote->vehicle->make }} {{ $quote->vehicle->model }}</div>@endif
            <div class="req-meta-item">{{ $quote->urgencyLabel() }}</div>
            @if($quote->budgetLabel() !== 'Flexible')<div class="req-meta-item">💰 Budget: {{ $quote->budgetLabel() }}</div>@endif
            @if($quote->preferred_date)<div class="req-meta-item">📅 {{ \Carbon\Carbon::parse($quote->preferred_date)->format('M d, Y') }}{{ $quote->preferred_time ? ' · '.$quote->preferred_time : '' }}</div>@endif
            <div class="req-meta-item" style="color:var(--text-tertiary);">Sent {{ $quote->created_at->diffForHumans() }}</div>
        </div>
        <div class="req-desc">{{ $quote->description }}</div>
    </div>

    {{-- Already responded --}}
    @if(!$quote->isPending())
    <div class="already-responded">
        @if($quote->isQuoted())
        <div style="font-family:'Orbitron',sans-serif;font-size:.8rem;font-weight:700;color:var(--accent-cyan);margin-bottom:.625rem;">✓ You sent a quote</div>
        <div class="detail-row"><span class="k">Your price</span><span class="v" style="font-family:'Orbitron',sans-serif;color:var(--accent-cyan);">${{ number_format($quote->quoted_price, 2) }}</span></div>
        @if($quote->estimated_duration)<div class="detail-row"><span class="k">Est. duration</span><span class="v">{{ $quote->estimated_duration }}</span></div>@endif
        @if($quote->provider_message)<div style="margin-top:.625rem;font-size:.82rem;color:var(--text-secondary);font-style:italic;">"{{ $quote->provider_message }}"</div>@endif
        @if($quote->consumer_action === 'accepted')
        <div style="margin-top:.875rem;padding:.75rem 1rem;background:rgba(0,255,170,.08);border:1px solid rgba(0,255,170,.2);border-radius:8px;font-size:.82rem;color:var(--accent-green);font-weight:700;">
            🎉 Customer accepted! <a href="{{ route('provider.jobs.show', $quote->convertedJob) }}" style="color:var(--accent-cyan);margin-left:.5rem;">View Job →</a>
        </div>
        @elseif($quote->consumer_action === 'declined')
        <div style="margin-top:.875rem;font-size:.78rem;color:var(--text-tertiary);">Customer declined the quote.</div>
        @else
        <div style="margin-top:.875rem;font-size:.78rem;color:var(--accent-warning);">⏳ Awaiting customer's decision</div>
        @endif
        @elseif($quote->isDeclined())
        <div style="font-size:.82rem;color:var(--text-tertiary);">You declined this request.@if($quote->provider_message) "{{ $quote->provider_message }}"@endif</div>
        @endif
    </div>
    @else

    {{-- Respond form (only when pending) --}}
    <div class="card">
        <div class="card-title">💬 Send Your Quote</div>
        <form method="POST" action="{{ route('provider.quotes.respond', $quote) }}">
            @csrf
            <div class="field-group">
                <label class="field-label" for="quoted_price">Your Price <span>*</span></label>
                <div class="price-wrap">
                    <span class="price-prefix">$</span>
                    <input type="number" name="quoted_price" id="quoted_price" class="field-input"
                        step="0.01" min="1" placeholder="0.00"
                        value="{{ old('quoted_price') }}" required>
                </div>
                @error('quoted_price')<div style="color:#ff8099;font-size:.75rem;margin-top:.3rem;">{{ $message }}</div>@enderror
            </div>
            <div class="field-group">
                <label class="field-label" for="estimated_duration">Estimated Duration</label>
                <input type="text" name="estimated_duration" id="estimated_duration" class="field-input"
                    placeholder="e.g. 1–2 hours, Half day" value="{{ old('estimated_duration') }}" maxlength="100">
            </div>
            <div class="field-group">
                <label class="field-label" for="provider_message">Message to Customer <span>*</span></label>
                <textarea name="provider_message" id="provider_message" class="field-textarea"
                    placeholder="Explain what's included in your quote, any conditions, your availability, etc."
                    maxlength="1500" required>{{ old('provider_message') }}</textarea>
                @error('provider_message')<div style="color:#ff8099;font-size:.75rem;margin-top:.3rem;">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn-send">Send Quote →</button>
        </form>
    </div>

    {{-- Decline option --}}
    <div class="card" style="padding:1.25rem 1.5rem;">
        <div style="font-size:.82rem;color:var(--text-secondary);margin-bottom:.875rem;">
            Can't fulfil this request? Let the customer know so they can find another provider.
        </div>
        <form method="POST" action="{{ route('provider.quotes.decline', $quote) }}">
            @csrf
            <div class="field-group">
                <label class="field-label" for="decline_reason">Reason (optional)</label>
                <input type="text" name="decline_reason" id="decline_reason" class="field-input"
                    placeholder="e.g. Not available on requested date, outside service area..."
                    maxlength="500">
            </div>
            <button type="submit" class="btn-decline-sm" onclick="return confirm('Decline this quote request?')">
                ✕ Decline Request
            </button>
        </form>
    </div>
    @endif
</div>
@endsection