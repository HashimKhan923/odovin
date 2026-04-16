@extends('admin.layouts.app')
@section('title', 'Service Providers')
@push('styles')
<style>
.filter-group{display:flex;flex-direction:column;gap:.35rem;flex:1;min-width:140px;}
.filter-label{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-tertiary);}
.svc-tag{font-size:.65rem;background:rgba(0,212,255,.08);color:#00d4ff;border:1px solid rgba(0,212,255,.2);padding:.15rem .5rem;border-radius:4px;white-space:nowrap;}
.provider-avatar{width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#00d4ff,#00ffaa);display:flex;align-items:center;justify-content:center;color:#000;font-weight:800;font-size:.9rem;flex-shrink:0;}
</style>
@endpush
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Service Providers</div>
        <div class="page-sub">{{ $providers->total() }} providers registered on the platform</div>
    </div>
    <a href="{{ route('admin.providers.create') }}" class="btn btn-primary">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Provider
    </a>
</div>

<form method="GET" class="filter-bar">
    <div class="filter-group">
        <span class="filter-label">Search</span>
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Name, email, phone…"
            class="form-input" style="padding:.55rem .875rem;font-size:.8rem;">
    </div>
    <div class="filter-group" style="min-width:130px;max-width:160px;">
        <span class="filter-label">Status</span>
        <select name="status" class="form-select" style="padding:.55rem .875rem;font-size:.8rem;">
            <option value="">All Status</option>
            <option value="active"    @selected(request('status')==='active')>Active</option>
            <option value="inactive"  @selected(request('status')==='inactive')>Inactive</option>
            <option value="suspended" @selected(request('status')==='suspended')>Suspended</option>
        </select>
    </div>
    <div style="display:flex;align-items:flex-end;gap:.5rem;">
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        @if(request()->hasAny(['search','status','service_type']))
            <a href="{{ route('admin.providers.index') }}" class="btn btn-secondary btn-sm">Clear</a>
        @endif
    </div>
</form>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Provider</th>
                <th>Contact</th>
                <th>Services</th>
                <th>Rating</th>
                <th>Jobs</th>
                <th>Subscription</th>
                <th>Status</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($providers as $provider)
            @php
                $activeSub = \App\Models\ProviderSubscription::where('service_provider_id', $provider->id)
                    ->whereIn('status', ['active','trialing'])
                    ->with('plan')
                    ->latest()
                    ->first();
                $jobCount = \App\Models\ServiceJobPost::where('assigned_provider_id', $provider->id)->count();
            @endphp
            <tr>
                {{-- Provider name + avatar --}}
                <td>
                    <div style="display:flex;align-items:center;gap:.75rem;">
                        <div class="provider-avatar">{{ substr($provider->name ?? 'P', 0, 1) }}</div>
                        <div>
                            <div style="font-weight:600;font-size:.875rem;color:var(--text-primary);">
                                {{ $provider->name }}
                            </div>
                            @if($provider->is_verified)
                                <span style="font-size:.65rem;color:#00d4ff;font-weight:700;">✓ Verified</span>
                            @endif
                        </div>
                    </div>
                </td>

                {{-- Contact --}}
                <td>
                    <div style="font-size:.82rem;color:var(--text-secondary);">{{ $provider->email }}</div>
                    <div style="font-size:.75rem;color:var(--text-tertiary);margin-top:.15rem;">{{ $provider->phone }}</div>
                </td>

                {{-- Services --}}
                <td>
                    <div style="display:flex;flex-wrap:wrap;gap:.3rem;max-width:200px;">
                        @foreach(array_slice((array)($provider->services_offered ?? []), 0, 3) as $svc)
                            <span class="svc-tag">{{ str_replace('_',' ', $svc) }}</span>
                        @endforeach
                        @if(count((array)($provider->services_offered ?? [])) > 3)
                            <span style="font-size:.65rem;color:var(--text-tertiary);">
                                +{{ count((array)$provider->services_offered) - 3 }} more
                            </span>
                        @endif
                        @if(empty($provider->services_offered))
                            <span style="font-size:.75rem;color:var(--text-tertiary);">—</span>
                        @endif
                    </div>
                </td>

                {{-- Rating --}}
                <td>
                    <div style="font-size:.9rem;font-weight:700;color:#ffaa00;">
                        ★ {{ number_format($provider->rating ?? 0, 1) }}
                    </div>
                    <div style="font-size:.72rem;color:var(--text-tertiary);margin-top:.1rem;">
                        {{ $provider->total_reviews ?? 0 }} reviews
                    </div>
                </td>

                {{-- Jobs --}}
                <td>
                    <div style="font-size:.9rem;font-weight:700;color:var(--accent-cyan);">
                        {{ $jobCount }}
                    </div>
                    <div style="font-size:.72rem;color:var(--text-tertiary);margin-top:.1rem;">
                        {{ $provider->bookings_count }} bookings
                    </div>
                </td>

                {{-- Subscription --}}
                <td>
                    @if($activeSub && $activeSub->plan)
                        @php
                            $planColor = match($activeSub->plan->slug) {
                                'premium' => '#a855f7',
                                'pro'     => '#00d4ff',
                                default   => 'var(--text-tertiary)',
                            };
                        @endphp
                        <span style="font-family:'Orbitron',sans-serif;font-size:.72rem;font-weight:700;color:{{ $planColor }};">
                            {{ strtoupper($activeSub->plan->name) }}
                        </span>
                        @if($activeSub->status === 'trialing')
                            <div style="font-size:.65rem;color:#00d4ff;margin-top:.15rem;">trialing</div>
                        @endif
                    @else
                        <span style="font-size:.75rem;color:var(--text-tertiary);">Free</span>
                    @endif
                </td>

                {{-- Status --}}
                <td>
                    @php
                        $statusColor = match($provider->status) {
                            'active'    => ['#00ffaa', 'rgba(0,255,170,.12)', 'rgba(0,255,170,.3)'],
                            'inactive'  => ['#ffaa00', 'rgba(255,170,0,.12)', 'rgba(255,170,0,.3)'],
                            'suspended' => ['#ff3366', 'rgba(255,51,102,.12)', 'rgba(255,51,102,.3)'],
                            default     => ['var(--text-tertiary)', 'rgba(255,255,255,.06)', 'rgba(255,255,255,.1)'],
                        };
                    @endphp
                    <span style="font-size:.7rem;font-weight:700;color:{{ $statusColor[0] }};background:{{ $statusColor[1] }};border:1px solid {{ $statusColor[2] }};padding:.25rem .7rem;border-radius:20px;text-transform:uppercase;letter-spacing:.04em;">
                        {{ ucfirst($provider->status) }}
                    </span>
                </td>

                {{-- Joined --}}
                <td style="font-size:.78rem;color:var(--text-tertiary);">
                    {{ $provider->created_at?->format('M d, Y') }}
                </td>

                {{-- Actions --}}
                <td>
                    <div style="display:flex;gap:.375rem;align-items:center;">
                        <a href="{{ route('admin.providers.show', $provider) }}" class="act act-view">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            View
                        </a>
                        <a href="{{ route('admin.providers.edit', $provider) }}" class="act act-edit">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.providers.toggle-status', $provider) }}">
                            @csrf
                            <button type="submit"
                                class="act {{ $provider->status === 'active' ? 'act-del' : 'act-ok' }}"
                                title="{{ $provider->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                @if($provider->status === 'active')
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    Off
                                @else
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    On
                                @endif
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center;padding:3rem;color:var(--text-tertiary);">
                    <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:block;margin:0 auto .75rem;opacity:.25;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    No service providers found
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($providers->hasPages())
<div style="margin-top:1.5rem;display:flex;justify-content:center;">
    {{ $providers->links() }}
</div>
@endif

@endsection