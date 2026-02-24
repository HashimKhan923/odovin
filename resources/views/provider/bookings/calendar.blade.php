@extends('provider.layouts.app')
@section('title', 'Booking Calendar')

@push('styles')
<style>
.cal-nav { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; }
.cal-title { font-family:'Orbitron',sans-serif; font-size:1.25rem; font-weight:800; }
.cal-btns { display:flex; gap:.75rem; }
.cal-btn { padding:.625rem 1.25rem; background:var(--card-bg); border:1px solid var(--border-color); border-radius:10px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.875rem; font-weight:600; cursor:pointer; text-decoration:none; transition:all .25s; }
.cal-btn:hover { border-color:var(--accent-cyan); color:var(--accent-cyan); }
.cal-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:.5rem; }
.cal-header { background:rgba(0,212,255,.06); border:1px solid var(--border-color); border-radius:8px; padding:.625rem; text-align:center; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--text-tertiary); }
.cal-day { min-height:100px; background:var(--card-bg); border:1px solid var(--border-color); border-radius:10px; padding:.625rem; transition:all .25s; }
.cal-day.today { border-color:var(--accent-cyan); background:rgba(0,212,255,.06); }
.cal-day.other-month { opacity:.35; }
.cal-day-num { font-size:.8rem; font-weight:700; margin-bottom:.375rem; }
.today-num { color:var(--accent-cyan); }
.cal-event { font-size:.65rem; padding:.25rem .5rem; border-radius:6px; margin-bottom:.25rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; font-weight:600; text-decoration:none; display:block; }
.event-pending    { background:rgba(255,170,0,.2);  color:#ffaa00; }
.event-confirmed  { background:rgba(0,212,255,.2);  color:#00d4ff; }
.event-in_progress{ background:rgba(168,85,247,.2); color:#a855f7; }
.event-completed  { background:rgba(0,255,170,.2);  color:#00ffaa; }
.legend { display:flex; gap:1rem; margin-bottom:1rem; flex-wrap:wrap; }
.legend-item { display:flex; align-items:center; gap:.375rem; font-size:.75rem; color:var(--text-secondary); }
.legend-dot { width:10px; height:10px; border-radius:3px; }
</style>
@endpush

@section('content')

<div class="cal-nav">
    <div class="cal-title">{{ $month->format('F Y') }}</div>
    <div class="cal-btns">
        <a href="{{ route('provider.bookings.calendar', ['month' => $month->copy()->subMonth()->format('Y-m')]) }}" class="cal-btn">← Prev</a>
        <a href="{{ route('provider.bookings.calendar') }}" class="cal-btn">Today</a>
        <a href="{{ route('provider.bookings.calendar', ['month' => $month->copy()->addMonth()->format('Y-m')]) }}" class="cal-btn">Next →</a>
    </div>
</div>

<div class="legend">
    <div class="legend-item"><div class="legend-dot" style="background:#ffaa00;"></div>Pending</div>
    <div class="legend-item"><div class="legend-dot" style="background:#00d4ff;"></div>Confirmed</div>
    <div class="legend-item"><div class="legend-dot" style="background:#a855f7;"></div>In Progress</div>
    <div class="legend-item"><div class="legend-dot" style="background:#00ffaa;"></div>Completed</div>
</div>

<div class="cal-grid">
    @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
    <div class="cal-header">{{ $day }}</div>
    @endforeach

    @php
        $startCal = $month->copy()->startOfMonth()->startOfWeek(\Carbon\Carbon::SUNDAY);
        $endCal   = $month->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);
        $today    = \Carbon\Carbon::today();
    @endphp

    @for($day = $startCal->copy(); $day->lte($endCal); $day->addDay())
    @php
        $dayStr      = $day->format('Y-m-d');
        $dayBookings = $bookings[$dayStr] ?? collect();
        $isToday     = $day->isSameDay($today);
        $isThisMonth = $day->month === $month->month;
    @endphp
    <div class="cal-day {{ $isToday ? 'today' : '' }} {{ !$isThisMonth ? 'other-month' : '' }}">
        <div class="cal-day-num {{ $isToday ? 'today-num' : '' }}">{{ $day->day }}</div>
        @foreach($dayBookings->take(3) as $b)
        <a href="{{ route('provider.bookings.show', $b) }}" class="cal-event event-{{ $b->status }}"
           title="{{ $b->service_type }} – {{ $b->scheduled_date->format('H:i') }}">
            {{ $b->scheduled_date->format('H:i') }} {{ Str::limit($b->service_type, 10) }}
        </a>
        @endforeach
        @if($dayBookings->count() > 3)
        <div style="font-size:.6rem;color:var(--text-tertiary);margin-top:.25rem;">+{{ $dayBookings->count()-3 }} more</div>
        @endif
    </div>
    @endfor
</div>

@endsection