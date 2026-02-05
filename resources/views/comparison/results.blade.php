@extends('layouts.app')

@section('title', 'Comparison Results')

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
    }

    .results-container {
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

    .page-header h1 {
        font-family: 'Orbitron', sans-serif;
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
    }

    .page-header p {
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    /* Comparison Table Card */
    .table-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 2rem;
        animation: fadeInUp 0.6s ease-out 0.2s backwards;
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

    .comparison-table {
        width: 100%;
        border-collapse: collapse;
    }

    .comparison-table thead {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 255, 170, 0.1));
        border-bottom: 2px solid var(--border-color);
    }

    .comparison-table th {
        padding: 1.5rem 1.5rem;
        text-align: left;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .comparison-table th:first-child {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--text-tertiary);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        width: 200px;
    }

    .vehicle-header {
        font-family: 'Orbitron', sans-serif;
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .comparison-table tbody tr {
        border-bottom: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .comparison-table tbody tr:hover {
        background: rgba(0, 212, 255, 0.03);
    }

    .comparison-table td {
        padding: 1.25rem 1.5rem;
        font-size: 0.875rem;
    }

    .comparison-table td:first-child {
        font-weight: 600;
        color: var(--text-secondary);
        border-right: 1px solid var(--border-color);
    }

    .metric-value {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .value {
        font-family: 'Orbitron', sans-serif;
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    /* Best/Worst Indicators */
    .indicator {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .indicator.best {
        background: rgba(0, 255, 136, 0.15);
        border: 2px solid rgba(0, 255, 136, 0.3);
    }

    .indicator.best svg {
        width: 18px;
        height: 18px;
        color: #00ff88;
    }

    .indicator.worst {
        background: rgba(255, 51, 102, 0.15);
        border: 2px solid rgba(255, 51, 102, 0.3);
    }

    .indicator.worst svg {
        width: 18px;
        height: 18px;
        color: #ff3366;
    }

    /* Back Link */
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.875rem 1.5rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        color: var(--accent-cyan);
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.3s ease;
        animation: fadeInUp 0.6s ease-out 0.3s backwards;
    }

    .back-link:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: var(--accent-cyan);
        box-shadow: 0 4px 15px rgba(0, 212, 255, 0.2);
    }

    .back-link svg {
        width: 20px;
        height: 20px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .results-container {
            padding: 1.5rem 1rem;
        }

        .page-header h1 {
            font-size: 2rem;
        }

        .table-card {
            overflow-x: auto;
        }

        .comparison-table {
            min-width: 800px;
        }

        .comparison-table th:first-child {
            width: 150px;
        }

        .comparison-table th,
        .comparison-table td {
            padding: 1rem;
        }

        .value {
            font-size: 1rem;
        }
    }
</style>

<div class="results-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Comparison Results</h1>
        <p>Analyzing {{ count($comparison) }} vehicles side-by-side</p>
    </div>

    <!-- Comparison Table -->
    <div class="table-card">
        <table class="comparison-table">
            <thead>
                <tr>
                    <th>Metric</th>
                    @foreach ($comparison as $item)
                        <th class="vehicle-header">
                            {{ $item['vehicle']->year }}
                            {{ $item['vehicle']->make }}
                            {{ $item['vehicle']->model }}
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                <!-- Total Expenses -->
                <tr>
                    <td>Total Expenses</td>
                    @php
                        $minExpense = min(array_column($comparison, 'total_expenses'));
                        $maxExpense = max(array_column($comparison, 'total_expenses'));
                    @endphp
                    @foreach ($comparison as $item)
                        <td>
                            <div class="metric-value">
                                <span class="value">${{ number_format($item['total_expenses'], 2) }}</span>
                                @if($item['total_expenses'] == $minExpense && count($comparison) > 1)
                                    <div class="indicator best" title="Best">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @elseif($item['total_expenses'] == $maxExpense && count($comparison) > 1)
                                    <div class="indicator worst" title="Highest">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </td>
                    @endforeach
                </tr>

                <!-- Monthly Average -->
                <tr>
                    <td>Monthly Average</td>
                    @php
                        $minMonthly = min(array_column($comparison, 'monthly_average'));
                        $maxMonthly = max(array_column($comparison, 'monthly_average'));
                    @endphp
                    @foreach ($comparison as $item)
                        <td>
                            <div class="metric-value">
                                <span class="value">${{ number_format($item['monthly_average'], 2) }}</span>
                                @if($item['monthly_average'] == $minMonthly && count($comparison) > 1)
                                    <div class="indicator best" title="Best">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @elseif($item['monthly_average'] == $maxMonthly && count($comparison) > 1)
                                    <div class="indicator worst" title="Highest">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </td>
                    @endforeach
                </tr>

                <!-- Cost Per Mile -->
                <tr>
                    <td>Cost Per Mile</td>
                    @php
                        $minCPM = min(array_column($comparison, 'cost_per_mile'));
                        $maxCPM = max(array_column($comparison, 'cost_per_mile'));
                    @endphp
                    @foreach ($comparison as $item)
                        <td>
                            <div class="metric-value">
                                <span class="value">${{ number_format($item['cost_per_mile'], 3) }}</span>
                                @if($item['cost_per_mile'] == $minCPM && count($comparison) > 1)
                                    <div class="indicator best" title="Best">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @elseif($item['cost_per_mile'] == $maxCPM && count($comparison) > 1)
                                    <div class="indicator worst" title="Highest">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </td>
                    @endforeach
                </tr>

                <!-- Fuel Cost -->
                <tr>
                    <td>Fuel Cost</td>
                    @php
                        $minFuel = min(array_column($comparison, 'fuel_cost'));
                        $maxFuel = max(array_column($comparison, 'fuel_cost'));
                    @endphp
                    @foreach ($comparison as $item)
                        <td>
                            <div class="metric-value">
                                <span class="value">${{ number_format($item['fuel_cost'], 2) }}</span>
                                @if($item['fuel_cost'] == $minFuel && count($comparison) > 1)
                                    <div class="indicator best" title="Best">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @elseif($item['fuel_cost'] == $maxFuel && count($comparison) > 1)
                                    <div class="indicator worst" title="Highest">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </td>
                    @endforeach
                </tr>

                <!-- Maintenance Cost -->
                <tr>
                    <td>Maintenance Cost</td>
                    @php
                        $minMaint = min(array_column($comparison, 'maintenance_cost'));
                        $maxMaint = max(array_column($comparison, 'maintenance_cost'));
                    @endphp
                    @foreach ($comparison as $item)
                        <td>
                            <div class="metric-value">
                                <span class="value">${{ number_format($item['maintenance_cost'], 2) }}</span>
                                @if($item['maintenance_cost'] == $minMaint && count($comparison) > 1)
                                    <div class="indicator best" title="Best">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @elseif($item['maintenance_cost'] == $maxMaint && count($comparison) > 1)
                                    <div class="indicator worst" title="Highest">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </td>
                    @endforeach
                </tr>

                <!-- Service Count -->
                <tr>
                    <td>Service Count</td>
                    @php
                        $maxServices = max(array_column($comparison, 'service_count'));
                        $minServices = min(array_column($comparison, 'service_count'));
                    @endphp
                    @foreach ($comparison as $item)
                        <td>
                            <div class="metric-value">
                                <span class="value">{{ $item['service_count'] }}</span>
                                @if($item['service_count'] == $maxServices && count($comparison) > 1)
                                    <div class="indicator best" title="Most Services">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @elseif($item['service_count'] == $minServices && count($comparison) > 1)
                                    <div class="indicator worst" title="Least Services">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Back Link -->
    <a href="{{ route('comparison.index') }}" class="back-link">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Compare Other Vehicles
    </a>
</div>
@endsection