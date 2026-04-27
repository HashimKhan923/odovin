<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Service History — {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}</title>

<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0d1117; color: #e6edf3; line-height: 1.6; min-height: 100vh; }
.container { max-width: 900px; margin: 0 auto; padding: 2.5rem 1.5rem; }
/* Header */
.report-header { background: linear-gradient(135deg, #161b22, #1c2128); border: 1px solid #30363d; border-radius: 16px; padding: 2rem; margin-bottom: 2rem; position: relative; overflow: hidden; }
.report-header::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #00d4ff, #00ffaa); }
.report-header-top { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem; }
.brand { font-family: 'Orbitron', 'Arial Black', sans-serif; font-size: 1rem; font-weight: 800; color: #00d4ff; letter-spacing: .1em; }
.report-badge { display: inline-flex; align-items: center; gap: .4rem; padding: .3rem .875rem; background: rgba(0,212,255,.1); border: 1px solid rgba(0,212,255,.25); border-radius: 20px; font-size: .72rem; font-weight: 700; color: #00d4ff; }
.vehicle-name { font-size: 1.75rem; font-weight: 800; color: #fff; margin-bottom: .35rem; }
.vehicle-meta { display: flex; gap: 1.5rem; flex-wrap: wrap; font-size: .82rem; color: #8b949e; }
.vehicle-meta span strong { color: #e6edf3; }
/* Stats row */
.stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem; }
@media(max-width:600px) { .stats-row { grid-template-columns: 1fr 1fr; } }
.stat-card { background: #161b22; border: 1px solid #30363d; border-radius: 12px; padding: 1rem; text-align: center; }
.stat-num { font-size: 1.6rem; font-weight: 800; color: #00d4ff; }
.stat-lbl { font-size: .7rem; color: #8b949e; text-transform: uppercase; letter-spacing: .06em; margin-top: .2rem; }
/* Records */
.section-title { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #8b949e; margin-bottom: 1rem; padding-bottom: .5rem; border-bottom: 1px solid #21262d; }
.record-card { background: #161b22; border: 1px solid #30363d; border-radius: 14px; padding: 1.5rem; margin-bottom: 1rem; position: relative; overflow: hidden; }
.record-card::before { content: ''; position: absolute; top: 0; left: 0; bottom: 0; width: 3px; background: #00d4ff; }
.record-header { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: .75rem; margin-bottom: .875rem; }
.record-type { font-size: 1rem; font-weight: 700; color: #fff; }
.record-date { font-size: .78rem; color: #8b949e; margin-top: .2rem; }
.record-cost { font-size: 1.25rem; font-weight: 800; color: #00ffaa; white-space: nowrap; }
.record-desc { font-size: .875rem; color: #8b949e; line-height: 1.7; margin-bottom: .875rem; }
.record-detail { display: flex; gap: 1.5rem; flex-wrap: wrap; font-size: .78rem; }
.detail-item label { color: #8b949e; display: block; margin-bottom: .15rem; font-size: .7rem; text-transform: uppercase; letter-spacing: .05em; }
.detail-item span { color: #e6edf3; font-weight: 600; }
.provider-tag { display: inline-flex; align-items: center; gap: .4rem; padding: .25rem .75rem; background: rgba(0,255,170,.08); border: 1px solid rgba(0,255,170,.2); border-radius: 20px; font-size: .75rem; color: #00ffaa; margin-top: .625rem; }
.parts-list { display: flex; flex-wrap: wrap; gap: .4rem; margin-top: .625rem; }
.part-tag { display: inline-flex; padding: .2rem .625rem; background: rgba(0,212,255,.08); border: 1px solid rgba(0,212,255,.2); border-radius: 6px; font-size: .72rem; color: #00d4ff; }
/* Diagnostics */
.diag-card { background: #0d1117; border: 1px solid #21262d; border-radius: 10px; padding: 1rem; margin-top: .875rem; }
.diag-title { font-size: .82rem; font-weight: 700; color: #e6edf3; margin-bottom: .25rem; }
.sev-critical { color: #ff8099; }
.sev-high     { color: #ff6e40; }
.sev-medium   { color: #ffaa00; }
.sev-low      { color: #00ffaa; }
/* Photos */
.photo-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)); gap: .625rem; margin-top: .875rem; }
.photo-thumb { border-radius: 8px; overflow: hidden; aspect-ratio: 1; cursor: pointer; }
.photo-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform .3s; }
.photo-thumb:hover img { transform: scale(1.06); }
.photo-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; margin-bottom: .5rem; display: flex; align-items: center; gap: .4rem; }
/* Footer */
.report-footer { text-align: center; padding: 2rem 0; margin-top: 2rem; border-top: 1px solid #21262d; font-size: .78rem; color: #8b949e; }
.report-footer a { color: #00d4ff; text-decoration: none; }
.pdf-btn { display: inline-flex; align-items: center; gap: .5rem; margin-top: 1rem; padding: .75rem 1.5rem; background: linear-gradient(135deg, #a855f7, #ec4899); border: none; border-radius: 10px; color: #fff; font-weight: 700; font-size: .875rem; text-decoration: none; cursor: pointer; }
/* Lightbox */
#lb { display:none;position:fixed;inset:0;background:rgba(0,0,0,.92);z-index:999;align-items:center;justify-content:center; }
#lbImg { max-width:92vw;max-height:90vh;border-radius:12px;object-fit:contain; }
#lbClose { position:absolute;top:1.5rem;right:1.5rem;background:rgba(255,255,255,.1);border:none;border-radius:50%;width:44px;height:44px;color:#fff;font-size:1.25rem;cursor:pointer; }
</style></head>

<body>
<div class="container">

    {{-- Header --}}
    <div class="report-header">
        <div class="report-header-top">
            <div class="brand">ODOVIN</div>
            <span class="report-badge">📋 Verified Service Report</span>
        </div>

        <div class="vehicle-name">
            {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
        </div>

        <div class="vehicle-meta">
            @if($vehicle->vin)
                <span><strong>VIN:</strong> {{ $vehicle->vin }}</span>
            @endif

            @if($vehicle->color)
                <span><strong>Color:</strong> {{ $vehicle->color }}</span>
            @endif

            @if($vehicle->current_mileage > 0)
                <span><strong>Mileage:</strong> {{ number_format($vehicle->current_mileage) }} mi</span>
            @endif

            <span><strong>Generated:</strong> {{ now()->format('M d, Y') }}</span>

            @if($share->from_date || $share->to_date)
                <span>
                    <strong>Period:</strong>
                    {{ $share->from_date ? $share->from_date->format('M Y') : 'All time' }}
                    –
                    {{ $share->to_date ? $share->to_date->format('M Y') : 'Present' }}
                </span>
            @endif
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-num">{{ $records->count() }}</div>
            <div class="stat-lbl">Services</div>
        </div>

        @if($share->include_costs)
        <div class="stat-card">
            <div class="stat-num">${{ number_format($records->sum('cost'), 0) }}</div>
            <div class="stat-lbl">Total Spent</div>
        </div>
        @endif

        <div class="stat-card">
            <div class="stat-num">
                {{ $records->where('service_date', '>=', now()->subYear())->count() }}
            </div>
            <div class="stat-lbl">This Year</div>
        </div>

        @if($share->include_diagnostics)
        <div class="stat-card">
            <div class="stat-num">
                {{ $records->flatMap->serviceDiagnostics->where('status', 'open')->count() }}
            </div>
            <div class="stat-lbl">Open Issues</div>
        </div>
        @endif
    </div>

    {{-- Records --}}
    <div class="section-title">
        Service History ({{ $records->count() }} records)
    </div>

    @forelse($records as $record)
    <div class="record-card">

        <div class="record-header">
            <div>
                <div class="record-type">{{ $record->service_type }}</div>
                <div class="record-date">
                    {{ $record->service_date->format('M d, Y') }}
                    @if($record->mileage_at_service)
                        · {{ number_format($record->mileage_at_service) }} mi
                    @endif
                </div>
            </div>

            @if($share->include_costs && $record->cost)
                <div class="record-cost">${{ number_format($record->cost, 2) }}</div>
            @endif
        </div>

        @if($record->description)
            <div class="record-desc">{{ $record->description }}</div>
        @endif

        <div class="record-detail">
            @if($record->invoice_number)
                <div class="detail-item">
                    <label>Invoice</label>
                    <span>#{{ $record->invoice_number }}</span>
                </div>
            @endif

            @if($record->next_service_date)
                <div class="detail-item">
                    <label>Next Service</label>
                    <span>{{ $record->next_service_date->format('M d, Y') }}</span>
                </div>
            @endif

            @if($record->next_service_mileage)
                <div class="detail-item">
                    <label>Next at</label>
                    <span>{{ number_format($record->next_service_mileage) }} mi</span>
                </div>
            @endif
        </div>

        {{-- Provider --}}
        @if($share->include_provider_details && $record->serviceProvider)
            <div class="provider-tag">
                🏪 {{ $record->serviceProvider->business_name }}

                @if($record->serviceProvider->city)
                    · {{ $record->serviceProvider->city }}
                @endif

                @if($record->serviceProvider->is_verified)
                    · ✓ Verified
                @endif
            </div>
        @endif

        {{-- Diagnostics (FIXED SECTION) --}}
        @if($share->include_diagnostics && $record->serviceDiagnostics->isNotEmpty())

            @foreach($record->serviceDiagnostics as $diag)

                <div class="diag-card">

                    <div class="diag-title sev-{{ $diag->severity }}">
                        {{ $diag->is_safety_critical ? '🚨 SAFETY: ' : '⚠ ' }}
                        {{ $diag->title }}

                        <span style="font-size:.72rem;opacity:.7;">
                            · {{ ucfirst($diag->severity) }}
                        </span>
                    </div>

                    <div style="font-size:.8rem;color:#8b949e;margin-top:.25rem;">
                        {{ $diag->description }}
                    </div>

                    <div style="font-size:.72rem;color:#8b949e;margin-top:.35rem;">
                        Status:
                        <span style="color:#e6edf3;">
                            {{ ucfirst(str_replace('_', ' ', $diag->status)) }}
                        </span>

                        @if($diag->estimated_cost_min || $diag->estimated_cost_max)
                            · Est:
                            ${{ number_format($diag->estimated_cost_min ?? 0) }}
                            –
                            ${{ number_format($diag->estimated_cost_max ?? 0) }}
                        @endif
                    </div>

                </div>

            @endforeach

        @endif

    </div>

    @empty
        <div style="text-align:center;padding:3rem;color:#8b949e;">
            No service records found.
        </div>
    @endforelse

    {{-- Footer --}}
    <div class="report-footer">
        <div>
            This report was generated by
            <a href="{{ url('/') }}">Odovin</a>
        </div>

        <div style="margin-top:.5rem;">
            Report ID: {{ $share->token }}
        </div>

        <a href="{{ route('service-history.report.pdf', $share->token) }}" class="pdf-btn">
            📄 Download PDF
        </a>
    </div>

</div>
</body>
</html>