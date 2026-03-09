@extends('layouts.app')
@section('title', $job->service_type . ' — Job #' . $job->job_number)

@section('content')
<style>
.pg { max-width:1100px; margin:0 auto; padding:2rem 1.5rem; }
.back-link { display:inline-flex; align-items:center; gap:.5rem; color:var(--accent-cyan); text-decoration:none; font-size:.875rem; font-weight:600; margin-bottom:1.5rem; transition:all .3s; }
.back-link:hover { gap:.875rem; }
/* Hero */
.hero { background:var(--card-bg); border:1px solid var(--border-color); border-radius:20px; padding:2rem; margin-bottom:1.5rem; position:relative; overflow:hidden; }
.hero::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,var(--accent-cyan),var(--accent-green)); }
.hero-top { display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; flex-wrap:wrap; }
.job-title { font-family:'Orbitron',sans-serif; font-size:1.75rem; font-weight:800; margin-bottom:.25rem;     color: var(--text-primary);
 }
.job-num { font-size:.8rem; color:var(--text-tertiary); font-family:'Chakra Petch',sans-serif; }
.pill { display:inline-flex; align-items:center; gap:.35rem; padding:.375rem 1rem; border-radius:20px; font-size:.8rem; font-weight:700; }
.pill-open        { background:rgba(255,170,0,.12); color:#ffaa00; border:1px solid rgba(255,170,0,.3); }
.pill-accepted    { background:rgba(0,212,255,.12); color:#00d4ff; border:1px solid rgba(0,212,255,.3); }
.pill-completed   { background:rgba(0,255,170,.12); color:#00ffaa; border:1px solid rgba(0,255,170,.3); }
.pill-cancelled,.pill-expired { background:rgba(255,255,255,.07); color:var(--text-tertiary); border:1px solid rgba(255,255,255,.1); }
.meta-strip { display:flex; gap:2rem; flex-wrap:wrap; margin-top:1.25rem; padding-top:1.25rem; border-top:1px solid var(--border-color); font-size:.875rem; color:var(--text-secondary); }
.meta-item { display:flex; align-items:center; gap:.5rem; }
/* Status timeline */
.timeline-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.75rem; margin-bottom:1.5rem; }
.tl-title { font-family:'Orbitron',sans-serif; font-size:.95rem; font-weight:700; margin-bottom:1.5rem;     color: var(--text-primary);
}
.timeline { position:relative; }
.timeline::before { content:''; position:absolute; left:18px; top:0; bottom:0; width:2px; background:linear-gradient(to bottom, var(--accent-cyan), rgba(0,212,255,.1)); }
.tl-step { display:flex; gap:1rem; align-items:flex-start; margin-bottom:1.5rem; position:relative; }
.tl-step:last-child { margin-bottom:0; }
.tl-icon { width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1rem; flex-shrink:0; z-index:1; border:2px solid var(--border-color); background:var(--card-bg); }
.tl-icon.done    { border-color:var(--accent-green); background:rgba(0,255,170,.1); }
.tl-icon.current { border-color:var(--accent-cyan); background:rgba(0,212,255,.1); box-shadow:0 0 12px rgba(0,212,255,.3); }
.tl-icon.pending { border-color:rgba(255,255,255,.1); opacity:.4; }
.tl-body { flex:1; padding-top:.5rem; }
.tl-label { font-weight:700; font-size:.9rem; margin-bottom:.2rem; }
.tl-sub { font-size:.8rem; color:var(--text-tertiary); }
.tl-step.done .tl-label { color:var(--accent-green); }
.tl-step.current .tl-label { color:var(--accent-cyan); }
.tl-step.pending .tl-label { color:var(--text-tertiary); }
/* Layout */
.main-layout { display:grid; grid-template-columns:1fr 320px; gap:1.5rem; }
.card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.75rem; margin-bottom:1.5rem; }
.card-title { font-family:'Orbitron',sans-serif; font-size:.95rem; font-weight:700; margin-bottom:1.25rem; padding-bottom:.75rem; border-bottom:1px solid var(--border-color);     color: var(--text-primary);
 }
/* Offer cards */
.offer-card { border:1px solid var(--border-color); border-radius:14px; padding:1.5rem; margin-bottom:1rem; transition:all .3s; position:relative; overflow:hidden; }
.offer-card:hover { border-color:rgba(0,212,255,.3); }
.offer-card.winner { border-color:rgba(0,255,170,.4); background:rgba(0,255,170,.03); }
.offer-card.winner::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--accent-green); }
.offer-card.rejected { opacity:.45; }
.provider-name { font-weight:700; font-size:1rem;     color: var(--text-primary);
 }
