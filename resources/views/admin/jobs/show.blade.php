@extends('admin.layouts.app')
@section('title', 'Job #' . $job->job_number)
@section('content')

<a href="{{ route('admin.jobs.index') }}" class="back-link">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    Back to Jobs
</a>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;align-items:start;">

    <div style="display:flex;flex-direction:column;gap:1.5rem;">

        {{-- Job header --}}
        <div class="card">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
                <div>
                    <div style="font-family:'Orbitron',sans-serif;font-size:.7rem;color:var(--accent-cyan);letter-spacing:.1em;margin-bottom:.35rem;">JOB POST</div>
                    <div style="font-size:1.2rem;font-weight:700;">{{ $job->service_type }}</div>
                    <div style="font-family:'Orbitron',sans-serif;font-size:.78rem;color:var(--text-tertiary);margin-top:.2rem;">#{{ $job->job_number }}</div>
                </div>
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.5rem;">
                    <span class="pill pill-{{ $job->status }}" style="font-size:.75rem;padding:.35rem .875rem;">{{ $job->status }}</span>
                    @if($job->work_status)
                    <span class="pill pill-{{ $job->work_status }}" style="font-size:.75rem;padding:.35rem .875rem;">{{ str_replace('_',' ',$job->work_status) }}</span>
                    @endif
                </div>
            </div>
            <hr class="divider">
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.25rem;">
                <div>
                    <div style="font-size:.72rem;color:var(--text-tertiary);margin-bottom:.35rem;">Budget</div>
                    <div style="font-weight:700;color:var(--accent-green);">{{ $job->budgetLabel() }}</div>
                </div>
                <div>
                    <div style="font-size:.72rem;color:var(--text-tertiary);margin-bottom:.35rem;">Posted</div>
                    <div style="font-weight:600;">{{ $job->created_at?->format('M d, Y H:i') }}</div>
                </div>
                <div>
                    <div style="font-size:.72rem;color:var(--text-tertiary);margin-bottom:.35rem;">Expires</div>
                    <div style="font-weight:600;{{ $job->isExpired() ? 'color:#ff3366' : '' }}">
                        {{ $job->expires_at?->format('M d, Y H:i') ?? '—' }}
                        @if($job->isExpired())<span style="font-size:.7rem;color:#ff3366;"> (expired)</span>@endif
                    </div>
                </div>
                @if($job->final_cost)
                <div>
                    <div style="font-size:.72rem;color:var(--text-tertiary);margin-bottom:.35rem;">Final Cost</div>
                    <div style="font-weight:700;color:var(--accent-cyan);">${{ number_format($job->final_cost,2) }}</div>
                </div>
                @endif
                @if($job->location_address)
                <div style="grid-column:span 2;">
                    <div style="font-size:.72rem;color:var(--text-tertiary);margin-bottom:.35rem;">Location</div>
                    <div style="font-size:.875rem;">{{ $job->location_address }}</div>
                </div>
                @endif
            </div>
            @if($job->description)
            <hr class="divider">
            <div style="font-size:.72rem;color:var(--text-tertiary);text-transform:uppercase;letter-spacing:.06em;margin-bottom:.5rem;">Description</div>
            <div style="font-size:.875rem;color:var(--text-secondary);line-height:1.7;">{{ $job->description }}</div>
            @endif
            @if($job->rating)
            <hr class="divider">
            <div style="display:flex;align-items:center;gap:1rem;">
                <div>
                    <div style="font-size:.72rem;color:var(--text-tertiary);margin-bottom:.35rem;">Customer Rating</div>
                    <div style="font-size:1.4rem;font-weight:800;color:#ffaa00;">{{ $job->rating }}/5 ★</div>
                </div>
                @if($job->review)
                <div style="flex:1;font-size:.875rem;color:var(--text-secondary);font-style:italic;">"{{ $job->review }}"</div>
                @endif
            </div>
            @endif
        </div>

        {{-- Offers --}}
        <div class="card">
            <div class="card-title">Offers ({{ $job->offers->count() }})</div>
            @forelse($job->offers as $offer)
            <div style="background:rgba(255,255,255,.03);border:1px solid var(--border-color);border-radius:12px;padding:1rem 1.25rem;margin-bottom:.875rem;">
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
                    <div style="display:flex;align-items:center;gap:.75rem;">
                        <div style="width:36px;height:36px;border-radius:9px;background:linear-gradient(135deg,#00d4ff,#00ffaa);display:flex;align-items:center;justify-content:center;color:#000;font-weight:800;">
                            {{ substr($offer->serviceProvider->name ?? 'P',0,1) }}
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:.875rem;">{{ $offer->serviceProvider->name ?? '—' }}</div>
                            <div style="font-size:.72rem;color:var(--text-tertiary);">{{ $offer->created_at?->diffForHumans() }}</div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:.75rem;">
                        <span style="font-family:'Orbitron',sans-serif;font-size:1.1rem;font-weight:800;color:var(--accent-green);">${{ number_format($offer->offered_price,2) }}</span>
                        <span class="pill pill-{{ $offer->status }}">{{ $offer->status }}</span>
                        @if($job->accepted_offer_id === $offer->id)
                        <span style="font-size:.7rem;font-weight:700;color:#00ffaa;">✓ Accepted</span>
                        @endif
                    </div>
                </div>
                @if($offer->message)
                <div style="margin-top:.75rem;font-size:.8rem;color:var(--text-secondary);padding-left:3rem;">{{ $offer->message }}</div>
                @endif
                @if($offer->hasCounter())
                <div style="margin-top:.75rem;padding:.625rem .875rem;background:rgba(255,170,0,.06);border:1px solid rgba(255,170,0,.2);border-radius:8px;">
                    <div style="font-size:.72rem;font-weight:700;color:#ffaa00;margin-bottom:.3rem;">COUNTER OFFER</div>
                    <div style="display:flex;gap:1.5rem;font-size:.8rem;">
                        <span>Price: <strong style="color:#ffaa00">${{ number_format($offer->counter_price,2) }}</strong></span>
                        <span style="color:var(--text-tertiary);">{{ $offer->negotiationLabel() }}</span>
                    </div>
                    @if($offer->counter_message)
                    <div style="margin-top:.35rem;color:var(--text-secondary);font-size:.78rem;">{{ $offer->counter_message }}</div>
                    @endif
                </div>
                @endif
            </div>
            @empty
            <div style="text-align:center;padding:2rem;color:var(--text-tertiary);">No offers yet</div>
            @endforelse
        </div>

        {{-- Escrow --}}
        @if($job->escrow)
        <div class="card">
            <div class="card-title">Escrow / Payment</div>
            @php $escrow = $job->escrow; @endphp
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1.25rem;">
                <div>
                    <div style="font-size:.72rem;color:var(--text-tertiary);margin-bottom:.35rem;">Total Amount</div>
                    <div style="font-family:'Orbitron',sans-serif;font-size:1.2rem;font-weight:800;color:var(--accent-cyan);">{{ $escrow->formattedAmount() }}</div>
                </div>
                <div>
                    <div style="font-size:.72rem;color:var(--text-tertiary);margin-bottom:.35rem;">Platform Fee</div>
                    <div style="font-family:'Orbitron',sans-serif;font-size:1.2rem;font-weight:800;color:#ffaa00;">${{ number_format($escrow->platform_fee/100,2) }}</div>
                </div>
                <div>
                    <div style="font-size:.72rem;color:var(--text-tertiary);margin-bottom:.35rem;">Provider Receives</div>
                    <div style="font-family:'Orbitron',sans-serif;font-size:1.2rem;font-weight:800;color:var(--accent-green);">${{ number_format($escrow->providerAmount()/100,2) }}</div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
                @php $escolor = match($escrow->status) { 'held'=>'#6772e5','released'=>'#00ffaa','refunded'=>'#ff3366',default=>'#ffaa00' }; @endphp
                <span style="font-size:.75rem;font-weight:700;color:{{ $escolor }};background:{{ $escolor }}22;border:1px solid {{ $escolor }}44;padding:.3rem .875rem;border-radius:20px;">{{ strtoupper($escrow->status) }}</span>
                @if($escrow->isOverdue())<span style="font-size:.75rem;font-weight:700;color:#ff3366;background:rgba(255,51,102,.12);border:1px solid rgba(255,51,102,.3);padding:.3rem .875rem;border-radius:20px;">⚠ OVERDUE</span>@endif
                @if($escrow->stripe_payment_intent_id)<span style="font-size:.75rem;color:var(--text-tertiary);font-family:monospace;">{{ $escrow->stripe_payment_intent_id }}</span>@endif
            </div>
            <div style="margin-top:.875rem;display:flex;gap:1.5rem;font-size:.78rem;color:var(--text-tertiary);">
                @if($escrow->held_at)<span>Held: {{ $escrow->held_at->format('M d, Y') }}</span>@endif
                @if($escrow->released_at)<span>Released: {{ $escrow->released_at->format('M d, Y') }}</span>@endif
                @if($escrow->refunded_at)<span>Refunded: {{ $escrow->refunded_at->format('M d, Y') }}</span>@endif
            </div>
        </div>
        @endif

    </div>

    {{-- Right sidebar --}}
    <div style="display:flex;flex-direction:column;gap:1.5rem;">

        <div class="card">
            <div class="card-title">Customer</div>
            @if($job->user)
            <div style="display:flex;align-items:center;gap:.875rem;margin-bottom:1rem;">
                <div style="width:44px;height:44px;border-radius:11px;background:linear-gradient(135deg,var(--accent),var(--accent-alt));display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1rem;">{{ substr($job->user->name,0,1) }}</div>
                <div>
                    <div style="font-weight:700;">{{ $job->user->name }}</div>
                    <div style="font-size:.78rem;color:var(--text-tertiary);">{{ $job->user->email }}</div>
                </div>
            </div>
            <a href="{{ route('admin.users.show', $job->user) }}" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;">View Profile</a>
            @else<div style="color:var(--text-tertiary);">—</div>@endif
        </div>

        <div class="card">
            <div class="card-title">Vehicle</div>
            @if($job->vehicle)
            <div style="font-weight:700;margin-bottom:.25rem;">{{ $job->vehicle->year }} {{ $job->vehicle->make }} {{ $job->vehicle->model }}</div>
            @if($job->vehicle->vin)<div style="font-size:.72rem;color:var(--text-tertiary);font-family:monospace;margin-top:.2rem;">{{ $job->vehicle->vin }}</div>@endif
            @else<div style="color:var(--text-tertiary);">—</div>@endif
        </div>

        <div class="card">
            <div class="card-title">Assigned Provider</div>
            @if($job->assignedProvider)
            <div style="display:flex;align-items:center;gap:.875rem;margin-bottom:1rem;">
                <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#00d4ff,#00ffaa);display:flex;align-items:center;justify-content:center;color:#000;font-weight:800;">{{ substr($job->assignedProvider->name,0,1) }}</div>
                <div>
                    <div style="font-weight:700;">{{ $job->assignedProvider->name }}</div>
                    <div style="font-size:.75rem;color:var(--text-tertiary);">★ {{ number_format($job->assignedProvider->rating,1) }}</div>
                </div>
            </div>
            <a href="{{ route('admin.providers.show', $job->assignedProvider) }}" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;">View Provider</a>
            @else<div style="color:var(--text-tertiary);font-size:.875rem;">Not assigned yet</div>@endif
        </div>

        @if($job->work_started_at || $job->work_completed_at)
        <div class="card">
            <div class="card-title">Work Timeline</div>
            @if($job->work_started_at)
            <div style="display:flex;gap:.75rem;margin-bottom:.75rem;">
                <div style="width:8px;height:8px;border-radius:50%;background:#a855f7;margin-top:5px;flex-shrink:0;"></div>
                <div><div style="font-size:.8rem;font-weight:600;">Work Started</div><div style="font-size:.75rem;color:var(--text-tertiary);">{{ $job->work_started_at->format('M d, Y H:i') }}</div></div>
            </div>
            @endif
            @if($job->work_completed_at)
            <div style="display:flex;gap:.75rem;">
                <div style="width:8px;height:8px;border-radius:50%;background:#00ffaa;margin-top:5px;flex-shrink:0;"></div>
                <div><div style="font-size:.8rem;font-weight:600;">Completed</div><div style="font-size:.75rem;color:var(--text-tertiary);">{{ $job->work_completed_at->format('M d, Y H:i') }}</div></div>
            </div>
            @endif
        </div>
        @endif

        <div class="card">
            <div class="card-title">Admin Actions</div>
            <div style="display:flex;flex-direction:column;gap:.75rem;">
                <form method="POST" action="{{ route('admin.jobs.force-close', $job) }}" onsubmit="return confirm('Force-close this job?')">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm" style="width:100%;justify-content:center;">Force Close Job</button>
                </form>
                <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}" onsubmit="return confirm('Permanently delete this job?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" style="width:100%;justify-content:center;">Delete Job</button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection