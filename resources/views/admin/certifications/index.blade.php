@extends('admin.layouts.app')
@section('title', 'Certification Reviews')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Certification Reviews</div>
        <div class="page-sub">Review and verify provider certification documents</div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:1.5rem;">✓ {{ session('success') }}</div>
@endif
@if(session('info'))
<div class="alert alert-info" style="margin-bottom:1.5rem;">ℹ {{ session('info') }}</div>
@endif

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem;">
    @foreach([
        ['Pending Review', $stats['pending'],  '#ffaa00', 'admin.certifications.index', 'pending'],
        ['Verified',       $stats['approved'], '#00ffaa', 'admin.certifications.index', 'approved'],
        ['Rejected',       $stats['rejected'], '#ff8099', 'admin.certifications.index', 'rejected'],
        ['Total',          $stats['total'],    '#00d4ff', 'admin.certifications.index', ''],
    ] as [$label, $count, $color, $route, $status])
    <a href="{{ route($route, $status ? ['status' => $status] : []) }}"
       style="background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:1.1rem 1.25rem;text-align:center;text-decoration:none;display:block;transition:border-color .2s;"
       onmouseover="this.style.borderColor='{{ $color }}44'" onmouseout="this.style.borderColor='var(--border-color)'">
        <div style="font-family:'Orbitron',sans-serif;font-size:1.75rem;font-weight:900;color:{{ $color }};">{{ $count }}</div>
        <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:.07em;color:var(--text-tertiary);margin-top:.2rem;">{{ $label }}</div>
    </a>
    @endforeach
</div>

{{-- Filter --}}
<div style="display:flex;gap:.5rem;margin-bottom:1.25rem;flex-wrap:wrap;">
    @foreach(['' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $val => $label)
    <a href="{{ route('admin.certifications.index', $val ? ['status' => $val] : []) }}"
       class="btn {{ request('status') === $val || (!request('status') && $val === '') ? 'btn-primary' : 'btn-secondary' }} btn-sm">
       {{ $label }}
    </a>
    @endforeach
</div>

{{-- Table --}}
<div class="card">
    <div class="card-title" style="display:flex;justify-content:space-between;align-items:center;">
        <span>Certifications</span>
        @if(request('status') === 'pending')
        <span style="font-size:.75rem;color:var(--accent-warning);font-weight:600;">⚡ {{ $stats['pending'] }} awaiting review</span>
        @endif
    </div>
    @if($certifications->isEmpty())
    <div style="text-align:center;padding:4rem 2rem;color:var(--text-tertiary);font-size:.875rem;">No certifications found.</div>
    @else
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:.82rem;">
            <thead>
                <tr style="border-bottom:1px solid var(--border-color);">
                    @foreach(['Provider','Certification','Issuer','Submitted','Status','Action'] as $h)
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.7rem;text-transform:uppercase;letter-spacing:.07em;color:var(--text-tertiary);white-space:nowrap;">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($certifications as $cert)
                <tr style="border-bottom:1px solid rgba(0,212,255,.04);" onmouseover="this.style.background='rgba(0,212,255,.03)'" onmouseout="this.style.background=''">
                    <td style="padding:.875rem 1rem;">
                        <div style="font-weight:700;color:var(--text-primary);">{{ $cert->provider->business_name }}</div>
                        <div style="font-size:.75rem;color:var(--text-tertiary);">{{ $cert->provider->city }}, {{ $cert->provider->state }}</div>
                    </td>
                    <td style="padding:.875rem 1rem;">
                        <div style="font-weight:600;color:var(--text-primary);">{{ $cert->name }}</div>
                        @if($cert->certificate_number)<div style="font-size:.75rem;color:var(--text-tertiary);">#{{ $cert->certificate_number }}</div>@endif
                    </td>
                    <td style="padding:.875rem 1rem;color:var(--text-secondary);">{{ $cert->issuing_body }}</td>
                    <td style="padding:.875rem 1rem;color:var(--text-tertiary);white-space:nowrap;">{{ $cert->created_at->format('M d, Y') }}</td>
                    <td style="padding:.875rem 1rem;">
                        <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.2rem .625rem;border-radius:12px;font-size:.72rem;font-weight:700;background:{{ $cert->statusColor() }}1a;color:{{ $cert->statusColor() }};border:1px solid {{ $cert->statusColor() }}33;">
                            {{ $cert->statusLabel() }}
                        </span>
                    </td>
                    <td style="padding:.875rem 1rem;">
                        <a href="{{ route('admin.certifications.show', $cert) }}"
                           style="display:inline-flex;align-items:center;gap:.35rem;padding:.4rem .875rem;background:rgba(0,212,255,.08);border:1px solid rgba(0,212,255,.2);border-radius:8px;color:var(--accent-cyan);font-size:.75rem;text-decoration:none;">
                            {{ $cert->isPending() ? '🔍 Review' : 'View' }} →
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top:1.25rem;">{{ $certifications->links() }}</div>
    @endif
</div>
@endsection