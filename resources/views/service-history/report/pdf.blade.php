<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10pt; color: #1a1a2e; background: #fff; }

/* ── Page layout ── */
.page-header { background: #1a1a2e; color: #fff; padding: 20px 24px; margin-bottom: 20px; position: relative; }
.page-header-bar { height: 3px; background: linear-gradient(90deg, #00d4ff, #00ffaa); margin-bottom: 14px; }
.brand { font-size: 13pt; font-weight: 800; color: #00d4ff; letter-spacing: 3px; text-transform: uppercase; }
.report-title { font-size: 10pt; color: #8899aa; margin-top: 2px; }
.vehicle-name { font-size: 18pt; font-weight: 800; color: #fff; margin: 10px 0 6px; }
.vehicle-meta { font-size: 8pt; color: #8899aa; line-height: 1.8; }
.vehicle-meta strong { color: #ddeeff; }

/* ── Stats row ── */
.stats-row { display: table; width: 100%; margin-bottom: 18px; border-collapse: separate; border-spacing: 6px 0; }
.stat-box { display: table-cell; width: 25%; background: #f0f4f8; border: 1px solid #dde3ea; border-radius: 8px; padding: 10px 12px; text-align: center; vertical-align: middle; }
.stat-num { font-size: 16pt; font-weight: 800; color: #1a1a2e; display: block; }
.stat-lbl { font-size: 7pt; color: #667788; text-transform: uppercase; letter-spacing: .05em; display: block; margin-top: 2px; }

/* ── Section title ── */
.section-title { font-size: 7pt; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #667788; border-bottom: 1px solid #dde3ea; padding-bottom: 5px; margin-bottom: 10px; }

/* ── Record card ── */
.record { border: 1px solid #dde3ea; border-radius: 8px; padding: 12px 14px; margin-bottom: 10px; page-break-inside: avoid; position: relative; }
.record-stripe { width: 3px; background: #00d4ff; position: absolute; left: 0; top: 0; bottom: 0; border-radius: 8px 0 0 8px; }
.record-header { margin-bottom: 6px; }
.record-type { font-size: 11pt; font-weight: 700; color: #1a1a2e; }
.record-date { font-size: 8pt; color: #667788; margin-top: 2px; }
.record-cost-box { float: right; text-align: right; }
.record-cost { font-size: 13pt; font-weight: 800; color: #0a7c55; }
.record-cost-lbl { font-size: 7pt; color: #667788; }
.record-desc { font-size: 9pt; color: #334455; line-height: 1.6; margin: 6px 0; }

/* ── Detail pills ── */
.detail-row { margin: 6px 0; }
.detail-item { display: inline-block; margin-right: 16px; font-size: 8pt; }
.detail-item .k { color: #667788; }
.detail-item .v { color: #1a1a2e; font-weight: 600; }

/* ── Parts ── */
.part { display: inline-block; background: #e8f4ff; border: 1px solid #b8d8f8; border-radius: 4px; padding: 2px 7px; font-size: 7.5pt; color: #1a5c8a; margin: 2px 2px 2px 0; }

/* ── Provider ── */
.provider { background: #edfaf3; border: 1px solid #b8e8d0; border-radius: 5px; padding: 4px 10px; font-size: 8pt; color: #0a7c55; display: inline-block; margin-top: 5px; }

/* ── Diagnostics ── */
.diag { background: #fff8f0; border: 1px solid #f0d8b8; border-radius: 6px; padding: 8px 10px; margin-top: 6px; }
.diag-title { font-size: 8.5pt; font-weight: 700; }
.diag-body  { font-size: 8pt; color: #556677; margin-top: 2px; }
.sev-critical { color: #c0392b; }
.sev-high     { color: #e67e22; }
.sev-medium   { color: #f39c12; }
.sev-low      { color: #27ae60; }

/* ── Footer ── */
.page-footer { margin-top: 24px; border-top: 1px solid #dde3ea; padding-top: 10px; text-align: center; font-size: 7.5pt; color: #889aaa; }
.clearfix::after { content: ''; display: table; clear: both; }
</style>
</head>
<body>

{{-- Header --}}
<div class="page-header">
    <div class="page-header-bar"></div>

    <table width="100%">
        <tr>
            <td>
                <div class="brand">ODOVIN</div>
                <div class="report-title">Vehicle Service History Report</div>
            </td>

            <td style="text-align:right;font-size:8pt;color:#8899aa;">
                Report ID: {{ substr($share->token, 0, 16) }}...<br>
                Generated: {{ now()->format('M d, Y') }}<br>

                @if($share->from_date || $share->to_date)
                    Period:
                    {{ $share->from_date ? $share->from_date->format('M Y') : 'All time' }}
                    –
                    {{ $share->to_date ? $share->to_date->format('M Y') : 'Present' }}
                @endif
            </td>
        </tr>
    </table>

    <div class="vehicle-name">
        {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
    </div>

    <div class="vehicle-meta">
        @if($vehicle->vin)
            <strong>VIN:</strong> {{ $vehicle->vin }} &nbsp;&nbsp;
        @endif

        @if($vehicle->color)
            <strong>Color:</strong> {{ $vehicle->color }} &nbsp;&nbsp;
        @endif

        @if($vehicle->current_mileage > 0)
            <strong>Mileage:</strong> {{ number_format($vehicle->current_mileage) }} mi
        @endif
    </div>
</div>

{{-- Stats --}}
<div class="stats-row">
    <div class="stat-box">
        <span class="stat-num">{{ $records->count() }}</span>
        <span class="stat-lbl">Total Services</span>
    </div>

    @if($share->include_costs)
    <div class="stat-box">
        <span class="stat-num">${{ number_format($records->sum('cost'), 0) }}</span>
        <span class="stat-lbl">Total Spent</span>
    </div>
    @endif

    <div class="stat-box">
        <span class="stat-num">
            {{ $records->where('service_date', '>=', now()->subYear())->count() }}
        </span>
        <span class="stat-lbl">This Year</span>
    </div>

    @if($share->include_diagnostics)
    <div class="stat-box">
        <span class="stat-num">
            {{ $records->flatMap->serviceDiagnostics->where('status', 'open')->count() }}
        </span>
        <span class="stat-lbl">Open Issues</span>
    </div>
    @endif
</div>

{{-- Records --}}
<div class="section-title">
    Service History — {{ $records->count() }} Records
</div>

@forelse($records as $record)
<div class="record clearfix">
    <div class="record-stripe"></div>

    {{-- Cost --}}
    @if($share->include_costs && $record->cost)
    <div class="record-cost-box">
        <div class="record-cost">${{ number_format($record->cost, 2) }}</div>
        <div class="record-cost-lbl">cost</div>
    </div>
    @endif

    {{-- Header --}}
    <div class="record-header">
        <div class="record-type">{{ $record->service_type }}</div>

        <div class="record-date">
            {{ $record->service_date->format('M d, Y') }}

            @if($record->mileage_at_service)
                · {{ number_format($record->mileage_at_service) }} mi
            @endif

            @if($record->invoice_number)
                · Invoice #{{ $record->invoice_number }}
            @endif
        </div>
    </div>

    {{-- Description --}}
    @if($record->description)
        <div class="record-desc">{{ $record->description }}</div>
    @endif

    {{-- Details --}}
    <div class="detail-row">
        @if($record->next_service_date)
        <span class="detail-item">
            <span class="k">Next service: </span>
            <span class="v">{{ $record->next_service_date->format('M d, Y') }}</span>
        </span>
        @endif

        @if($record->next_service_mileage)
        <span class="detail-item">
            <span class="k">Next at: </span>
            <span class="v">{{ number_format($record->next_service_mileage) }} mi</span>
        </span>
        @endif
    </div>

    {{-- Parts --}}
    @if($record->parts_replaced && count($record->parts_replaced))
    <div style="margin-top:5px;">
        @foreach($record->parts_replaced as $part)
            <span class="part">{{ $part }}</span>
        @endforeach
    </div>
    @endif

    {{-- Provider --}}
    @if($share->include_provider_details && $record->serviceProvider)
    <div class="provider">
        ✓ {{ $record->serviceProvider->business_name }}
        @if($record->serviceProvider->city)
            , {{ $record->serviceProvider->city }}
        @endif
        @if($record->serviceProvider->is_verified)
            · Verified
        @endif
    </div>
    @endif

    {{-- Diagnostics (FIXED) --}}
    @if($share->include_diagnostics && $record->serviceDiagnostics->isNotEmpty())

        @foreach($record->serviceDiagnostics as $diag)

        <div class="diag">

            <div class="diag-title sev-{{ $diag->severity }}">
                {{ $diag->is_safety_critical ? '🚨 SAFETY: ' : '' }}
                {{ $diag->title }}

                <span style="font-size:7.5pt;">
                    ({{ ucfirst($diag->severity) }},
                    {{ ucfirst(str_replace('_', ' ', $diag->status)) }})
                </span>
            </div>

            <div class="diag-body">
                {{ $diag->description }}
            </div>

            @if($diag->estimated_cost_min || $diag->estimated_cost_max)
            <div style="font-size:7.5pt;color:#889aaa;margin-top:2px;">
                Est. cost:
                ${{ number_format($diag->estimated_cost_min ?? 0) }}
                –
                ${{ number_format($diag->estimated_cost_max ?? 0) }}
            </div>
            @endif

        </div>

        @endforeach

    @endif

    {{-- Notes --}}
    @if($record->notes)
        <div style="margin-top:5px;font-size:8pt;color:#667788;font-style:italic;">
            Note: {{ $record->notes }}
        </div>
    @endif

</div>
@empty
<div style="text-align:center;padding:20px;color:#889aaa;font-size:9pt;">
    No service records found for this period.
</div>
@endforelse

{{-- Footer --}}
<div class="page-footer">
    This report was generated by Odovin Vehicle Management Platform ·
    {{ url('/') }} ·
    Report ID: {{ substr($share->token, 0, 20) }}... ·
    Generated {{ now()->format('M d, Y \a\t g:i A') }}
</div>

</body>
</html>