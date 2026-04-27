```blade
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

    {{-- Back --}}
    <a href="{{ route('service-history.index') }}" class="back-link">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Service History
    </a>

    {{-- Title --}}
    <div class="page-title">
        {{ $record->service_type }} <span>Record</span>
    </div>

    {{-- Subtitle --}}
    <p style="color:var(--text-tertiary);font-size:.825rem;margin-top:.2rem;margin-bottom:1.5rem;">
        {{ $record->vehicle->year }} {{ $record->vehicle->make }} {{ $record->vehicle->model }}
        · {{ $record->service_date?->format('M d, Y') }}

        @if($record->serviceProvider)
            · {{ $record->serviceProvider->business_name ?? $record->serviceProvider->name }}
        @endif
    </p>

    {{-- Service Details --}}
    <div class="card">
        <div class="card-title">🔧 Service Details</div>

        <div class="kv-grid">
            <div class="kv">
                <span class="k">Service Type</span>
                <span class="v">{{ $record->service_type }}</span>
            </div>

            <div class="kv">
                <span class="k">Date</span>
                <span class="v">{{ $record->service_date?->format('M d, Y') }}</span>
            </div>

            <div class="kv">
                <span class="k">Cost</span>
                <span class="v" style="color:var(--accent-warning);font-family:'Orbitron',sans-serif;">
                    {{ $record->cost ? '$'.number_format($record->cost, 2) : '—' }}
                </span>
            </div>

            <div class="kv">
                <span class="k">Mileage</span>
                <span class="v">
                    {{ $record->mileage_at_service ? number_format($record->mileage_at_service).' mi' : '—' }}
                </span>
            </div>

            <div class="kv">
                <span class="k">Provider</span>
                <span class="v">
                    {{ $record->serviceProvider?->business_name ?? $record->serviceProvider?->name ?? '—' }}
                </span>
            </div>

            <div class="kv">
                <span class="k">Invoice #</span>
                <span class="v">{{ $record->invoice_number ?: '—' }}</span>
            </div>

            @if($record->next_service_date || $record->next_service_mileage)
                <div class="kv" style="grid-column:1/-1;">
                    <span class="k">Next Service</span>
                    <span class="v" style="color:var(--accent-green);">

                        @if($record->next_service_date)
                            {{ $record->next_service_date->format('M d, Y') }}
                        @endif

                        @if($record->next_service_mileage)
                            · {{ number_format($record->next_service_mileage) }} mi
                        @endif

                    </span>
                </div>
            @endif
        </div>

        {{-- Description --}}
        <div style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid var(--border-color);">
            <div style="font-size:.72rem;text-transform:uppercase;color:var(--text-tertiary);font-weight:700;margin-bottom:.5rem;">
                Work Performed
            </div>
            <p style="font-size:.875rem;color:var(--text-secondary);line-height:1.7;">
                {{ $record->description }}
            </p>
        </div>

        {{-- Parts --}}
        @if($record->parts_replaced && count((array)$record->parts_replaced))
            <div style="margin-top:1rem;">
                <div style="font-size:.72rem;text-transform:uppercase;color:var(--text-tertiary);font-weight:700;margin-bottom:.5rem;">
                    Parts Replaced
                </div>

                <div class="parts-list">
                    @foreach((array)$record->parts_replaced as $part)
                        <span class="part-tag">{{ $part }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Diagnostics --}}
    @if($record->serviceDiagnostics->isNotEmpty())
        <div class="card" style="border-color:rgba(255,102,0,.2);">

            <div class="card-title" style="border-bottom-color:rgba(255,102,0,.15);">
                Service Diagnostics
                <span style="margin-left:.5rem;background:rgba(255,102,0,.12);border:1px solid rgba(255,102,0,.3);color:#ff9944;font-size:.68rem;padding:.15rem .5rem;border-radius:6px;">
                    {{ $record->serviceDiagnostics->count() }}
                </span>
            </div>

            @foreach($record->serviceDiagnostics as $diag)
                @php
                    $sevColor = ['low'=>'#00ffaa','medium'=>'#ffaa00','high'=>'#ff6600','critical'=>'#ff3366'][$diag->severity] ?? '#888';
                    $sevBg = ['low'=>'rgba(0,255,170,.1)','medium'=>'rgba(255,170,0,.1)','high'=>'rgba(255,102,0,.1)','critical'=>'rgba(255,51,102,.1)'][$diag->severity] ?? 'rgba(136,136,136,.1)';
                    $stColor = ['open'=>'#ff3366','acknowledged'=>'#ffaa00','in_progress'=>'#00d4ff','monitoring'=>'#aa88ff','resolved'=>'#00ffaa','ignored'=>'#888'][$diag->status] ?? '#888';
                @endphp

                <div class="diag-item">

                    @if($diag->is_safety_critical)
                        <div style="color:#ff3366;font-size:.7rem;font-weight:700;">⚠️ SAFETY CRITICAL</div>
                    @endif

                    <div class="diag-title">
                        {{ $diag->title }}
                    </div>

                    <div class="diag-desc">
                        {{ $diag->description }}
                    </div>

                </div>
            @endforeach

        </div>
    @endif

</div>
@endsection
```
