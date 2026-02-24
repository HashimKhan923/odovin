@extends('provider.layouts.app')
@section('title', 'Booking ' . $booking->booking_number)

@push('styles')
<style>
.back-link { display:inline-flex; align-items:center; gap:.5rem; color:var(--accent-cyan); text-decoration:none; font-size:.875rem; font-weight:600; margin-bottom:1.5rem; transition:all .3s; }
.back-link:hover { gap:.875rem; }
.booking-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:2rem; flex-wrap:wrap; gap:1rem; }
.booking-num { font-family:'Orbitron',sans-serif; font-size:.8rem; color:var(--accent-cyan); margin-bottom:.375rem; }
.booking-title { font-family:'Orbitron',sans-serif; font-size:1.875rem; font-weight:800; }
.grid-2 { display:grid; grid-template-columns:1fr 380px; gap:1.5rem; }
.card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.75rem; margin-bottom:1.5rem; }
.card-title { font-family:'Orbitron',sans-serif; font-size:1rem; font-weight:700; margin-bottom:1.25rem; padding-bottom:.75rem; border-bottom:1px solid var(--border-color); }
.detail-row { display:flex; justify-content:space-between; align-items:flex-start; padding:.75rem 0; border-bottom:1px solid rgba(0,212,255,.05); }
.detail-row:last-child { border-bottom:none; }
.detail-label { font-size:.8rem; color:var(--text-tertiary); text-transform:uppercase; letter-spacing:.05em; font-weight:600; }
.detail-value { font-size:.875rem; color:var(--text-primary); text-align:right; max-width:60%; }
.status-pill { padding:.375rem 1rem; border-radius:20px; font-size:.8rem; font-weight:700; text-transform:uppercase; }
.pill-pending    { background:rgba(255,170,0,.15); color:#ffaa00; border:1px solid rgba(255,170,0,.3); }
.pill-confirmed  { background:rgba(0,212,255,.15); color:#00d4ff; border:1px solid rgba(0,212,255,.3); }
.pill-in_progress{ background:rgba(168,85,247,.15); color:#a855f7; border:1px solid rgba(168,85,247,.3); }
.pill-completed  { background:rgba(0,255,170,.15); color:#00ffaa; border:1px solid rgba(0,255,170,.3); }
.pill-cancelled  { background:rgba(255,51,102,.15); color:#ff3366; border:1px solid rgba(255,51,102,.3); }
.action-card { position:sticky; top:5rem; }
.form-group { margin-bottom:1.25rem; }
.form-label { display:block; font-size:.8rem; font-weight:600; color:var(--text-secondary); margin-bottom:.5rem; }
.form-select, .form-input, .form-textarea { width:100%; padding:.75rem 1rem; background:var(--input-bg); border:1px solid var(--border-color); border-radius:10px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.875rem; transition:all .3s; }
.form-select:focus, .form-input:focus, .form-textarea:focus { outline:none; border-color:var(--accent-cyan); box-shadow:0 0 0 3px rgba(0,212,255,.1); }
.form-select option { background:rgba(18,24,39,1); }
.form-textarea { resize:vertical; min-height:90px; }
.btn-update { width:100%; padding:1rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:12px; color:#000; font-family:'Orbitron',sans-serif; font-weight:800; font-size:.875rem; letter-spacing:.05em; cursor:pointer; transition:all .3s; }
.btn-update:hover { transform:translateY(-2px); box-shadow:0 6px 25px rgba(0,212,255,.4); }
.flow-steps { display:flex; gap:.5rem; margin-bottom:2rem; flex-wrap:wrap; }
.flow-step { flex:1; min-width:70px; padding:.5rem; text-align:center; border-radius:8px; font-size:.65rem; font-weight:700; text-transform:uppercase; border:1px solid var(--border-color); color:var(--text-tertiary); }
.flow-step.current { border-color:var(--accent-cyan); color:var(--accent-cyan); background:rgba(0,212,255,.1); }
.flow-step.done { border-color:var(--accent-green); color:var(--accent-green); background:rgba(0,255,170,.08); }
@media(max-width:900px) { .grid-2 { grid-template-columns:1fr; } .action-card { position:relative; top:0; } }
</style>
@endpush

@section('content')

<a href="{{ route('provider.bookings.index') }}" class="back-link">
    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    Back to Bookings
</a>

<div class="booking-header">
    <div>
        <div class="booking-num">{{ $booking->booking_number }}</div>
        <div class="booking-title">{{ $booking->service_type }}</div>
    </div>
    <span class="status-pill pill-{{ $booking->status }}">{{ str_replace('_',' ',$booking->status) }}</span>
</div>

@php
    $steps   = ['pending' => 0, 'confirmed' => 1, 'in_progress' => 2, 'completed' => 3];
    $current = $steps[$booking->status] ?? -1;
    $labels  = ['Pending','Confirmed','In Progress','Completed'];
@endphp
<div class="flow-steps">
    @foreach($labels as $i => $label)
    <div class="flow-step {{ $i < $current ? 'done' : ($i === $current ? 'current' : '') }}">{{ $label }}</div>
    @endforeach
</div>

<div class="grid-2">
    <div>
        <div class="card">
            <div class="card-title">Booking Details</div>
            <div class="detail-row"><div class="detail-label">Service</div><div class="detail-value">{{ $booking->service_type }}</div></div>
            <div class="detail-row"><div class="detail-label">Scheduled</div><div class="detail-value">{{ $booking->scheduled_date->format('M d, Y · H:i') }}</div></div>
            <div class="detail-row"><div class="detail-label">Status</div><div class="detail-value"><span class="status-pill pill-{{ $booking->status }}">{{ str_replace('_',' ',$booking->status) }}</span></div></div>
            <div class="detail-row"><div class="detail-label">Est. Cost</div><div class="detail-value">{{ $booking->estimated_cost ? '$'.number_format($booking->estimated_cost,2) : 'Not set' }}</div></div>
            <div class="detail-row"><div class="detail-label">Final Cost</div><div class="detail-value">{{ $booking->final_cost ? '$'.number_format($booking->final_cost,2) : 'Not yet set' }}</div></div>
            <div class="detail-row"><div class="detail-label">Booked On</div><div class="detail-value">{{ $booking->created_at->format('M d, Y') }}</div></div>
            @if($booking->description)
            <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border-color);">
                <div class="detail-label" style="margin-bottom:.5rem;">Description</div>
                <div style="font-size:.875rem;color:var(--text-secondary);line-height:1.6;">{{ $booking->description }}</div>
            </div>
            @endif
            @if($booking->customer_notes)
            <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border-color);">
                <div class="detail-label" style="margin-bottom:.5rem;">Customer Notes</div>
                <div style="font-size:.875rem;color:var(--text-secondary);background:rgba(255,170,0,.06);border:1px solid rgba(255,170,0,.2);border-radius:8px;padding:.75rem;line-height:1.6;">{{ $booking->customer_notes }}</div>
            </div>
            @endif
        </div>

        <div class="card">
            <div class="card-title">Vehicle</div>
            @if($booking->vehicle)
            <div class="detail-row"><div class="detail-label">Vehicle</div><div class="detail-value">{{ $booking->vehicle->full_name }}</div></div>
            <div class="detail-row"><div class="detail-label">Year</div><div class="detail-value">{{ $booking->vehicle->year }}</div></div>
            <div class="detail-row"><div class="detail-label">Color</div><div class="detail-value">{{ $booking->vehicle->color ?? 'N/A' }}</div></div>
            <div class="detail-row"><div class="detail-label">License Plate</div><div class="detail-value">{{ $booking->vehicle->license_plate ?? 'N/A' }}</div></div>
            <div class="detail-row"><div class="detail-label">Mileage</div><div class="detail-value">{{ $booking->vehicle->current_mileage ? number_format($booking->vehicle->current_mileage).' mi' : 'N/A' }}</div></div>
            @else
            <p style="color:var(--text-tertiary);font-size:.875rem;">Vehicle info unavailable</p>
            @endif
        </div>

        <div class="card">
            <div class="card-title">Customer</div>
            @if($booking->user)
            <div class="detail-row"><div class="detail-label">Name</div><div class="detail-value">{{ $booking->user->name }}</div></div>
            <div class="detail-row"><div class="detail-label">Email</div><div class="detail-value"><a href="mailto:{{ $booking->user->email }}" style="color:var(--accent-cyan);">{{ $booking->user->email }}</a></div></div>
            @endif
            @if($booking->rating)
            <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border-color);">
                <div class="detail-label" style="margin-bottom:.5rem;">Their Rating</div>
                <div style="display:flex;align-items:center;gap:.5rem;">
                    @for($i=1;$i<=5;$i++)<svg width="18" height="18" fill="{{ $i<=$booking->rating ? '#ffaa00' : 'rgba(255,255,255,.2)' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>@endfor
                    <span style="font-family:'Orbitron',sans-serif;font-weight:700;color:#ffaa00;">{{ $booking->rating }}/5</span>
                </div>
                @if($booking->review)<div style="margin-top:.75rem;font-size:.875rem;color:var(--text-secondary);font-style:italic;">"{{ $booking->review }}"</div>@endif
            </div>
            @endif
        </div>
    </div>

    <div class="action-card">
        <div class="card">
            <div class="card-title">Update Status</div>
            @if(!in_array($booking->status, ['cancelled','completed']))
            <form action="{{ route('provider.bookings.update-status', $booking) }}" method="POST">
                @csrf @method('PUT')
                <div class="form-group">
                    <label class="form-label">New Status</label>
                    <select name="status" class="form-select" required>
                        @foreach(['pending','confirmed','in_progress','completed','cancelled'] as $s)
                        <option value="{{ $s }}" {{ $booking->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Final Cost (when completing)</label>
                    <input type="number" name="final_cost" class="form-input" step="0.01" min="0" placeholder="0.00" value="{{ $booking->final_cost }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Notes to Customer</label>
                    <textarea name="provider_notes" class="form-textarea" placeholder="Any notes for the customer...">{{ $booking->provider_notes }}</textarea>
                </div>
                <button type="submit" class="btn-update">Update Booking</button>
            </form>
            @else
            <div style="padding:1rem;background:rgba(0,255,170,.06);border:1px solid rgba(0,255,170,.2);border-radius:10px;font-size:.875rem;color:var(--text-secondary);text-align:center;">
                Booking is {{ $booking->status }}. No further changes needed.
            </div>
            @if($booking->provider_notes)
            <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border-color);">
                <div class="form-label">Your Notes</div>
                <div style="font-size:.875rem;color:var(--text-secondary);margin-top:.5rem;">{{ $booking->provider_notes }}</div>
            </div>
            @endif
            @endif
        </div>

        @if($booking->status === 'pending')
        <div style="background:rgba(255,170,0,.06);border:1px solid rgba(255,170,0,.25);border-radius:12px;padding:1.25rem;">
            <div style="font-size:.8rem;font-weight:700;color:#ffaa00;margin-bottom:.5rem;">⚡ Action Required</div>
            <div style="font-size:.8rem;color:var(--text-secondary);line-height:1.6;">Customer is waiting. Confirm to proceed or cancel if unavailable.</div>
        </div>
        @endif
    </div>
</div>

@endsection