.offer-price { font-family:'Orbitron',sans-serif; font-size:1.75rem; font-weight:900; color:var(--accent-warning); }
.offer-details { display:grid; grid-template-columns:1fr 1fr; gap:.75rem; margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border-color); font-size:.825rem; color:var(--text-secondary); }
.offer-message { font-size:.825rem; color:var(--text-secondary); margin-top:.875rem; padding:.875rem; background:rgba(0,212,255,.04); border-left:2px solid rgba(0,212,255,.25); border-radius:0 8px 8px 0; line-height:1.6; }
.btn-accept { display:inline-flex; align-items:center; gap:.5rem; padding:.625rem 1.25rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:8px; color:#000; font-family:'Orbitron',sans-serif; font-weight:700; font-size:.75rem; cursor:pointer; transition:all .3s; text-decoration:none; }
.btn-accept:hover { transform:translateY(-1px); box-shadow:0 4px 15px rgba(0,212,255,.4); }
.btn-profile { display:inline-flex; align-items:center; gap:.5rem; padding:.625rem 1.1rem; background:rgba(0,212,255,.08); border:1px solid rgba(0,212,255,.25); border-radius:8px; color:var(--accent-cyan); font-size:.8rem; font-weight:600; text-decoration:none; transition:all .3s; }
/* Rating section */
.rating-card { background:linear-gradient(135deg,rgba(0,255,170,.06),rgba(0,212,255,.04)); border:1px solid rgba(0,255,170,.25); border-radius:16px; padding:2rem; margin-bottom:1.5rem; }
.stars { display:flex; gap:.35rem; margin-bottom:1rem; }
.star { font-size:2rem; cursor:pointer; color:rgba(255,255,255,.15); transition:all .2s; }
.star.filled { color:#ffaa00; }
.star:hover { transform:scale(1.1); color:#ffaa00; }
.form-input,.form-textarea { width:100%; padding:.75rem 1rem; background:rgba(0,212,255,.05); border:1px solid var(--border-color); border-radius:10px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.875rem; transition:all .3s; }
.form-input:focus,.form-textarea:focus { outline:none; border-color:var(--accent-cyan); }
.form-textarea { resize:vertical; min-height:90px; }
.btn-submit { display:inline-flex; align-items:center; gap:.5rem; padding:.75rem 1.75rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:10px; color:#000; font-family:'Orbitron',sans-serif; font-weight:700; font-size:.8rem; cursor:pointer; transition:all .3s; }
.btn-submit:hover { transform:translateY(-1px); box-shadow:0 4px 15px rgba(0,212,255,.4); }
/* Sidebar */
.sidebar-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.5rem; margin-bottom:1rem; }
.sidebar-card h3 { font-family:'Orbitron',sans-serif; font-size:.85rem; font-weight:700; margin-bottom:1rem; padding-bottom:.75rem; border-bottom:1px solid var(--border-color); }
.kv { display:flex; justify-content:space-between; font-size:.825rem; margin-bottom:.75rem; }
.kv .k { color:var(--text-tertiary); }
.kv .v { color:var(--text-primary); font-weight:600; text-align:right; max-width:60%; }
.btn-danger { display:inline-flex; align-items:center; gap:.5rem; padding:.6rem 1.25rem; background:rgba(255,51,102,.1); border:1px solid rgba(255,51,102,.3); border-radius:8px; color:#ff8099; font-size:.8rem; font-weight:600; cursor:pointer; text-decoration:none; transition:all .3s; width:100%; justify-content:center; }
.accepted-banner { background:rgba(0,255,170,.08); border:1px solid rgba(0,255,170,.25); border-radius:14px; padding:1.5rem; text-align:center; margin-bottom:1.5rem; }
.waiting-hint { text-align:center; padding:2.5rem; color:var(--text-tertiary); font-size:.875rem; }
.spinner { width:40px; height:40px; border:2px solid rgba(0,212,255,.15); border-top-color:var(--accent-cyan); border-radius:50%; animation:spin 1s linear infinite; margin:0 auto 1rem; }
@keyframes spin { to { transform:rotate(360deg); } }
.provider-badge { display:inline-flex; align-items:center; gap:.35rem; font-size:.72rem; padding:.2rem .6rem; border-radius:10px; }
.badge-verified { background:rgba(0,255,170,.1); color:var(--accent-green); border:1px solid rgba(0,255,170,.2); }
.badge-type     { background:rgba(0,212,255,.1); color:var(--accent-cyan); border:1px solid rgba(0,212,255,.2); }
@media(max-width:900px) { .main-layout { grid-template-columns:1fr; } }
</style>

<div class="pg">
    <a href="{{ route('jobs.index') }}" class="back-link">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to My Jobs
    </a>

    @if(session('success'))<div style="background:rgba(0,255,170,.1);border:1px solid rgba(0,255,170,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-green);font-size:.875rem;">✓ {{ session('success') }}</div>@endif
    @if(session('error'))<div style="background:rgba(255,51,102,.1);border:1px solid rgba(255,51,102,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:#ff8099;font-size:.875rem;">{{ session('error') }}</div>@endif

    {{-- Hero Card --}}
    <div class="hero">
        <div class="hero-top">
            <div>
                <div class="job-title">{{ $job->service_type }}</div>
                <div class="job-num">Job #{{ $job->job_number }}</div>
            </div>
            <span class="pill pill-{{ $job->status }}">{{ ucfirst($job->status) }}</span>
        </div>
        <div class="meta-strip">
            <div class="meta-item">🚗 <strong>{{ $job->vehicle->year }} {{ $job->vehicle->make }} {{ $job->vehicle->model }}</strong></div>
            <div class="meta-item">💰 Budget: <strong>{{ $job->budgetLabel() }}</strong></div>
            @if($job->preferred_date)<div class="meta-item">📅 <strong>{{ \Carbon\Carbon::parse($job->preferred_date)->format('M d, Y') }}{{ $job->preferred_time ? ' · '.$job->preferred_time : '' }}</strong></div>@endif
            <div class="meta-item">🕐 Posted {{ $job->created_at->diffForHumans() }}</div>
        </div>
    </div>

    {{-- Work Status Timeline (only after offer accepted) --}}
    @if($job->status === 'accepted' || $job->status === 'completed')
    @php
        $ws = $job->work_status ?? 'pending';
        $steps = [
            ['key'=>'offer_accepted', 'icon'=>'✓',  'label'=>'Offer Accepted',  'sub'=>'You selected a provider'],
            ['key'=>'confirmed',      'icon'=>'✅',  'label'=>'Confirmed',        'sub'=>'Provider confirmed the job'],
            ['key'=>'in_progress',    'icon'=>'🔧', 'label'=>'Work In Progress', 'sub'=>$job->work_started_at ? $job->work_started_at->format('M d · H:i') : 'Provider has started'],
            ['key'=>'completed',      'icon'=>'🎉', 'label'=>'Completed',        'sub'=>$job->work_completed_at ? $job->work_completed_at->format('M d · H:i') : 'Job finished'],
        ];
        $order = ['pending'=>0, 'confirmed'=>1, 'in_progress'=>2, 'completed'=>3];
        $currentIdx = $order[$ws] ?? 0;
    @endphp
    <div class="timeline-card">
        <div class="tl-title">📍 Job Progress</div>
        @if($ws === 'cancelled')
        <div style="display:flex;align-items:center;gap:.75rem;padding:1rem;background:rgba(255,51,102,.06);border:1px solid rgba(255,51,102,.2);border-radius:12px;">
            <span style="font-size:1.5rem;">❌</span>
            <div>
                <div style="font-weight:700;color:#ff8099;">Job Cancelled by Provider</div>
                <div style="font-size:.825rem;color:var(--text-tertiary);">Please post a new job to find another provider.{{ $job->provider_notes ? ' Provider note: "'.$job->provider_notes.'"' : '' }}</div>
            </div>
        </div>
        @else
        <div class="timeline">
            {{-- Step 0: Offer Accepted (always done) --}}
            <div class="tl-step done">
                <div class="tl-icon done">✓</div>
                <div class="tl-body"><div class="tl-label">Offer Accepted</div><div class="tl-sub">You selected {{ $job->acceptedOffer?->serviceProvider?->name ?? 'a provider' }}</div></div>
            </div>
            @foreach([['confirmed',1,'✅','Confirmed','Provider confirmed the job'],['in_progress',2,'🔧','Work In Progress','Provider has started working'],['completed',3,'🎉','Completed','Job finished successfully']] as [$key,$idx,$icon,$label,$sub])
            @php
                $state = $currentIdx > $idx ? 'done' : ($currentIdx === $idx ? 'current' : 'pending');
                $realSub = match($key) {
                    'in_progress' => $job->work_started_at ? $job->work_started_at->format('M d, Y · H:i') : $sub,
                    'completed'   => $job->work_completed_at ? $job->work_completed_at->format('M d, Y · H:i') : $sub,
                    default       => $sub,
                };
            @endphp
            <div class="tl-step {{ $state }}">
                <div class="tl-icon {{ $state }}">{{ $state === 'done' ? '✓' : $icon }}</div>
                <div class="tl-body">
                    <div class="tl-label">{{ $label }}</div>
                    <div class="tl-sub">{{ $realSub }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @if($job->provider_notes)
        <div style="margin-top:1.25rem;padding:1rem;background:rgba(0,212,255,.04);border:1px solid rgba(0,212,255,.12);border-radius:10px;font-size:.85rem;color:var(--text-secondary);line-height:1.6;">
            <strong style="color:var(--accent-cyan);">Provider Note:</strong> {{ $job->provider_notes }}
        </div>
        @endif
        @if($job->final_cost && $ws === 'completed')
        <div style="margin-top:1rem;display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;background:rgba(0,255,170,.06);border:1px solid rgba(0,255,170,.2);border-radius:10px;">
            <span style="font-size:.875rem;color:var(--text-secondary);">Final Amount Charged</span>
            <span style="font-family:'Orbitron',sans-serif;font-size:1.25rem;font-weight:800;color:var(--accent-green);">${{ number_format($job->final_cost, 2) }}</span>
        </div>
        @endif
        @endif
    </div>
    @endif

    {{-- Rating Section (after completed, before rated) --}}
    @if($job->work_status === 'completed' && is_null($job->rating))
    <div class="rating-card">
        <div style="font-family:'Orbitron',sans-serif;font-size:1rem;font-weight:700;margin-bottom:.5rem;">⭐ Rate Your Experience</div>
        <div style="font-size:.875rem;color:var(--text-secondary);margin-bottom:1.5rem;">How was {{ $job->acceptedOffer?->serviceProvider?->name ?? 'the provider' }}'s work?</div>
        <form action="{{ route('jobs.rate', $job) }}" method="POST">
            @csrf
            <div class="stars" id="starRating">
                @for($i = 1; $i <= 5; $i++)
                <span class="star" onclick="setRating({{ $i }})">★</span>
                @endfor
            </div>
            <input type="hidden" name="rating" id="ratingInput" value="0">
            <div style="margin-bottom:1rem;">
                <textarea name="review" class="form-textarea" placeholder="Share your experience with this provider... (optional)"></textarea>
            </div>
            <button type="submit" class="btn-submit" onclick="return document.getElementById('ratingInput').value > 0 || (alert('Please select a star rating'), false)">
                Submit Review ★
            </button>
        </form>
    </div>
    @elseif($job->rating)
    <div style="background:rgba(255,170,0,.06);border:1px solid rgba(255,170,0,.2);border-radius:14px;padding:1.5rem;margin-bottom:1.5rem;">
        <div style="font-family:'Orbitron',sans-serif;font-size:.9rem;font-weight:700;margin-bottom:.875rem;">Your Review</div>
        <div style="display:flex;align-items:center;gap:.375rem;margin-bottom:.625rem;">
            @for($i=1;$i<=5;$i++)
            <svg width="20" height="20" fill="{{ $i<=$job->rating ? '#ffaa00' : 'rgba(255,255,255,.15)' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            @endfor
            <span style="font-family:'Orbitron',sans-serif;font-weight:700;color:#ffaa00;margin-left:.25rem;">{{ $job->rating }}/5</span>
        </div>
        @if($job->review)<p style="font-size:.875rem;color:var(--text-secondary);font-style:italic;line-height:1.6;">"{{ $job->review }}"</p>@endif
    </div>
    @endif

    <div class="main-layout">
        {{-- Offers Section --}}
        <div>
            @if($acceptedOffer && ($job->status === 'accepted' || $job->status === 'completed'))
            <div class="accepted-banner">
                <div style="font-size:2rem;margin-bottom:.5rem;">🎯</div>
                <h3 style="font-family:'Orbitron',sans-serif;font-size:1rem;color:var(--accent-green);margin-bottom:.5rem;">{{ $acceptedOffer->serviceProvider->name }} is on the job</h3>
                <p style="color:var(--text-secondary);font-size:.875rem;">Offered price: <strong style="color:var(--accent-warning);">${{ number_format($acceptedOffer->offered_price, 2) }}</strong> · {{ $job->workStatusLabel() }}</p>
            </div>
            @endif

            <div class="card" id="offersCard">
                <div class="card-title">
                    Offers Received
                    <span id="offerCount" style="font-family:'Chakra Petch',sans-serif;font-size:.8rem;font-weight:400;color:var(--text-tertiary);">({{ $pendingOffers->count() + ($acceptedOffer?1:0) + $rejectedOffers->count() }})</span>
                </div>

                @if($job->isOpen() && $pendingOffers->isEmpty() && !$acceptedOffer)
                <div class="waiting-hint" id="waitingHint">
                    <div class="spinner"></div>
                    <p>Waiting for providers to send offers...<br><small style="color:var(--text-tertiary);">Nearby providers have been notified</small></p>
                </div>
                @endif

                {{-- Accepted offer shown first --}}
                @if($acceptedOffer)
                @php $prov = $acceptedOffer->serviceProvider; @endphp
                <div class="offer-card winner">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1rem;">
                        <div>
                            <div style="font-size:.72rem;color:var(--accent-green);font-weight:700;margin-bottom:.35rem;">✓ ACCEPTED OFFER</div>
                            <div class="provider-name">{{ $prov->name }}</div>
                            <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-top:.35rem;">
                                @if($prov->is_verified)<span class="provider-badge badge-verified">✓ Verified</span>@endif
                                <span class="provider-badge badge-type">{{ ucfirst(str_replace('_',' ',$prov->type)) }}</span>
                                @if($prov->rating > 0)<span style="font-size:.72rem;color:var(--accent-warning);">★ {{ number_format($prov->rating,1) }}</span>@endif
                            </div>
                        </div>
                        <div class="offer-price">${{ number_format($acceptedOffer->offered_price,2) }}</div>
                    </div>
                    @if($acceptedOffer->message)<div class="offer-message">"{{ $acceptedOffer->message }}"</div>@endif
                    <div style="margin-top:.875rem;">
                        <a href="{{ route('providers.show', $prov) }}" class="btn-profile">View Profile →</a>
                    </div>
                </div>
                @endif

                {{-- Pending offers (only visible before acceptance) --}}
                <div id="pendingOffersList">
                @foreach($pendingOffers as $offer)
                @php $prov = $offer->serviceProvider; @endphp
                <div class="offer-card" id="offer-{{ $offer->id }}">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:.875rem;">
                        <div>
                            <div class="provider-name">{{ $prov->name }}</div>
                            <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-top:.35rem;">
                                @if($prov->is_verified)<span class="provider-badge badge-verified">✓ Verified</span>@endif
                                <span class="provider-badge badge-type">{{ ucfirst(str_replace('_',' ',$prov->type)) }}</span>
                                @if($prov->rating > 0)<span style="font-size:.72rem;color:var(--accent-warning);">★ {{ number_format($prov->rating,1) }} ({{ $prov->total_reviews }})</span>@endif
                            </div>
                            @if(isset($offer->distance))<div style="font-size:.78rem;color:var(--text-tertiary);margin-top:.35rem;">📍 {{ $offer->distance }} miles away</div>@endif
                        </div>
                        <div class="offer-price">${{ number_format($offer->offered_price,2) }}</div>
                    </div>
                    <div class="offer-details">
                        <div>📅 {{ \Carbon\Carbon::parse($offer->available_date)->format('M d, Y') }}</div>
                        <div>🕐 {{ $offer->available_time }}</div>
                        @if($offer->estimated_duration)<div>⏱ ~{{ $offer->estimated_duration }} min</div>@endif
                        <div>📍 {{ $prov->city }}, {{ $prov->state }}</div>
                    </div>
                    @if($offer->message)<div class="offer-message">"{{ $offer->message }}"</div>@endif
                    @if($job->isOpen())
                    <div style="margin-top:1.25rem;display:flex;gap:.75rem;align-items:center;flex-wrap:wrap;">
                        <form method="POST" action="{{ route('jobs.accept-offer', [$job, $offer]) }}">@csrf
                            <button type="submit" class="btn-accept" onclick="return confirm('Accept {{ $prov->name }}\'s offer of ${{ number_format($offer->offered_price,2) }}?')">✓ Accept This Offer</button>
                        </form>
                        <a href="{{ route('providers.show', $prov) }}" class="btn-profile">View Profile →</a>
                    </div>
                    @endif
                </div>
                @endforeach
                </div>{{-- #pendingOffersList --}}

                @if($rejectedOffers->isNotEmpty())
                <details style="margin-top:1rem;">
                    <summary style="cursor:pointer;font-size:.825rem;color:var(--text-tertiary);list-style:none;display:flex;align-items:center;gap:.5rem;user-select:none;">
                        ▾ {{ $rejectedOffers->count() }} other {{ $rejectedOffers->count()===1?'offer':'offers' }} (not selected)
                    </summary>
                    <div style="margin-top:1rem;">
                        @foreach($rejectedOffers as $offer)
                        <div class="offer-card rejected" style="padding:1rem;margin-bottom:.5rem;">
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <div class="provider-name" style="font-size:.875rem;">{{ $offer->serviceProvider->name }}</div>
                                <div style="font-family:'Orbitron',sans-serif;font-size:1rem;color:var(--text-tertiary);">${{ number_format($offer->offered_price,2) }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </details>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div>
            <div class="sidebar-card">
                <h3>Job Details</h3>
                <div class="kv"><span class="k">Job #</span><span class="v">{{ $job->job_number }}</span></div>
                <div class="kv"><span class="k">Service</span><span class="v">{{ $job->service_type }}</span></div>
                <div class="kv"><span class="k">Vehicle</span><span class="v">{{ $job->vehicle->year }} {{ $job->vehicle->make }} {{ $job->vehicle->model }}</span></div>
                <div class="kv"><span class="k">Budget</span><span class="v">{{ $job->budgetLabel() }}</span></div>
                @if($job->preferred_date)<div class="kv"><span class="k">Preferred Date</span><span class="v">{{ \Carbon\Carbon::parse($job->preferred_date)->format('M d, Y') }}</span></div>@endif
                <div class="kv"><span class="k">Status</span><span class="v">{{ ucfirst($job->status) }}</span></div>
                @if(in_array($job->status,['accepted','completed']))<div class="kv"><span class="k">Work Status</span><span class="v">{{ $job->workStatusLabel() }}</span></div>@endif
                @if($job->final_cost)<div class="kv"><span class="k">Final Cost</span><span class="v" style="color:var(--accent-green);font-family:'Orbitron',sans-serif;">${{ number_format($job->final_cost,2) }}</span></div>@endif
                <div class="kv"><span class="k">Offers</span><span class="v">{{ $job->offers->count() }}</span></div>
            </div>

            <div class="sidebar-card">
                <h3 style="color: var(--text-primary);">Description</h3>
                <p style="font-size:.875rem;color:var(--text-secondary);line-height:1.7;">{{ $job->description }}</p>
                @if($job->customer_notes)<p style="font-size:.825rem;color:var(--text-secondary);margin-top:.875rem;padding-top:.875rem;border-top:1px solid var(--border-color);font-style:italic;">{{ $job->customer_notes }}</p>@endif
            </div>

            @if($job->isOpen())
            <div class="sidebar-card">
                <h3>Actions</h3>
                @if($job->expires_at)<div style="background:rgba(255,170,0,.06);border:1px solid rgba(255,170,0,.2);border-radius:10px;padding:.875rem 1rem;font-size:.85rem;color:var(--accent-warning);margin-bottom:1rem;">⏳ Expires {{ $job->expires_at->diffForHumans() }}</div>@endif
                <form method="POST" action="{{ route('jobs.cancel', $job) }}">@csrf
                    <button type="submit" class="btn-danger" onclick="return confirm('Cancel this job post?')">✕ Cancel Job Post</button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function setRating(val) {
    document.getElementById('ratingInput').value = val;
    document.querySelectorAll('#starRating .star').forEach((s, i) => {
        s.classList.toggle('filled', i < val);
    });
}
// Hover preview
document.querySelectorAll('#starRating .star').forEach((s, i) => {
    s.addEventListener('mouseenter', () => {
        document.querySelectorAll('#starRating .star').forEach((x, j) => {
            x.style.color = j <= i ? '#ffaa00' : 'rgba(255,255,255,.15)';
        });
    });
    s.addEventListener('mouseleave', () => {
        const val = parseInt(document.getElementById('ratingInput').value || 0);
        document.querySelectorAll('#starRating .star').forEach((x, j) => {
            x.classList.toggle('filled', j < val);
            x.style.color = '';
        });
    });
});
</script>
<script>
(function() {
    // ── Consumer job page: real-time offer polling ──────────────────────
    // Runs only on open jobs. Polls every 5s for new offers.
    // No WebSocket needed — pure HTTP polling fallback.

    const IS_OPEN  = {{ $job->isOpen() ? 'true' : 'false' }};
    const JOB_ID   = {{ $job->id }};
    const CSRF     = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

    if (!IS_OPEN) return; // job already accepted/cancelled — stop

    // Seed with already-rendered offer IDs so we don't duplicate them
    const seen = new Set([
        @foreach($pendingOffers as $o){{ (int)$o->id }},@endforeach
        @if($acceptedOffer){{ (int)$acceptedOffer->id }},@endif
    ]);

    // ── Toast notification ──────────────────────────────────────────────
    function toast(title, body) {
        const el = document.createElement('div');
        el.style.cssText = 'position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;background:var(--card-bg,#1a2235);border:1px solid rgba(0,212,255,.4);border-radius:14px;padding:1rem 1.25rem;min-width:280px;max-width:360px;box-shadow:0 8px 32px rgba(0,0,0,.4);animation:toastIn .4s ease;';
        el.innerHTML = `<div style="font-weight:700;font-size:.875rem;color:#fff;margin-bottom:.25rem;">${title}</div><div style="font-size:.8rem;color:#aaa;">${body}</div>`;
        document.body.appendChild(el);
        setTimeout(() => { el.style.animation = 'toastOut .4s ease forwards'; setTimeout(() => el.remove(), 400); }, 5000);
    }

    // ── Build offer card HTML ───────────────────────────────────────────
    function buildCard(o) {
        const p = o.provider;
        const badge = p.is_verified ? `<span style="font-size:.72rem;background:rgba(0,212,255,.1);border:1px solid rgba(0,212,255,.3);color:var(--accent-cyan);padding:.15rem .5rem;border-radius:10px;">✓ Verified</span>` : '';
        const stars = p.rating ? `<span style="font-size:.78rem;color:#ffaa00;">★ ${parseFloat(p.rating).toFixed(1)}</span>` : '';
        const url   = o.accept_url || `/jobs/${JOB_ID}/accept-offer/${o.offer_id}`;
        return `<div class="offer-card" id="offer-${o.offer_id}" style="animation:offerSlideIn .5s ease;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1rem;">
                <div>
                    <div style="font-weight:700;font-size:.95rem;color:var(--text-primary);">${p.name}</div>
                    <div style="display:flex;gap:.5rem;align-items:center;margin-top:.35rem;flex-wrap:wrap;">
                        <span style="font-size:.78rem;color:var(--text-tertiary);">${p.type||''}</span>
                        ${badge}${stars}
                        ${p.total_reviews?`<span style="font-size:.72rem;color:var(--text-tertiary);">(${p.total_reviews} reviews)</span>`:''}
                    </div>
                </div>
                <div class="offer-price">$${parseFloat(o.offered_price).toFixed(2)}</div>
            </div>
            <div class="offer-details">
                <div><span style="color:var(--text-tertiary);">Available</span><br><strong>${o.available_date}${o.available_time?' · '+o.available_time:''}</strong></div>
                ${o.estimated_duration?`<div><span style="color:var(--text-tertiary);">Duration</span><br><strong>${o.estimated_duration} min</strong></div>`:''}
            </div>
            ${o.message?`<div class="offer-message">"${o.message}"</div>`:''}
            <div style="margin-top:1rem;">
                <form method="POST" action="${url}">
                    <input type="hidden" name="_token" value="${CSRF}">
                    <button type="submit" style="width:100%;padding:.75rem;background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green));border:none;border-radius:10px;color:#000;font-family:'Orbitron',sans-serif;font-weight:700;font-size:.8rem;cursor:pointer;">✓ Accept Offer</button>
                </form>
            </div>
        </div>`;
    }

    // ── Insert offer into DOM ───────────────────────────────────────────
    function addOffer(o) {
        const id = parseInt(o.offer_id);
        if (seen.has(id)) return;
        seen.add(id);

        // Hide the "waiting" spinner if visible
        const hint = document.getElementById('waitingHint');
        if (hint) hint.style.display = 'none';

        // Ensure the list container exists — create it if not
        let list = document.getElementById('pendingOffersList');
        if (!list) {
            const card = document.querySelector('.card'); // fallback: first card
            list = document.createElement('div');
            list.id = 'pendingOffersList';
            if (card) card.appendChild(list);
            else document.body.appendChild(list);
        }

        list.insertAdjacentHTML('beforeend', buildCard(o));

        // Update the count badge
        const badge = document.getElementById('offerCount');
        if (badge) {
            const cur = parseInt(badge.textContent.replace(/\D/g,'')) || 0;
            badge.textContent = `(${cur + 1})`;
        }

        toast(`💰 New offer from ${o.provider.name}`, `$${parseFloat(o.offered_price).toFixed(2)} — review below`);
    }

    // ── Poll endpoint ───────────────────────────────────────────────────
    let since = Math.floor(Date.now() / 1000) - 15;

    async function poll() {
        try {
            const res = await fetch(`/api/realtime/jobs/${JOB_ID}/offers/live?since=${since}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF }
            });
            if (!res.ok) {
                console.warn('[Offers] poll HTTP', res.status);
                return;
            }
            const data = await res.json();
            if (data.server_time) since = data.server_time - 2;
            (data.new_offers || []).forEach(addOffer);
        } catch (e) {
            console.error('[Offers] poll error', e);
        }
    }

    // Start immediately then every 5s
    poll();
    setInterval(poll, 5000);

    // Pause when tab hidden, resume when visible
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) { since = Math.floor(Date.now()/1000) - 15; poll(); }
    });
})();
</script>
<style>
@keyframes offerSlideIn { from { opacity:0; transform:translateY(-12px); } to { opacity:1; transform:translateY(0); } }
@keyframes toastIn  { from { opacity:0; transform:translateX(20px); } to { opacity:1; transform:translateX(0); } }
@keyframes toastOut { from { opacity:1; transform:translateX(0); } to { opacity:0; transform:translateX(20px); } }
</style>

@endsection