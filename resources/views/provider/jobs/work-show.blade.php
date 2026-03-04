@extends('provider.layouts.app')
@section('title', 'Job ' . $job->job_number)

@section('content')
<style>
.back-link { display:inline-flex; align-items:center; gap:.5rem; color:var(--accent-cyan); text-decoration:none; font-size:.875rem; font-weight:600; margin-bottom:1.5rem; transition:all .3s; }
.back-link:hover { gap:.875rem; }
.job-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:2rem; flex-wrap:wrap; gap:1rem; }
.job-num-lbl { font-family:'Orbitron',sans-serif; font-size:.8rem; color:var(--accent-cyan); margin-bottom:.375rem; }
.job-title { font-family:'Orbitron',sans-serif; font-size:1.75rem; font-weight:800; }
.grid-2 { display:grid; grid-template-columns:1fr 380px; gap:1.5rem; }
.card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.75rem; margin-bottom:1.5rem; }
.card-title { font-family:'Orbitron',sans-serif; font-size:.95rem; font-weight:700; margin-bottom:1.25rem; padding-bottom:.75rem; border-bottom:1px solid var(--border-color); }
.detail-row { display:flex; justify-content:space-between; align-items:flex-start; padding:.75rem 0; border-bottom:1px solid rgba(0,212,255,.05); }
.detail-row:last-child { border-bottom:none; }
.detail-label { font-size:.78rem; color:var(--text-tertiary); text-transform:uppercase; letter-spacing:.05em; font-weight:600; }
.detail-value { font-size:.875rem; color:var(--text-primary); text-align:right; max-width:65%; font-weight:500; }
/* Status pills */
.pill { display:inline-flex; align-items:center; gap:.35rem; padding:.375rem 1rem; border-radius:20px; font-size:.8rem; font-weight:700; }
.pill-pending     { background:rgba(255,170,0,.12); color:#ffaa00; border:1px solid rgba(255,170,0,.3); }
.pill-confirmed   { background:rgba(0,212,255,.12); color:#00d4ff; border:1px solid rgba(0,212,255,.3); }
.pill-in_progress { background:rgba(168,85,247,.12); color:#a855f7; border:1px solid rgba(168,85,247,.3); }
.pill-completed   { background:rgba(0,255,170,.12); color:#00ffaa; border:1px solid rgba(0,255,170,.3); }
.pill-cancelled   { background:rgba(255,51,102,.12); color:#ff3366; border:1px solid rgba(255,51,102,.3); }
/* Progress steps */
.flow-steps { display:flex; gap:.5rem; margin-bottom:2rem; flex-wrap:nowrap; }
.flow-step { flex:1; padding:.625rem .5rem; text-align:center; border-radius:10px; font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; border:1px solid var(--border-color); color:var(--text-tertiary); transition:all .3s; white-space:nowrap; }
.flow-step.done    { border-color:var(--accent-green); color:var(--accent-green); background:rgba(0,255,170,.08); }
.flow-step.current { border-color:var(--accent-cyan); color:var(--accent-cyan); background:rgba(0,212,255,.1); }
/* Sticky sidebar */
.action-card { position:sticky; top:5rem; }
.form-group { margin-bottom:1.25rem; }
.form-label { display:block; font-size:.8rem; font-weight:600; color:var(--text-secondary); margin-bottom:.5rem; }
.form-select, .form-input, .form-textarea { width:100%; padding:.75rem 1rem; background:var(--input-bg,rgba(0,212,255,.05)); border:1px solid var(--border-color); border-radius:10px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.875rem; transition:all .3s; }
.form-select:focus,.form-input:focus,.form-textarea:focus { outline:none; border-color:var(--accent-cyan); box-shadow:0 0 0 3px rgba(0,212,255,.1); }
.form-select option { background:#121827; }
.form-textarea { resize:vertical; min-height:90px; }
.btn-update { width:100%; padding:1rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:12px; color:#000; font-family:'Orbitron',sans-serif; font-weight:800; font-size:.875rem; letter-spacing:.05em; cursor:pointer; transition:all .3s; }
.btn-update:hover { transform:translateY(-2px); box-shadow:0 6px 25px rgba(0,212,255,.4); }
.hint-box { border-radius:12px; padding:1.25rem; font-size:.825rem; line-height:1.6; margin-top:1rem; }
.hint-warning { background:rgba(255,170,0,.06); border:1px solid rgba(255,170,0,.25); color:#ffaa00; }
.hint-success { background:rgba(0,255,170,.06); border:1px solid rgba(0,255,170,.25); color:var(--accent-green); }
.hint-danger  { background:rgba(255,51,102,.06); border:1px solid rgba(255,51,102,.25); color:#ff8099; }
.revenue-banner { background:linear-gradient(135deg,rgba(0,255,170,.1),rgba(0,212,255,.08)); border:1px solid rgba(0,255,170,.3); border-radius:14px; padding:1.5rem; text-align:center; margin-top:1.5rem; }
@media(max-width:900px) { .grid-2{grid-template-columns:1fr} .action-card{position:relative;top:0} }
</style>

<a href="{{ route('provider.jobs.work.index') }}" class="back-link">
    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    Back to Work Queue
</a>

@if(session('success'))
<div style="background:rgba(0,255,170,.1);border:1px solid rgba(0,255,170,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-green);font-size:.875rem;">✓ {{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background:rgba(255,51,102,.1);border:1px solid rgba(255,51,102,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:#ff8099;font-size:.875rem;">{{ session('error') }}</div>
@endif

<div class="job-header">
    <div>
        <div class="job-num-lbl">JOB #{{ $job->job_number }}</div>
        <div class="job-title">{{ $job->service_type }}</div>
    </div>
    <span class="pill pill-{{ $job->work_status ?? 'pending' }}">
        {{ ucfirst(str_replace('_', ' ', $job->work_status ?? 'pending')) }}
    </span>
</div>

{{-- Progress Steps --}}
@php
    $steps   = ['pending' => 0, 'confirmed' => 1, 'in_progress' => 2, 'completed' => 3];
    $current = $steps[$job->work_status ?? 'pending'] ?? 0;
    $labels  = ['⏳ Pending', '✅ Confirmed', '🔧 In Progress', '🎉 Completed'];
@endphp
<div class="flow-steps">
    @foreach($labels as $i => $label)
    <div class="flow-step {{ $i < $current ? 'done' : ($i === $current ? 'current' : '') }}">{{ $label }}</div>
    @endforeach
</div>

<div class="grid-2">
    {{-- Left: Details --}}
    <div>
        <div class="card">
            <div class="card-title">📋 Job Details</div>
            <div class="detail-row"><div class="detail-label">Service</div><div class="detail-value">{{ $job->service_type }}</div></div>
            <div class="detail-row"><div class="detail-label">Your Offered Price</div><div class="detail-value" style="color:var(--accent-warning);font-family:'Orbitron',sans-serif;">${{ number_format($offer->offered_price, 2) }}</div></div>
            @if($job->final_cost)<div class="detail-row"><div class="detail-label">Final Cost</div><div class="detail-value" style="color:var(--accent-green);font-family:'Orbitron',sans-serif;">${{ number_format($job->final_cost, 2) }}</div></div>@endif
            @if($job->preferred_date)<div class="detail-row"><div class="detail-label">Preferred Date</div><div class="detail-value">{{ \Carbon\Carbon::parse($job->preferred_date)->format('M d, Y') }}{{ $job->preferred_time ? ' · '.$job->preferred_time : '' }}</div></div>@endif
            @if($job->work_started_at)<div class="detail-row"><div class="detail-label">Work Started</div><div class="detail-value">{{ $job->work_started_at->format('M d, Y · H:i') }}</div></div>@endif
            @if($job->work_completed_at)<div class="detail-row"><div class="detail-label">Completed At</div><div class="detail-value">{{ $job->work_completed_at->format('M d, Y · H:i') }}</div></div>@endif
            <div class="detail-row"><div class="detail-label">Posted On</div><div class="detail-value">{{ $job->created_at->format('M d, Y') }}</div></div>
            @if($job->description)
            <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border-color);">
                <div class="detail-label" style="margin-bottom:.5rem;">Description</div>
                <div style="font-size:.875rem;color:var(--text-secondary);line-height:1.6;">{{ $job->description }}</div>
            </div>
            @endif
            @if($job->customer_notes)
            <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border-color);">
                <div class="detail-label" style="margin-bottom:.5rem;">Customer Notes</div>
                <div style="font-size:.875rem;color:var(--text-secondary);background:rgba(255,170,0,.06);border:1px solid rgba(255,170,0,.15);border-radius:8px;padding:.75rem;line-height:1.6;">{{ $job->customer_notes }}</div>
            </div>
            @endif
        </div>

        <div class="card">
            <div class="card-title">🚗 Vehicle</div>
            @if($job->vehicle)
            <div class="detail-row"><div class="detail-label">Vehicle</div><div class="detail-value">{{ $job->vehicle->year }} {{ $job->vehicle->make }} {{ $job->vehicle->model }}</div></div>
            @if($job->vehicle->color)<div class="detail-row"><div class="detail-label">Color</div><div class="detail-value">{{ $job->vehicle->color }}</div></div>@endif
            @if($job->vehicle->license_plate)<div class="detail-row"><div class="detail-label">Plate</div><div class="detail-value">{{ $job->vehicle->license_plate }}</div></div>@endif
            @if($job->vehicle->current_mileage)<div class="detail-row"><div class="detail-label">Mileage</div><div class="detail-value">{{ number_format($job->vehicle->current_mileage) }} mi</div></div>@endif
            @endif
        </div>

        <div class="card">
            <div class="card-title">👤 Customer</div>
            @if($job->user)
            <div class="detail-row"><div class="detail-label">Name</div><div class="detail-value">{{ $job->user->name }}</div></div>
            <div class="detail-row"><div class="detail-label">Email</div><div class="detail-value"><a href="mailto:{{ $job->user->email }}" style="color:var(--accent-cyan);">{{ $job->user->email }}</a></div></div>
            @endif
            @if($job->rating)
            <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border-color);">
                <div class="detail-label" style="margin-bottom:.625rem;">Their Review</div>
                <div style="display:flex;align-items:center;gap:.375rem;margin-bottom:.625rem;">
                    @for($i=1;$i<=5;$i++)
                    <svg width="18" height="18" fill="{{ $i<=$job->rating ? '#ffaa00' : 'rgba(255,255,255,.15)' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                    <span style="font-family:'Orbitron',sans-serif;font-size:.875rem;font-weight:700;color:#ffaa00;">{{ $job->rating }}/5</span>
                </div>
                @if($job->review)<div style="font-size:.875rem;color:var(--text-secondary);font-style:italic;line-height:1.6;">"{{ $job->review }}"</div>@endif
            </div>
            @endif
        </div>

        {{-- Revenue record if completed --}}
        @if($job->work_status === 'completed')
        <div class="revenue-banner">
            <div style="font-size:2rem;margin-bottom:.5rem;">💰</div>
            <div style="font-family:'Orbitron',sans-serif;font-size:1.1rem;font-weight:800;color:var(--accent-green);margin-bottom:.5rem;">${{ number_format($job->final_cost, 2) }} Earned</div>
            <div style="font-size:.825rem;color:var(--text-secondary);">Revenue recorded in your service history.</div>
            @if(!$job->rating)<div style="font-size:.8rem;color:var(--text-tertiary);margin-top:.5rem;">Waiting for customer review...</div>@endif
        </div>
        @endif
    </div>

    {{-- Right: Update Status --}}
    <div class="action-card">
        <div class="card">
            <div class="card-title">⚡ Update Status</div>

            @php
                $ws = $job->work_status ?? 'pending';
                $nextOptions = match($ws) {
                    'pending'     => ['confirmed' => '✅ Confirm Job', 'cancelled' => '✕ Cancel'],
                    'confirmed'   => ['in_progress' => '🔧 Start Work', 'cancelled' => '✕ Cancel'],
                    'in_progress' => [],  // completed handled by dedicated form link below
                    default => [],
                };
            @endphp

            @if(!in_array($ws, ['completed','cancelled']))
            <form action="{{ route('provider.jobs.work.update-status', $job) }}" method="POST" id="statusForm">
                @csrf

                {{-- Next status buttons --}}
                <div class="form-group">
                    <label class="form-label">Move to next stage</label>
                    <div style="display:flex;flex-direction:column;gap:.625rem;">
                        @foreach($nextOptions as $val => $label)
                        @php
                            $isCancel = $val === 'cancelled';
                            $btnStyle = $isCancel
                                ? 'background:rgba(255,51,102,.1);border:1px solid rgba(255,51,102,.3);color:#ff8099;'
                                : 'background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green));color:#000;';
                        @endphp
                        <button type="submit" name="work_status" value="{{ $val }}"
                            style="padding:.875rem;border-radius:10px;border:none;font-family:'Orbitron',sans-serif;font-weight:700;font-size:.8rem;cursor:pointer;transition:all .3s;{{ $btnStyle }}"
                            @if($isCancel) onclick="return confirm('Are you sure you want to cancel this job? The customer will be notified.')" @endif
                            @if($val === 'completed') onclick="return validateComplete()" @endif>
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Final cost — only shown when completing --}}
                @if($ws === 'in_progress')
                <div class="form-group" id="finalCostGroup">
                    <label class="form-label">Final Cost (required to complete) <span style="color:#ff8099;">*</span></label>
                    <div style="position:relative;">
                        <span style="position:absolute;left:.875rem;top:50%;transform:translateY(-50%);color:var(--text-tertiary);font-weight:700;">$</span>
                        <input type="number" name="final_cost" id="finalCostInput" class="form-input" style="padding-left:2rem;" step="0.01" min="1" placeholder="0.00" value="{{ $job->final_cost }}">
                    </div>
                </div>
                @endif

                <div class="form-group">
                    <label class="form-label">Notes to Customer (optional)</label>
                    <textarea name="provider_notes" class="form-textarea" placeholder="Any updates for the customer...">{{ $job->provider_notes }}</textarea>
                </div>
            </form>

            @if($ws === 'pending')
            <div class="hint-box hint-warning">
                <strong>⚡ Action Required</strong><br>
                The customer is waiting. Confirm to accept this job or cancel if you're unavailable.
            </div>
            @elseif($ws === 'confirmed')
            <div class="hint-box hint-success">
                <strong>✅ Job Confirmed</strong><br>
                Start the work when you're ready. The customer will be notified when you begin.
            </div>
            @elseif($ws === 'in_progress')
            <a href="{{ route('provider.jobs.work.complete-form', $job) }}"
               style="display:block;width:100%;padding:1rem;background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green));border:none;border-radius:12px;color:#000;font-family:'Orbitron',sans-serif;font-weight:800;font-size:.875rem;letter-spacing:.05em;text-align:center;text-decoration:none;transition:all .3s;margin-bottom:1rem;"
               onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 25px rgba(0,212,255,.4)'"
               onmouseout="this.style.transform='';this.style.boxShadow=''">
                🎉 Complete &amp; Fill Service Record
            </a>
            <div class="hint-box hint-warning">
                <strong>🔧 Work in Progress</strong><br>
                Click above to complete the job and fill in the full service record — mileage, parts, next service, and more — all in one step.
            </div>
            @endif

            @else
            {{-- Completed or Cancelled --}}
            <div class="hint-box {{ $ws === 'completed' ? 'hint-success' : 'hint-danger' }}">
                @if($ws === 'completed')
                <strong>🎉 Job Completed!</strong><br>
                Revenue of ${{ number_format($job->final_cost, 2) }} has been recorded. The customer can now leave a review.
                @else
                <strong>❌ Job Cancelled</strong><br>
                This job has been cancelled. No further updates are possible.
                @endif
            </div>
            @if($job->provider_notes)
            <div style="margin-top:1rem;">
                <div class="form-label">Your Notes</div>
                <div style="font-size:.875rem;color:var(--text-secondary);margin-top:.5rem;line-height:1.6;">{{ $job->provider_notes }}</div>
            </div>
            @endif
            @endif
        </div>

        {{-- Quick summary --}}
        <div class="card">
            <div class="card-title">💼 Job Summary</div>
            <div class="detail-row"><div class="detail-label">Your Offer</div><div class="detail-value" style="color:var(--accent-warning);">${{ number_format($offer->offered_price, 2) }}</div></div>
            <div class="detail-row"><div class="detail-label">Available Date</div><div class="detail-value">{{ $offer->available_date }}</div></div>
            <div class="detail-row"><div class="detail-label">Time</div><div class="detail-value">{{ $offer->available_time }}</div></div>
            @if($offer->estimated_duration)<div class="detail-row"><div class="detail-label">Est. Duration</div><div class="detail-value">{{ $offer->estimated_duration }} min</div></div>@endif
            @if($offer->message)
            <div style="margin-top:.875rem;padding-top:.875rem;border-top:1px solid var(--border-color);">
                <div class="detail-label" style="margin-bottom:.5rem;">Your Message</div>
                <div style="font-size:.825rem;color:var(--text-secondary);font-style:italic;line-height:1.5;">"{{ $offer->message }}"</div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function validateComplete() {
    const fc = document.getElementById('finalCostInput');
    if (!fc) return true;
    const val = parseFloat(fc.value);
    if (!val || val < 1) {
        fc.style.borderColor = '#ff3366';
        fc.focus();
        alert('Please enter the final cost before marking as completed.');
        return false;
    }
    return confirm(`Mark job as completed with a final cost of $${val.toFixed(2)}?`);
}
</script>
@endsection