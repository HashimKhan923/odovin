@extends('layouts.app')
@section('title', 'Service Record — ' . $record->service_type)

@section('content')
<style>
.pg { max-width:860px; margin:0 auto; padding:2rem 1.5rem; }
.back-link { display:inline-flex; align-items:center; gap:.5rem; color:var(--accent-cyan); text-decoration:none; font-size:.875rem; font-weight:600; margin-bottom:1.5rem; transition:all .3s; }
.back-link:hover { gap:.875rem; }
.page-title { font-family:'Orbitron',sans-serif; font-size:1.3rem; font-weight:800; margin-bottom:.25rem; }
.page-title span { color:var(--accent-cyan); }
.card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.75rem; margin-bottom:1.25rem; position:relative; overflow:hidden; }
.card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,var(--accent-cyan),var(--accent-green)); }
.card-title { font-family:'Orbitron',sans-serif; font-size:.875rem; font-weight:700; margin-bottom:1.25rem; padding-bottom:.75rem; border-bottom:1px solid var(--border-color); display:flex; align-items:center; gap:.5rem; }
.kv-grid { display:grid; grid-template-columns:1fr 1fr; gap:.875rem; }
.kv { display:flex; flex-direction:column; gap:.2rem; }
.kv .k { font-size:.72rem; text-transform:uppercase; letter-spacing:.06em; color:var(--text-tertiary); font-weight:700; }
.kv .v { font-size:.9rem; font-weight:600; color:var(--text-primary); }
.parts-list { display:flex; flex-wrap:wrap; gap:.4rem; margin-top:.5rem; }
.part-tag { font-size:.75rem; padding:.25rem .625rem; background:rgba(0,212,255,.08); border:1px solid rgba(0,212,255,.15); border-radius:6px; color:var(--accent-cyan); }
.badge { display:inline-flex; align-items:center; font-size:.7rem; font-weight:700; padding:.2rem .6rem; border-radius:6px; white-space:nowrap; }
.diag-item { border:1px solid var(--border-color); border-radius:12px; padding:1.25rem; margin-bottom:.875rem; }
.diag-item:last-child { margin-bottom:0; }
.diag-title { font-weight:700; font-size:.925rem; color:var(--text-primary); }
.diag-meta { font-size:.78rem; color:var(--text-tertiary); margin-top:.2rem; }
.diag-desc { font-size:.825rem; color:var(--text-secondary); line-height:1.65; margin-top:.5rem; }
@media(max-width:600px) { .kv-grid { grid-template-columns:1fr; } }
</style>

