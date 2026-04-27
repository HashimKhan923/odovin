@extends('provider.layouts.app')
@section('title', 'Service Record #' . $record->id)

@section('content')
<style>
:root[data-theme="dark"] {
    --card-bg:rgba(26,32,48,.85); --border-color:rgba(0,212,255,.1);
    --input-bg:rgba(0,212,255,.05); --text-primary:#fff;
    --text-secondary:rgba(255,255,255,.7); --text-tertiary:rgba(255,255,255,.45);
    --accent-cyan:#00d4ff; --accent-green:#00ffaa; --accent-warning:#ffaa00;
}
:root[data-theme="light"] {
    --card-bg:rgba(255,255,255,.9); --border-color:rgba(0,0,0,.1);
    --input-bg:rgba(0,0,0,.03); --text-primary:#1a1f36;
    --text-secondary:rgba(26,31,54,.7); --text-tertiary:rgba(26,31,54,.45);
    --accent-cyan:#0066ff; --accent-green:#00cc88; --accent-warning:#ff9500;
}
.pg { max-width:860px; margin:0 auto; padding:2rem 1.5rem; }
.back-link { display:inline-flex; align-items:center; gap:.5rem; color:var(--accent-cyan); text-decoration:none; font-size:.875rem; font-weight:600; margin-bottom:1.5rem; transition:all .3s; }
.back-link:hover { gap:.875rem; }
.page-title { font-family:'Orbitron',sans-serif; font-size:1.4rem; font-weight:800; margin-bottom:.25rem; }
.page-title span { color:var(--accent-cyan); }
/* Cards */
.card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.75rem; margin-bottom:1.25rem; position:relative; overflow:hidden; }
.card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,var(--accent-cyan),var(--accent-green)); }
.card-title { font-family:'Orbitron',sans-serif; font-size:.875rem; font-weight:700; color:var(--text-primary); margin-bottom:1.25rem; padding-bottom:.75rem; border-bottom:1px solid var(--border-color); display:flex; align-items:center; gap:.5rem; }
/* KV grid */
.kv-grid { display:grid; grid-template-columns:1fr 1fr; gap:.875rem; }
.kv { display:flex; flex-direction:column; gap:.2rem; }
.kv .k { font-size:.72rem; text-transform:uppercase; letter-spacing:.06em; color:var(--text-tertiary); font-weight:700; }
.kv .v { font-size:.9rem; font-weight:600; color:var(--text-primary); }
/* Diagnostic items */
.diag-item { border:1px solid var(--border-color); border-radius:12px; padding:1.25rem; margin-bottom:.875rem; transition:border-color .3s; }
.diag-item:last-child { margin-bottom:0; }
.diag-header { display:flex; align-items:flex-start; justify-content:space-between; gap:.75rem; margin-bottom:.625rem; }
.diag-title { font-weight:700; font-size:.925rem; color:var(--text-primary); }
.diag-meta { font-size:.78rem; color:var(--text-tertiary); margin-top:.2rem; }
.badge { display:inline-flex; align-items:center; font-size:.7rem; font-weight:700; padding:.2rem .6rem; border-radius:6px; white-space:nowrap; }
.diag-desc { font-size:.825rem; color:var(--text-secondary); line-height:1.65; margin-top:.5rem; }
.cost-est { display:inline-flex; align-items:center; gap:.35rem; font-size:.78rem; color:var(--text-tertiary); margin-top:.625rem; }
.parts-list { display:flex; flex-wrap:wrap; gap:.4rem; margin-top:.5rem; }
.part-tag { font-size:.75rem; padding:.25rem .625rem; background:rgba(0,212,255,.08); border:1px solid rgba(0,212,255,.15); border-radius:6px; color:var(--accent-cyan); }
.action-row { display:flex; gap:.75rem; flex-wrap:wrap; margin-top:1.5rem; }
.btn-edit { display:inline-flex; align-items:center; gap:.5rem; padding:.75rem 1.5rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:10px; color:#000; font-family:'Orbitron',sans-serif; font-weight:700; font-size:.8rem; cursor:pointer; text-decoration:none; transition:all .3s; }
.btn-edit:hover { transform:translateY(-1px); box-shadow:0 4px 15px rgba(0,212,255,.4); }
.btn-sec { display:inline-flex; align-items:center; gap:.5rem; padding:.75rem 1.25rem; background:transparent; border:1px solid var(--border-color); border-radius:10px; color:var(--text-secondary); font-size:.8rem; text-decoration:none; transition:all .3s; }
.btn-sec:hover { border-color:var(--accent-cyan); color:var(--text-primary); }
@media(max-width:600px) { .kv-grid { grid-template-columns:1fr; } }
</style>

<div class="pg">
    <a href="{{ route('provider.service-records.index') }}" class="back-link">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Service Records
    </a>

    <div class="page-title">Service <span>Record #{{ $record->id }}</span></div>
    <p style="color:var(--text-tertiary);font-size:.825rem;margin-top:.25rem;margin-bottom:1.5rem;">
        {{ $record->service_type }} · {{ $record->service_date?->format('M d, Y') }}
    </p>

    @if(session('success'))
    <div style="background:rgba(0,255,170,.1);border:1px solid rgba(0,255,170,.3);border-radius:10px;padding:.875rem 1.25rem;margin-bottom:1.25rem;color:var(--accent-green);font-size:.875rem;">✓ {{ session('success') }}</div>
    @endif

    {{-- Vehicle & Service ── --}}
    <div class="card">
        <div class="card-title">🚗 Vehicle & Service</div>
        <div class="kv-grid">
            <div class="kv">
                <span class="k">Vehicle</span>
                <span class="v">
                    {{ $record->vehicle?->year }} {{ $record->vehicle?->make }} {{ $record->vehicle?->model }}
                    @if($record->vehicle?->license_plate)
                    <span style="font-size:.78rem;color:var(--text-tertiary);"> · {{ $record->vehicle->license_plate }}</span>
                    @endif
                </span>
            </div>
            <div class="kv">
                <span class="k">Service Type</span>
                <span class="v">{{ $record->service_type }}</span>
            </div>
            <div class="kv">
                <span class="k">Service Date</span>
                <span class="v">{{ $record->service_date?->format('M d, Y') }}</span>
            </div>
            <div class="kv">
                <span class="k">Mileage at Service</span>
                <span class="v">{{ $record->mileage_at_service ? number_format($record->mileage_at_service) . ' mi' : '—' }}</span>
            </div>
            <div class="kv">
                <span class="k">Cost Charged</span>
                <span class="v" style="color:var(--accent-warning);font-family:'Orbitron',sans-serif;">
                    {{ $record->cost ? '$' . number_format($record->cost, 2) : '—' }}
                </span>
            </div>
            <div class="kv">
                <span class="k">Invoice #</span>
                <span class="v">{{ $record->invoice_number ?: '—' }}</span>
            </div>
            @if($record->next_service_date || $record->next_service_mileage)
            <div class="kv">
                <span class="k">Next Service</span>
                <span class="v">
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

        @if($record->notes)
        <div style="margin-top:1rem;padding:.875rem 1rem;background:var(--input-bg);border-radius:10px;font-size:.825rem;color:var(--text-secondary);line-height:1.6;">
            <strong style="color:var(--text-tertiary);font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Internal Notes</strong><br>
            {{ $record->notes }}
        </div>
        @endif
    </div>

    {{-- Service Diagnostics ── --}}
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
            $stColor  = ['open'=>'#ff3366','acknowledged'=>'#ffaa00','in_progress'=>'#00d4ff','monitoring'=>'#aa88ff','resolved'=>'#00ffaa','ignored'=>'#555577'][$diag->status] ?? '#888';
        @endphp
        <div class="diag-item {{ $diag->is_safety_critical ? 'border-red' : '' }}"
             style="{{ $diag->is_safety_critical ? 'border-color:rgba(255,51,102,.4);' : '' }}">
            @if($diag->is_safety_critical)
            <div style="font-size:.7rem;font-weight:700;color:#ff3366;margin-bottom:.5rem;display:flex;align-items:center;gap:.35rem;">
                <span>⚠️</span> SAFETY CRITICAL
            </div>
            @endif
            <div class="diag-header">
                <div>
                    <div class="diag-title">{{ $diag->category_icon }} {{ $diag->title }}</div>
                    <div class="diag-meta">
                        {{ ucfirst(str_replace('_',' ',$diag->category)) }}
                        @if($diag->location) · {{ $diag->location }}@endif
                        · Found {{ $diag->created_at->format('M d, Y') }}
                    </div>
                </div>
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.35rem;">
                    <span class="badge" style="background:{{ $sevBg }};color:{{ $sevColor }};border:1px solid {{ $sevColor . '40' }};">
                        {{ ucfirst($diag->severity) }}
                    </span>
                    <span class="badge" style="background:{{ $stColor . '18' }};color:{{ $stColor }};border:1px solid {{ $stColor . '40' }};">
                        {{ $diag->status_label }}
                    </span>
                </div>
            </div>
            <div class="diag-desc">{{ $diag->description }}</div>
            @if($diag->estimated_cost_min || $diag->estimated_cost_max)
            <div class="cost-est">
                💰 Est. repair: <strong style="color:var(--text-primary);">{{ $diag->cost_range }}</strong>
            </div>
            @endif
            @if($diag->status === 'resolved' && $diag->resolution_notes)
            <div style="margin-top:.625rem;padding:.625rem .875rem;background:rgba(0,255,170,.05);border:1px solid rgba(0,255,170,.15);border-radius:8px;font-size:.8rem;color:var(--text-secondary);">
                <strong style="color:var(--accent-green);">Resolved:</strong> {{ $diag->resolution_notes }}
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <div class="card" style="border-color:rgba(255,102,0,.1);">
        <div class="card-title">
            <svg width="16" height="16" fill="none" stroke="#ff6600" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Service Diagnostics
        </div>
        <p style="font-size:.875rem;color:var(--text-tertiary);text-align:center;padding:1rem 0;">No diagnostic findings logged for this service record.</p>
    </div>
    @endif


    {{-- Actions ── --}}
    <div class="action-row">
<a href="{{ route('provider.service-records.edit', ['serviceRecord' => $record]) }}"> Edit        </a>
        <a href="{{ route('provider.service-diagnostics.index') }}" class="btn-sec">
            View All Diagnostics →
        </a>
        <a href="{{ route('provider.service-records.index') }}" class="btn-sec">
            ← All Records
        </a>
    </div>
</div>
@endif
    @if($record->hasEvidence())
    @include('partials.job_evidence', ['record' => $record])
    @endif


@endsection