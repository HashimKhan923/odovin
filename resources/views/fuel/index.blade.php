@extends('layouts.app')

@section('title', 'Fuel & Charge Logs')

@section('content')
<style>
    /* Page Variables */
    :root[data-theme="dark"] {
        --page-bg: #0a0e1a;
        --card-bg: rgba(26, 32, 48, 0.8);
        --border-color: rgba(0, 212, 255, 0.1);
        --text-primary: #ffffff;
        --text-secondary: rgba(255, 255, 255, 0.7);
        --text-tertiary: rgba(255, 255, 255, 0.5);
        --accent-cyan: #00d4ff;
        --accent-green: #00ffaa;
        --accent-danger: #ff3366;
        --accent-warning: #ffaa00;
        --accent-electric: #7c3aed;
    }

    :root[data-theme="light"] {
        --page-bg: #f8fafc;
        --card-bg: rgba(255, 255, 255, 0.9);
        --border-color: rgba(0, 0, 0, 0.1);
        --text-primary: #1a1f36;
        --text-secondary: rgba(26, 31, 54, 0.7);
        --text-tertiary: rgba(26, 31, 54, 0.5);
        --accent-cyan: #0066ff;
        --accent-green: #00cc88;
        --accent-danger: #ff3366;
        --accent-warning: #ff9500;
        --accent-electric: #6d28d9;
    }

    .fuel-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        animation: slideDown 0.5s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .page-title h1 {
        font-family: 'Orbitron', sans-serif;
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .header-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .action-button {
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .action-button.primary {
        background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
        color: white;
        box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
    }

    .action-button.primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(0, 212, 255, 0.5);
    }

    .action-button.secondary {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
    }

    .action-button.secondary:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-primary);
    }

    /* Filter */
    .filter-section {
        margin-bottom: 2rem;
        animation: fadeInUp 0.6s ease-out 0.1s backwards;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .filter-select {
        padding: 0.875rem 1.25rem;
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        color: var(--text-primary);
        font-size: 0.875rem;
        font-weight: 500;
        min-width: 250px;
        transition: all 0.3s ease;
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--accent-cyan);
        box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
    }

    .filter-select option {
        background: var(--card-bg);
        color: var(--text-primary);
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        animation: fadeInUp 0.6s ease-out backwards;
    }

    .stat-card:nth-child(1) { animation-delay: 0.2s; }
    .stat-card:nth-child(2) { animation-delay: 0.25s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.35s; }
    .stat-card:nth-child(5) { animation-delay: 0.4s; }

    .stat-card:hover {
        transform: translateY(-4px);
        border-color: var(--accent-cyan);
        box-shadow: 0 8px 30px rgba(0, 212, 255, 0.2);
    }

    .stat-card.electric:hover {
        border-color: var(--accent-electric);
        box-shadow: 0 8px 30px rgba(124, 58, 237, 0.2);
    }

    .stat-label {
        font-size: 0.75rem;
        color: var(--text-tertiary);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.75rem;
        font-weight: 600;
    }

    .stat-value {
        font-family: 'Orbitron', sans-serif;
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .stat-subtitle {
        font-size: 0.75rem;
        color: var(--text-secondary);
    }

    /* Vehicle Type Badge */
    .vehicle-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.625rem;
        border-radius: 12px;
        font-size: 0.625rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-left: 0.5rem;
    }

    .vehicle-type-badge.fuel {
        background: rgba(0, 212, 255, 0.15);
        color: var(--accent-cyan);
        border: 1px solid rgba(0, 212, 255, 0.3);
    }

    .vehicle-type-badge.electric {
        background: rgba(124, 58, 237, 0.15);
        color: var(--accent-electric);
        border: 1px solid rgba(124, 58, 237, 0.3);
    }

    .vehicle-type-badge.hybrid {
        background: rgba(0, 255, 170, 0.15);
        color: var(--accent-green);
        border: 1px solid rgba(0, 255, 170, 0.3);
    }

    /* Chart Card */
    .chart-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        animation: fadeInUp 0.6s ease-out 0.45s backwards;
    }

    .chart-card h2 {
        font-family: 'Orbitron', sans-serif;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
    }

    /* Table */
    .table-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
        animation: fadeInUp 0.6s ease-out 0.5s backwards;
    }

    .fuel-table {
        width: 100%;
        border-collapse: collapse;
    }

    .fuel-table thead {
        background: rgba(0, 212, 255, 0.05);
        border-bottom: 1px solid var(--border-color);
    }

    .fuel-table th {
        padding: 1.25rem 1.5rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-tertiary);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .fuel-table th.text-right {
        text-align: right;
    }

    .fuel-table tbody tr {
        border-bottom: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .fuel-table tbody tr:hover {
        background: rgba(0, 212, 255, 0.03);
    }

    .fuel-table td {
        padding: 1.25rem 1.5rem;
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .fuel-table td.text-right {
        text-align: right;
    }

    .efficiency-value {
        font-weight: 600;
        color: var(--text-primary);
    }

    .anomaly-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.625rem;
        border-radius: 12px;
        font-size: 0.625rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-left: 0.5rem;
    }

    .anomaly-badge.impossible {
        background: rgba(255, 51, 102, 0.15);
        color: var(--accent-danger);
        border: 1px solid rgba(255, 51, 102, 0.3);
    }

    .anomaly-badge.unrealistic {
        background: rgba(255, 170, 0, 0.15);
        color: var(--accent-warning);
        border: 1px solid rgba(255, 170, 0, 0.3);
    }

    .anomaly-badge.suspicious {
        background: rgba(255, 220, 0, 0.15);
        color: #ffdc00;
        border: 1px solid rgba(255, 220, 0, 0.3);
    }

    .actions-cell {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
    }

    .action-link {
        color: var(--accent-cyan);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }

    .action-link:hover {
        text-shadow: 0 0 10px var(--accent-cyan);
    }

    .action-button-delete {
        background: none;
        border: none;
        color: var(--accent-danger);
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: 'Chakra Petch', sans-serif;
    }

    .action-button-delete:hover {
        text-shadow: 0 0 10px var(--accent-danger);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .fuel-container {
            padding: 1.5rem 1rem;
        }

        .page-header {
            flex-direction: column;
            gap: 1.5rem;
            align-items: flex-start;
        }

        .page-title h1 {
            font-size: 2rem;
        }

        .header-actions {
            width: 100%;
            flex-direction: column;
        }

        .action-button {
            width: 100%;
            justify-content: center;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .table-card {
            overflow-x: auto;
        }

        .fuel-table {
            min-width: 800px;
        }
    }
</style>

<div class="fuel-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title">
            <h1>{{ $selectedVehicle && $selectedVehicle->fuel_type === 'Electric' ? 'Charge Logs' : 'Fuel Logs' }}</h1>
        </div>

        <div class="header-actions">
            <a href="{{ route('fuel.create') }}" class="action-button primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ $selectedVehicle && $selectedVehicle->fuel_type === 'Electric' ? 'Add Charge Log' : 'Add Fuel Log' }}
            </a>
            <a href="{{ route('fuel.import.form') }}" class="action-button secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Import CSV
            </a>
            <a href="{{ route('fuel.export.csv', $selectedVehicleId) }}" class="action-button secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
                Export CSV
            </a>
            <a href="{{ route('fuel.export.pdf', ['vehicle_id' => request('vehicle_id')]) }}" class="action-button secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export PDF
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-section">
        <form method="GET">
            <select name="vehicle_id" class="filter-select" onchange="this.form.submit()">
                <option value="">All Vehicles</option>
                @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}" {{ $selectedVehicleId == $vehicle->id ? 'selected' : '' }}>
                        {{ $vehicle->full_name }}
                        @if($vehicle->fuel_type === 'Electric')
                            ⚡
                        @else
                            🔥
                        @endif
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        @if(!$selectedVehicle || $selectedVehicle->fuel_type !== 'Electric')
        <div class="stat-card">
            <div class="stat-label">Total Gallons</div>
            <div class="stat-value">{{ number_format($stats['total_gallons'], 2) }}</div>
        </div>
        @else
        <div class="stat-card electric">
            <div class="stat-label">Total kWh</div>
            <div class="stat-value">{{ number_format($stats['total_gallons'], 2) }}</div>
        </div>
        @endif

        <div class="stat-card {{ $selectedVehicle && $selectedVehicle->fuel_type === 'Electric' ? 'electric' : '' }}">
            <div class="stat-label">Total Cost</div>
            <div class="stat-value">${{ number_format($stats['total_cost'], 2) }}</div>
        </div>

        @if(!$selectedVehicle || $selectedVehicle->fuel_type !== 'Electric')
        <div class="stat-card">
            <div class="stat-label">Average MPG</div>
            <div class="stat-value">{{ number_format($stats['average_mpg'], 1) }}</div>
        </div>
        @else
        <div class="stat-card electric">
            <div class="stat-label">Average Mi/kWh</div>
            <div class="stat-value">{{ number_format($stats['average_mpg'], 2) }}</div>
        </div>
        @endif

        @if(!$selectedVehicle || $selectedVehicle->fuel_type !== 'Electric')
        <div class="stat-card">
            <div class="stat-label">Avg Price/Gal</div>
            <div class="stat-value">${{ number_format($stats['average_price_per_gallon'], 2) }}</div>
        </div>
        @else
        <div class="stat-card electric">
            <div class="stat-label">Avg Price/kWh</div>
            <div class="stat-value">${{ number_format($stats['average_price_per_gallon'], 3) }}</div>
        </div>
        @endif

        <div class="stat-card {{ $selectedVehicle && $selectedVehicle->fuel_type === 'Electric' ? 'electric' : '' }}">
            <div class="stat-label">{{ $selectedVehicle && $selectedVehicle->fuel_type === 'Electric' ? 'Charge' : 'Fuel' }} Cost / Mile</div>
            @if($fuelCostPerMile)
                <div class="stat-value">${{ number_format($fuelCostPerMile, 3) }}</div>
                <div class="stat-subtitle">Based on {{ number_format($totalMiles) }} miles</div>
            @else
                <div class="stat-subtitle">Not enough data</div>
            @endif
        </div>
    </div>

    <!-- Chart -->
    @if($mpgChartData->count() > 1)
    <div class="chart-card">
        <h2>
            @if($selectedVehicle && $selectedVehicle->fuel_type === 'Electric')
                Efficiency Trend (Mi/kWh)
            @else
                MPG Performance Trend
            @endif
        </h2>
        <canvas id="mpgChart" height="100"></canvas>
    </div>
    @endif

    <!-- Table -->
    <div class="table-card">
        <table class="fuel-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Vehicle</th>
                    <th class="text-right">
                        @if($selectedVehicle)
                            {{ $selectedVehicle->fuel_type === 'Electric' ? 'kWh' : 'Gallons' }}
                        @else
                            Quantity
                        @endif
                    </th>
                    <th class="text-right">Cost</th>
                    <th class="text-right">
                        @if($selectedVehicle)
                            {{ $selectedVehicle->fuel_type === 'Electric' ? 'Mi/kWh' : 'MPG' }}
                        @else
                            Efficiency
                        @endif
                    </th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td>{{ $log->fill_date->format('M d, Y') }}</td>
                        <td>
                            {{ $log->vehicle->full_name }}
                            @if($log->vehicle->fuel_type === 'Electric')
                                <span class="vehicle-type-badge electric">⚡ Electric</span>
                            @else
                                <span class="vehicle-type-badge fuel">🔥 Fuel</span>
                            @endif
                        </td>
                        <td class="text-right">{{ $log->gallons }}</td>
                        <td class="text-right">${{ $log->total_cost }}</td>
                        <td class="text-right">
                            @if($log->mpg)
                                <span class="efficiency-value">{{ number_format($log->mpg, $log->vehicle->fuel_type === 'Electric' ? 2 : 1) }}</span>
                                
                                @if($log->mpg_anomaly === 'impossible')
                                    <span class="anomaly-badge impossible" title="This efficiency is far from your vehicle's average">
                                        ⚠ Impossible
                                    </span>
                                @elseif($log->mpg_anomaly === 'unrealistic')
                                    <span class="anomaly-badge unrealistic" title="This efficiency is unusual for this vehicle">
                                        ⚠ Unrealistic
                                    </span>
                                @elseif($log->mpg_anomaly === 'suspicious')
                                    <span class="anomaly-badge suspicious" title="Check odometer or quantity">
                                        ⚠ Suspicious
                                    </span>
                                @endif
                            @else
                                <span style="color: var(--text-tertiary);">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="actions-cell">
                                <a href="{{ route('fuel.edit', $log) }}" class="action-link">Edit</a>
                                <form method="POST" action="{{ route('fuel.destroy', $log) }}" 
                                      onsubmit="return confirm('Delete this {{ $log->vehicle->fuel_type === 'Electric' ? 'charge' : 'fuel' }} log?')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-button-delete">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($logs->hasPages())
        <div style="margin-top: 2rem;">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
@if($mpgChartData->count() > 1)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mpgData = @json($mpgChartData);
        const canvas = document.getElementById('mpgChart');
        const isElectric = {{ $selectedVehicle && $selectedVehicle->fuel_type === 'Electric' ? 'true' : 'false' }};
        
        if (!canvas) return;

        // Get theme colors
        const theme = document.documentElement.getAttribute('data-theme') || 'dark';
        const isDark = theme === 'dark';
        
        const colors = {
            gradient1: isElectric ? (isDark ? '#7c3aed' : '#6d28d9') : (isDark ? '#00d4ff' : '#0066ff'),
            gradient2: isElectric ? (isDark ? '#a78bfa' : '#8b5cf6') : (isDark ? '#00ffaa' : '#00cc88'),
            grid: isDark ? 'rgba(0, 212, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)',
            text: isDark ? 'rgba(255, 255, 255, 0.5)' : 'rgba(26, 31, 54, 0.5)',
            tooltip: isDark ? '#1a2030' : '#ffffff'
        };

        // Create gradient
        const ctx = canvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
        gradient.addColorStop(0, colors.gradient1 + '40');
        gradient.addColorStop(1, colors.gradient2 + '10');

        const lineGradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
        lineGradient.addColorStop(0, colors.gradient1);
        lineGradient.addColorStop(1, colors.gradient2);

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: mpgData.map(d => d.date),
                datasets: [{
                    label: isElectric ? 'Mi/kWh' : 'MPG',
                    data: mpgData.map(d => d.mpg),
                    borderColor: lineGradient,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    pointBackgroundColor: colors.gradient1,
                    pointBorderWidth: 2,
                    pointBorderColor: isDark ? '#0a0e1a' : '#ffffff',
                    pointHoverBackgroundColor: colors.gradient2,
                    borderWidth: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                animation: {
                    duration: 1200,
                    easing: 'easeInOutCubic'
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: colors.tooltip,
                        titleColor: colors.text,
                        bodyColor: colors.text,
                        borderColor: isDark ? (isElectric ? 'rgba(124, 58, 237, 0.2)' : 'rgba(0, 212, 255, 0.2)') : 'rgba(0, 0, 0, 0.1)',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: ctx => isElectric ? `${ctx.parsed.y.toFixed(2)} Mi/kWh` : `${ctx.parsed.y.toFixed(1)} MPG`
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: colors.text,
                            font: {
                                family: "'Chakra Petch', sans-serif",
                                size: 11
                            }
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        ticks: {
                            color: colors.text,
                            font: {
                                family: "'Chakra Petch', sans-serif",
                                size: 11
                            },
                            callback: value => isElectric ? value.toFixed(1) : value.toFixed(0)
                        },
                        grid: {
                            color: colors.grid
                        },
                        border: {
                            display: false
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // Re-render chart on theme change
        const observer = new MutationObserver(() => {
            location.reload();
        });
        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['data-theme']
        });
    });
</script>
@endif
@endpush