<div class="pg">
    <a href="{{ route('service-history.index') }}" class="back-link">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Service History
    </a>

    <div class="page-title">{{ $record->service_type }} <span>Record</span></div>
    <p style="color:var(--text-tertiary);font-size:.825rem;margin-top:.2rem;margin-bottom:1.5rem;">
        {{ $record->vehicle->year }} {{ $record->vehicle->make }} {{ $record->vehicle->model }}
        · {{ $record->service_date?->format('M d, Y') }}
        @if($record->serviceProvider) · {{ $record->serviceProvider->business_name ?? $record->serviceProvider->name }}@endif
    </p>

    {{-- Service Details --}}
    <div class="card">
        <div class="card-title">🔧 Service Details</div>
        <div class="kv-grid">
            <div class="kv"><span class="k">Service Type</span><span class="v">{{ $record->service_type }}</span></div>
            <div class="kv"><span class="k">Date</span><span class="v">{{ $record->service_date?->format('M d, Y') }}</span></div>
            <div class="kv">
                <span class="k">Cost</span>
                <span class="v" style="color:var(--accent-warning);font-family:'Orbitron',sans-serif;">
                    {{ $record->cost ? '$'.number_format($record->cost, 2) : '—' }}
                </span>
            </div>
            <div class="kv"><span class="k">Mileage</span><span class="v">{{ $record->mileage_at_service ? number_format($record->mileage_at_service).' mi' : '—' }}</span></div>
            <div class="kv"><span class="k">Provider</span><span class="v">{{ $record->serviceProvider?->business_name ?? $record->serviceProvider?->name ?? '—' }}</span></div>
            <div class="kv"><span class="k">Invoice #</span><span class="v">{{ $record->invoice_number ?: '—' }}</span></div>
            @if($record->next_service_date || $record->next_service_mileage)
            <div class="kv" style="grid-column:1/-1;">
                <span class="k">Next Service</span>
                <span class="v" style="color:var(--accent-green);">
                    @if($record->next_service_date){{ $record->next_service_date->format('M d, Y') }}@endif
                    @if($record->next_service_mileage) · {{ number_format($record->next_service_mileage) }} mi@endif
                </span>
            </div>
            @endif
        </div>

        <div style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid var(--border-color);">
            <div style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;color:var(--text-tertiary);font-weight:700;margin-bottom:.5rem;">Work Performed</div>
            <p style="font-size:.875rem;color:var(--text-secondary);line-height:1.7;">{{ $record->description }}</p>
        </div>

        @if($record->parts_replaced && count((array)$record->parts_replaced))
        <div style="margin-top:1rem;">
            <div style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;color:var(--text-tertiary);font-weight:700;margin-bottom:.5rem;">Parts Replaced</div>
            <div class="parts-list">
                @foreach((array)$record->parts_replaced as $part)
                <span class="part-tag">{{ $part }}</span>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Service Diagnostics --}}
    @if($record->serviceDiagnostics->isNotEmpty())
    <div class="card" style="border-color:rgba(255,102,0,.2);">
        <div class="card-title" style="border-bottom-color:rgba(255,102,0,.15);">
            <svg width="16" height="16" fill="none" stroke="#ff6600" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Service Diagnostics
            <span style="margin-left:.5rem;background:rgba(255,102,0,.12);border:1px solid rgba(255,102,0,.3);color:#ff9944;font-size:.68rem;padding:.15rem .5rem;border-radius:6px;">
                {{ $record->serviceDiagnostics->count() }} finding{{ $record->serviceDiagnostics->count() > 1 ? 's' : '' }}
            </span>
        </div>

        @foreach($record->serviceDiagnostics as $diag)
        @php
            $sevColor = ['low'=>'#00ffaa','medium'=>'#ffaa00','high'=>'#ff6600','critical'=>'#ff3366'][$diag->severity] ?? '#888';
            $sevBg    = ['low'=>'rgba(0,255,170,.1)','medium'=>'rgba(255,170,0,.1)','high'=>'rgba(255,102,0,.1)','critical'=>'rgba(255,51,102,.1)'][$diag->severity] ?? 'rgba(136,136,136,.1)';
            $stColor  = ['open'=>'#ff3366','acknowledged'=>'#ffaa00','in_progress'=>'#00d4ff','monitoring'=>'#aa88ff','resolved'=>'#00ffaa','ignored'=>'#888'][$diag->status] ?? '#888';
        @endphp
        <div class="diag-item" style="{{ $diag->is_safety_critical ? 'border-color:rgba(255,51,102,.4);' : '' }}">
            @if($diag->is_safety_critical)
            <div style="font-size:.7rem;font-weight:700;color:#ff3366;margin-bottom:.5rem;">⚠️ SAFETY CRITICAL</div>
            @endif
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:.75rem;">
                <div>
                    <div class="diag-title">{{ $diag->category_icon ?? '' }} {{ $diag->title }}</div>
                    <div class="diag-meta">
                        {{ ucfirst(str_replace('_', ' ', $diag->category)) }}
                        @if($diag->location) · {{ $diag->location }}@endif
                    </div>
                </div>
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.35rem;flex-shrink:0;">
                    <span class="badge" style="background:{{ $sevBg }};color:{{ $sevColor }};border:1px solid {{ $sevColor . '40' }};">{{ ucfirst($diag->severity) }}</span>
                    <span class="badge" style="background:{{ $stColor . '18' }};color:{{ $stColor }};border:1px solid {{ $stColor . '40' }};">{{ $diag->status_label ?? ucfirst(str_replace('_',' ',$diag->status)) }}</span>
                </div>
            </div>
            <div class="diag-desc">{{ $diag->description }}</div>
            @if($diag->estimated_cost_min || $diag->estimated_cost_max)
            <div style="font-size:.78rem;color:var(--text-tertiary);margin-top:.625rem;display:flex;align-items:center;gap:.35rem;">
                💰 Est. repair:
                <strong style="color:var(--text-primary);">
                    @if($diag->estimated_cost_min && $diag->estimated_cost_max)
                        ${{ number_format($diag->estimated_cost_min) }} – ${{ number_format($diag->estimated_cost_max) }}
                    @elseif($diag->estimated_cost_min)
                        From ${{ number_format($diag->estimated_cost_min) }}
                    @else
                        Up to ${{ number_format($diag->estimated_cost_max) }}
                    @endif
                </strong>
            </div>
            @endif
            @if($diag->status === 'resolved' && $diag->resolution_notes)
            <div style="margin-top:.625rem;padding:.625rem .875rem;background:rgba(0,255,170,.05);border:1px solid rgba(0,255,170,.15);border-radius:8px;font-size:.8rem;color:var(--text-secondary);">
                <strong style="color:var(--accent-green);">✓ Resolved:</strong> {{ $diag->resolution_notes }}
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection