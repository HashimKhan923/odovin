
@extends('admin.layouts.app')
@section('title','User — '.$user->name)
@section('content')
<a href="{{ route('admin.users.index') }}" class="back-link">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    Back to Users
</a>
<div class="page-header">
    <div style="display:flex;align-items:center;gap:1rem;">
        <div style="width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,var(--accent),var(--accent-alt));
            display:flex;align-items:center;justify-content:center;font-family:'Orbitron',sans-serif;font-weight:900;font-size:1.25rem;">
            {{ substr($user->name,0,1) }}
        </div>
        <div>
            <div class="page-title">{{ $user->name }}</div>
            <div class="page-sub">{{ $user->email }} · Member since {{ $user->created_at->format('M Y') }}</div>
        </div>
    </div>
    <div style="display:flex;gap:.75rem;">
        <a href="{{ route('admin.users.edit',$user) }}" class="btn btn-secondary">Edit User</a>
        <form action="{{ route('admin.users.toggle-status',$user) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn {{ $user->email_verified_at ? 'btn-danger' : 'btn-success' }}">
                {{ $user->email_verified_at ? 'Deactivate' : 'Activate' }}
            </button>
        </form>
    </div>
</div>

<div class="grid-4" style="margin-bottom:1.5rem;">
    <div class="stat-card" style="--accent-color:#a855f7">
        <div class="stat-icon" style="background:rgba(168,85,247,.12);color:#a855f7"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 1h1m8-1h3M4 16h1m12-9l2 2v3h-3M9 6h5"/></svg></div>
        <div class="stat-value">{{ $stats['total_vehicles'] }}</div>
        <div class="stat-label">Vehicles</div>
    </div>
    <div class="stat-card" style="--accent-color:#00d4ff">
        <div class="stat-icon" style="background:rgba(0,212,255,.12);color:#00d4ff"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
        <div class="stat-value">{{ $stats['total_bookings'] }}</div>
        <div class="stat-label">Bookings</div>
    </div>
    <div class="stat-card" style="--accent-color:#00ffaa">
        <div class="stat-icon" style="background:rgba(0,255,170,.12);color:#00ffaa"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <div class="stat-value">${{ number_format($stats['total_expenses'],0) }}</div>
        <div class="stat-label">Total Expenses</div>
    </div>
    <div class="stat-card" style="--accent-color:#ffaa00">
        <div class="stat-icon" style="background:rgba(255,170,0,.12);color:#ffaa00"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <div class="stat-value">{{ $stats['pending_reminders'] }}</div>
        <div class="stat-label">Pending Reminders</div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-title">Account Info</div>
        @foreach(['Name'=>$user->name,'Email'=>$user->email,'Role'=>ucfirst($user->user_type),'Status'=>($user->email_verified_at?'Active':'Inactive'),'Joined'=>$user->created_at->format('M d, Y')] as $k=>$v)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:.75rem 0;border-bottom:1px solid var(--border-color);">
            <span style="font-size:.8rem;color:var(--text-tertiary);">{{ $k }}</span>
            <span style="font-size:.875rem;font-weight:600;">{{ $v }}</span>
        </div>
        @endforeach
    </div>
    <div class="card">
        <div class="card-title">Vehicles</div>
        @forelse($user->vehicles as $v)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:.75rem 0;border-bottom:1px solid var(--border-color);">
            <div>
                <div style="font-size:.875rem;font-weight:600;">{{ $v->year }} {{ $v->make }} {{ $v->model }}</div>
                <div style="font-size:.72rem;color:var(--text-tertiary);">{{ $v->license_plate }}</div>
            </div>
            <a href="{{ route('admin.vehicles.show',$v) }}" class="act act-view">View</a>
        </div>
        @empty
        <div style="text-align:center;padding:2rem;color:var(--text-tertiary);">No vehicles registered</div>
        @endforelse
    </div>
</div>
@endsection
