@extends('layouts.app')

@section('title', 'Advanced Analytics')

@section('content')
<div class="analytics-container">
    <!-- Animated Background -->
    <div class="analytics-bg">
        <div class="grid-overlay"></div>
        <div class="gradient-orb orb-1"></div>
        <div class="gradient-orb orb-2"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        @if(!isset($hasVehicles) || $vehicles->count() > 0)
        <!-- Header with Vehicle Selector -->
        <div class="analytics-header fade-in-up">
            <div>
                <h1 class="page-title">
                    <span class="title-icon">📊</span>
                    Advanced Analytics
                </h1>
                <p class="page-subtitle">AI-powered insights and predictive analysis for your vehicle</p>
            </div>
            
            @if($vehicles->count() > 1)
            <div class="vehicle-selector">
                <label for="vehicle-select">Select Vehicle:</label>
                <select id="vehicle-select" onchange="window.location.href='?vehicle_id=' + this.value">
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" {{ $selectedVehicle->id == $vehicle->id ? 'selected' : '' }}>
                            {{ $vehicle->full_name }}
                        </option>
                    @endforeach>
                </select>
            </div>
            @endif
        </div>

        <!-- Current Vehicle Card -->
        <div class="current-vehicle-card fade-in-up" style="animation-delay: 0.1s;">
            <div class="vehicle-info-grid">
                <div class="vehicle-primary-info">
                    <h2>{{ $selectedVehicle->full_name }}</h2>
                    <p class="vin">VIN: {{ $selectedVehicle->vin }}</p>
                </div>
                <div class="vehicle-stat">
                    <span class="stat-label">Current Mileage</span>
                    <span class="stat-value">{{ number_format($selectedVehicle->current_mileage) }} mi</span>
                </div>
                <div class="vehicle-stat">
                    <span class="stat-label">Avg. Miles/Month</span>
                    <span class="stat-value">{{ number_format($predictiveMaintenance['avg_miles_per_month']) }} mi</span>
                </div>
                @if($selectedVehicle->purchase_date)
                <div class="vehicle-stat">
                    <span class="stat-label">Time Owned</span>
                    <span class="stat-value">{{ Carbon\Carbon::parse($selectedVehicle->purchase_date)->diffForHumans(null, true) }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Predictive Maintenance Section -->
        <div class="analytics-section fade-in-up" style="animation-delay: 0.2s;">
            <div class="section-header">
                <h2 class="section-title">
                    <span class="icon">🤖</span>
                    AI Predictive Maintenance
                </h2>
                <div class="section-badge">
                    {{ count($predictiveMaintenance['predictions']) }} Predictions
                </div>
            </div>

            @if($predictiveMaintenance['critical_items'] > 0)
            <div class="critical-alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <h3>Critical Maintenance Required</h3>
                    <p>{{ $predictiveMaintenance['critical_items'] }} component(s) need immediate attention</p>
                </div>
            </div>
            @endif

            <div class="prediction-grid">
                @forelse($predictiveMaintenance['predictions'] as $prediction)
                <div class="prediction-card severity-{{ $prediction['severity'] }}">
                    <div class="prediction-header">
                        <div>
                            <h3>{{ $prediction['component'] }}</h3>
                            <span class="prediction-badge {{ $prediction['severity'] }}">
                                {{ ucfirst($prediction['severity']) }}
                            </span>
                        </div>
                        <div class="confidence-circle" data-confidence="{{ $prediction['confidence'] }}">
                            <svg viewBox="0 0 36 36">
                                <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                <path class="circle-progress" stroke-dasharray="{{ $prediction['confidence'] }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            </svg>
                            <span class="confidence-text">{{ $prediction['confidence'] }}%</span>
                        </div>
                    </div>

                    <div class="prediction-body">
                        <div class="prediction-status">{{ $prediction['prediction'] }}</div>
                        
                        <div class="prediction-stats">
                            @if($prediction['miles_remaining'] !== null)
                            <div class="stat">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span>{{ number_format($prediction['miles_remaining']) }} mi remaining</span>
                            </div>
                            @endif
                            
                            <div class="stat">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>~{{ $prediction['days_remaining'] }} days</span>
                            </div>
                            
                            <div class="stat cost">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>${{ number_format($prediction['estimated_cost']) }}</span>
                            </div>
                        </div>

                        <div class="prediction-recommendation">
                            <strong>Recommendation:</strong> {{ $prediction['recommendation'] }}
                        </div>
                    </div>

                    <div class="prediction-footer">
                        <a href="{{ route('bookings.create', ['vehicle_id' => $selectedVehicle->id]) }}" class="btn-book">
                            Book Service
                        </a>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <p>All systems optimal! No maintenance predicted in the near future.</p>
                </div>
                @endforelse
            </div>

            <div class="total-cost-banner">
                <span>Total Estimated Maintenance Cost:</span>
                <span class="total-amount">${{ number_format($predictiveMaintenance['total_estimated_cost']) }}</span>
            </div>
        </div>

        <!-- Cost Forecast Section -->
        <div class="analytics-section fade-in-up" style="animation-delay: 0.3s;">
            <div class="section-header">
                <h2 class="section-title">
                    <span class="icon">💰</span>
                    Cost Forecasting
                </h2>
                <div class="trend-indicator {{ $costForecast['trend_direction'] ?? 'stable' }}">
                    @if(isset($costForecast['trend_direction']))
                        @if($costForecast['trend_direction'] === 'increasing')
                            ↗ Increasing
                        @elseif($costForecast['trend_direction'] === 'decreasing')
                            ↘ Decreasing
                        @else
                            → Stable
                        @endif
                    @endif
                </div>
            </div>

            @if($costForecast['has_data'])
            <div class="forecast-grid">
                <div class="forecast-stats">
                    <div class="stat-card">
                        <span class="stat-label">Monthly Average</span>
                        <span class="stat-number">${{ number_format($costForecast['monthly_average'], 2) }}</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-label">Trend</span>
                        <span class="stat-number trend-{{ $costForecast['trend_direction'] }}">
                            {{ $costForecast['trend_percentage'] > 0 ? '+' : '' }}{{ $costForecast['trend_percentage'] }}%
                        </span>
                    </div>
                    <div class="stat-card highlighted">
                        <span class="stat-label">Next Month Prediction</span>
                        <span class="stat-number">${{ number_format($costForecast['next_month_prediction'], 2) }}</span>
                    </div>
                    <div class="stat-card highlighted">
                        <span class="stat-label">Next Year Prediction</span>
                        <span class="stat-number">${{ number_format($costForecast['next_year_prediction'], 2) }}</span>
                    </div>
                </div>

                <!-- Forecast Chart -->
                <div class="chart-container">
                    <h3>12-Month Cost Forecast</h3>
                    <canvas id="forecastChart"></canvas>
                </div>

                <div class="recommendation-box">
                    <strong>💡 Insight:</strong> {{ $costForecast['recommendation'] }}
                </div>
            </div>
            @else
            <div class="empty-state">
                <p>{{ $costForecast['message'] }}</p>
            </div>
            @endif
        </div>

        <!-- Fuel Analytics Section -->
        <div class="analytics-section fade-in-up" style="animation-delay: 0.4s;">
            <div class="section-header">
                <h2 class="section-title">
                    <span class="icon">⛽</span>
                    Fuel Efficiency Analytics
                </h2>
                @if(isset($fuelAnalytics['statistics']['trend_direction']))
                <div class="trend-indicator {{ $fuelAnalytics['statistics']['trend_direction'] }}">
                    @if($fuelAnalytics['statistics']['trend_direction'] === 'improving')
                        ↗ Improving
                    @elseif($fuelAnalytics['statistics']['trend_direction'] === 'declining')
                        ↘ Declining
                    @else
                        → Stable
                    @endif
                </div>
                @endif
            </div>

            @if($fuelAnalytics['has_data'])
            <div class="fuel-stats-grid">
                <div class="stat-card">
                    <span class="stat-label">Average MPG</span>
                    <span class="stat-number">{{ $fuelAnalytics['statistics']['avg_mpg'] }}</span>
                </div>
                <div class="stat-card success">
                    <span class="stat-label">Best MPG</span>
                    <span class="stat-number">{{ $fuelAnalytics['statistics']['best_mpg'] }}</span>
                </div>
                <div class="stat-card danger">
                    <span class="stat-label">Worst MPG</span>
                    <span class="stat-number">{{ $fuelAnalytics['statistics']['worst_mpg'] }}</span>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Cost per Mile</span>
                    <span class="stat-number">${{ $fuelAnalytics['statistics']['avg_cost_per_mile'] }}</span>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Total Fuel Cost</span>
                    <span class="stat-number">${{ number_format($fuelAnalytics['statistics']['total_fuel_cost'], 2) }}</span>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Total Gallons</span>
                    <span class="stat-number">{{ number_format($fuelAnalytics['statistics']['total_gallons'], 1) }}</span>
                </div>
            </div>

            <div class="charts-row">
                <div class="chart-container">
                    <h3>MPG Over Time</h3>
                    <canvas id="mpgChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3>Cost per Mile Trend</h3>
                    <canvas id="costPerMileChart"></canvas>
                </div>
            </div>

            <div class="recommendation-box">
                <strong>💡 Insight:</strong> {{ $fuelAnalytics['recommendation'] }}
            </div>
            @else
            <div class="empty-state">
                <p>{{ $fuelAnalytics['message'] }}</p>
            </div>
            @endif
        </div>

        <!-- ROI Analysis Section -->
        <div class="analytics-section fade-in-up" style="animation-delay: 0.5s;">
            <div class="section-header">
                <h2 class="section-title">
                    <span class="icon">💎</span>
                    ROI & Total Cost of Ownership
                </h2>
            </div>

            <div class="roi-grid">
                <!-- Purchase Info -->
                <div class="roi-card">
                    <h3>Purchase Information</h3>
                    <div class="roi-details">
                        <div class="roi-row">
                            <span>Purchase Price</span>
                            <strong>${{ number_format($roiAnalysis['purchase_info']['purchase_price'], 2) }}</strong>
                        </div>
                        <div class="roi-row">
                            <span>Purchase Date</span>
                            <strong>{{ $roiAnalysis['purchase_info']['purchase_date'] }}</strong>
                        </div>
                        <div class="roi-row">
                            <span>Time Owned</span>
                            <strong>{{ $roiAnalysis['purchase_info']['years_owned'] }} years</strong>
                        </div>
                    </div>
                </div>

                <!-- Current Value -->
                <div class="roi-card">
                    <h3>Current Status</h3>
                    <div class="roi-details">
                        <div class="roi-row">
                            <span>Estimated Value</span>
                            <strong class="success">${{ number_format($roiAnalysis['current_status']['estimated_value'], 2) }}</strong>
                        </div>
                        <div class="roi-row">
                            <span>Depreciation</span>
                            <strong class="danger">${{ number_format($roiAnalysis['current_status']['depreciation'], 2) }}</strong>
                        </div>
                        <div class="roi-row">
                            <span>Depreciation Rate</span>
                            <strong>{{ $roiAnalysis['current_status']['depreciation_percentage'] }}%</strong>
                        </div>
                    </div>
                </div>

                <!-- Cost Analysis -->
                <div class="roi-card highlighted">
                    <h3>Cost Analysis</h3>
                    <div class="roi-details">
                        <div class="roi-row">
                            <span>Total Expenses</span>
                            <strong>${{ number_format($roiAnalysis['cost_analysis']['total_expenses'], 2) }}</strong>
                        </div>
                        <div class="roi-row">
                            <span>Total Cost</span>
                            <strong>${{ number_format($roiAnalysis['cost_analysis']['total_cost'], 2) }}</strong>
                        </div>
                        <div class="roi-row">
                            <span>Cost per Month</span>
                            <strong>${{ number_format($roiAnalysis['cost_analysis']['cost_per_month'], 2) }}</strong>
                        </div>
                        <div class="roi-row">
                            <span>Cost per Year</span>
                            <strong>${{ number_format($roiAnalysis['cost_analysis']['cost_per_year'], 2) }}</strong>
                        </div>
                        <div class="roi-row">
                            <span>Cost per Mile</span>
                            <strong>${{ $roiAnalysis['cost_analysis']['cost_per_mile'] }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Projections -->
                <div class="roi-card">
                    <h3>Future Projections</h3>
                    <div class="roi-details">
                        <div class="roi-row">
                            <span>Projected Annual Cost</span>
                            <strong>${{ number_format($roiAnalysis['projections']['annual_cost'], 2) }}</strong>
                        </div>
                        <div class="roi-row">
                            <span>5-Year Projection</span>
                            <strong>${{ number_format($roiAnalysis['projections']['five_year_cost'], 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expense Breakdown Chart -->
            @if($roiAnalysis['expense_breakdown']->isNotEmpty())
            <div class="chart-container">
                <h3>Expense Breakdown by Category</h3>
                <canvas id="expenseBreakdownChart"></canvas>
            </div>
            @endif

            <div class="recommendation-box">
                <strong>💡 Insight:</strong> {{ $roiAnalysis['recommendation'] }}
            </div>
        </div>

        <!-- Fleet Comparison (if multiple vehicles) -->
        @if($fleetComparison['has_multiple'])
        <div class="analytics-section fade-in-up" style="animation-delay: 0.6s;">
            <div class="section-header">
                <h2 class="section-title">
                    <span class="icon">🚗</span>
                    Fleet Comparison
                </h2>
            </div>

            <div class="fleet-grid">
                @foreach($fleetComparison['vehicles'] as $vehicle)
                <div class="fleet-card {{ $vehicle['id'] == $selectedVehicle->id ? 'active' : '' }}">
                    <h3>{{ $vehicle['name'] }}</h3>
                    <div class="fleet-stats">
                        <div class="fleet-stat">
                            <span class="label">Total Expenses</span>
                            <span class="value">${{ number_format($vehicle['total_expenses'], 2) }}</span>
                        </div>
                        <div class="fleet-stat">
                            <span class="label">Fuel Cost</span>
                            <span class="value">${{ number_format($vehicle['fuel_cost'], 2) }}</span>
                        </div>
                        <div class="fleet-stat">
                            <span class="label">Mileage</span>
                            <span class="value">{{ number_format($vehicle['mileage']) }} mi</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="fleet-insights">
                <div class="insight-card">
                    <span class="icon">💸</span>
                    <div>
                        <strong>Most Expensive:</strong>
                        <p>{{ $fleetComparison['most_expensive']['name'] }} - ${{ number_format($fleetComparison['most_expensive']['total_expenses'], 2) }}</p>
                    </div>
                </div>
                <div class="insight-card success">
                    <span class="icon">⚡</span>
                    <div>
                        <strong>Most Fuel Efficient:</strong>
                        <p>{{ $fleetComparison['most_efficient']['name'] }} - ${{ number_format($fleetComparison['most_efficient']['fuel_cost'], 2) }} fuel cost</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @else
        <!-- No Vehicles State -->
        <div class="empty-dashboard">
            <div class="empty-icon">📊</div>
            <h2>No Vehicles Added Yet</h2>
            <p>Add your first vehicle to start seeing advanced analytics and AI-powered insights.</p>
            <a href="{{ route('vehicles.create') }}" class="btn-primary">Add Your First Vehicle</a>
        </div>
        @endif

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart.js default configuration
    Chart.defaults.font.family = "'Chakra Petch', sans-serif";
    Chart.defaults.color = 'rgba(255, 255, 255, 0.7)';
    
    const chartColors = {
        primary: 'rgba(0, 212, 255, 1)',
        primaryLight: 'rgba(0, 212, 255, 0.2)',
        secondary: 'rgba(0, 255, 170, 1)',
        secondaryLight: 'rgba(0, 255, 170, 0.2)',
        danger: 'rgba(255, 51, 102, 1)',
        warning: 'rgba(255, 170, 0, 1)',
    };

    @if(isset($costForecast['has_data']) && $costForecast['has_data'])
    // Cost Forecast Chart
    const forecastCtx = document.getElementById('forecastChart');
    if (forecastCtx) {
        new Chart(forecastCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($costForecast['forecast'], 'month')) !!},
                datasets: [{
                    label: 'Predicted Cost',
                    data: {!! json_encode(array_column($costForecast['forecast'], 'predicted_amount')) !!},
                    borderColor: chartColors.primary,
                    backgroundColor: chartColors.primaryLight,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '$' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        },
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
    @endif

    @if(isset($fuelAnalytics['has_data']) && $fuelAnalytics['has_data'])
    // MPG Chart
    const mpgCtx = document.getElementById('mpgChart');
    if (mpgCtx) {
        new Chart(mpgCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($fuelAnalytics['chart_data']['mpg'], 'date')) !!},
                datasets: [{
                    label: 'MPG',
                    data: {!! json_encode(array_column($fuelAnalytics['chart_data']['mpg'], 'mpg')) !!},
                    borderColor: chartColors.secondary,
                    backgroundColor: chartColors.secondaryLight,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Cost per Mile Chart
    const costPerMileCtx = document.getElementById('costPerMileChart');
    if (costPerMileCtx) {
        new Chart(costPerMileCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($fuelAnalytics['chart_data']['cost'], 'date')) !!},
                datasets: [{
                    label: 'Cost per Mile',
                    data: {!! json_encode(array_column($fuelAnalytics['chart_data']['cost'], 'cost_per_mile')) !!},
                    backgroundColor: chartColors.primaryLight,
                    borderColor: chartColors.primary,
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '$' + context.parsed.y.toFixed(3);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toFixed(3);
                            }
                        },
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }
    @endif

    @if(isset($roiAnalysis['expense_breakdown']) && $roiAnalysis['expense_breakdown']->isNotEmpty())
    // Expense Breakdown Chart
    const expenseCtx = document.getElementById('expenseBreakdownChart');
    if (expenseCtx) {
        new Chart(expenseCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($roiAnalysis['expense_breakdown']->keys()) !!},
                datasets: [{
                    data: {!! json_encode($roiAnalysis['expense_breakdown']->values()) !!},
                    backgroundColor: [
                        chartColors.primary,
                        chartColors.secondary,
                        chartColors.danger,
                        chartColors.warning,
                        'rgba(138, 43, 226, 0.8)',
                        'rgba(255, 215, 0, 0.8)',
                    ],
                    borderWidth: 2,
                    borderColor: '#0a0e1a',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': $' + context.parsed.toFixed(2);
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    // Animate confidence circles
    document.querySelectorAll('.confidence-circle').forEach(circle => {
        const confidence = circle.dataset.confidence;
        const progressPath = circle.querySelector('.circle-progress');
        setTimeout(() => {
            progressPath.style.strokeDasharray = `${confidence}, 100`;
        }, 300);
    });
});
</script>
@endpush

<style>
    
.analytics-container {
    min-height: 100vh;
    background: var(--bg-primary);
    position: relative;
    padding-bottom: 4rem;
}

/* Animated Background */
.analytics-bg {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    pointer-events: none;
}

.grid-overlay {
    position: absolute;
    width: 100%;
    height: 100%;
    background-image: 
        linear-gradient(rgba(0, 212, 255, 0.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0, 212, 255, 0.02) 1px, transparent 1px);
    background-size: 50px 50px;
    animation: gridMove 20s linear infinite;
}

.gradient-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(100px);
    opacity: 0.2;
    animation: float 25s ease-in-out infinite;
}

.orb-1 {
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, var(--accent-primary), transparent);
    top: -300px;
    right: -300px;
}

.orb-2 {
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, var(--accent-secondary), transparent);
    bottom: -250px;
    left: -250px;
    animation-delay: 10s;
}

/* Header */
.analytics-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 3rem 0 2rem;
    flex-wrap: wrap;
    gap: 2rem;
}

.page-title {
    font-family: 'Orbitron', sans-serif;
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 1rem;
    margin: 0;
}

.title-icon {
    font-size: 2.5rem;
    filter: drop-shadow(0 0 15px var(--accent-primary));
}

.page-subtitle {
    font-size: 1rem;
    color: var(--text-secondary);
    margin-top: 0.5rem;
}

.vehicle-selector {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    backdrop-filter: blur(20px);
}

.vehicle-selector label {
    font-size: 0.875rem;
    color: var(--text-secondary);
    font-weight: 600;
}

.vehicle-selector select {
    padding: 0.5rem 1rem;
    background: rgba(0, 212, 255, 0.05);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    color: var(--text-primary);
    font-family: 'Chakra Petch', sans-serif;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.vehicle-selector select:hover {
    border-color: var(--accent-primary);
}

.vehicle-selector select:focus {
    outline: none;
    border-color: var(--accent-primary);
    box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
}

/* Current Vehicle Card */
.current-vehicle-card {
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 255, 170, 0.05));
    border: 1px solid var(--accent-primary);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 3rem;
    backdrop-filter: blur(20px);
    box-shadow: 0 10px 40px rgba(0, 212, 255, 0.2);
}

.vehicle-info-grid {
    display: grid;
    grid-template-columns: 2fr repeat(3, 1fr);
    gap: 2rem;
    align-items: center;
}

.vehicle-primary-info h2 {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 0.5rem 0;
}

.vehicle-primary-info .vin {
    font-size: 0.875rem;
    color: var(--text-tertiary);
    font-family: monospace;
}

.vehicle-stat {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.vehicle-stat .stat-label {
    font-size: 0.75rem;
    color: var(--text-tertiary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.vehicle-stat .stat-value {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--accent-primary);
}

/* Analytics Sections */
.analytics-section {
    margin-bottom: 3rem;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 2rem;
    backdrop-filter: blur(20px);
    transition: all 0.3s ease;
}

.analytics-section:hover {
    border-color: var(--border-color-hover);
    box-shadow: 0 10px 40px rgba(0, 212, 255, 0.15);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.section-title {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin: 0;
}

.section-title .icon {
    font-size: 1.75rem;
}

.section-badge {
    padding: 0.5rem 1rem;
    background: rgba(0, 212, 255, 0.1);
    border: 1px solid var(--accent-primary);
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--accent-primary);
}

.trend-indicator {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.trend-indicator.increasing {
    background: rgba(255, 51, 102, 0.1);
    color: var(--accent-danger);
}

.trend-indicator.decreasing {
    background: rgba(0, 255, 136, 0.1);
    color: var(--accent-success);
}

.trend-indicator.stable, .trend-indicator.improving {
    background: rgba(0, 255, 170, 0.1);
    color: var(--accent-secondary);
}

.trend-indicator.declining {
    background: rgba(255, 170, 0, 0.1);
    color: var(--accent-warning);
}

/* Critical Alert */
.critical-alert {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 1.5rem;
    background: rgba(255, 51, 102, 0.1);
    border: 2px solid var(--accent-danger);
    border-radius: 12px;
    margin-bottom: 2rem;
    animation: pulse 2s ease-in-out infinite;
}

.critical-alert svg {
    width: 48px;
    height: 48px;
    color: var(--accent-danger);
    flex-shrink: 0;
}

.critical-alert h3 {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 0.25rem 0;
}

.critical-alert p {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin: 0;
}

/* Prediction Grid */
.prediction-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.prediction-card {
    background: rgba(0, 212, 255, 0.03);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.prediction-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--accent-primary);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.prediction-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 212, 255, 0.2);
    border-color: var(--accent-primary);
}

.prediction-card:hover::before {
    opacity: 1;
}

.prediction-card.severity-critical {
    border-color: var(--accent-danger);
}

.prediction-card.severity-critical::before {
    background: var(--accent-danger);
}

.prediction-card.severity-high {
    border-color: var(--accent-warning);
}

.prediction-card.severity-high::before {
    background: var(--accent-warning);
}

.prediction-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.prediction-header h3 {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 0.5rem 0;
}

.prediction-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.prediction-badge.critical {
    background: rgba(255, 51, 102, 0.2);
    color: var(--accent-danger);
}

.prediction-badge.high {
    background: rgba(255, 170, 0, 0.2);
    color: var(--accent-warning);
}

.prediction-badge.medium {
    background: rgba(0, 212, 255, 0.2);
    color: var(--accent-primary);
}

.prediction-badge.low {
    background: rgba(0, 255, 170, 0.2);
    color: var(--accent-secondary);
}

/* Confidence Circle */
.confidence-circle {
    position: relative;
    width: 60px;
    height: 60px;
}

.confidence-circle svg {
    transform: rotate(-90deg);
    width: 100%;
    height: 100%;
}

.circle-bg {
    fill: none;
    stroke: rgba(255, 255, 255, 0.1);
    stroke-width: 3;
}

.circle-progress {
    fill: none;
    stroke: var(--accent-primary);
    stroke-width: 3;
    stroke-linecap: round;
    stroke-dasharray: 0, 100;
    transition: stroke-dasharray 1s ease;
}

.confidence-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--text-primary);
}

.prediction-body {
    margin-bottom: 1rem;
}

.prediction-status {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.prediction-stats {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-bottom: 1rem;
    padding: 1rem;
    background: rgba(0, 212, 255, 0.05);
    border-radius: 8px;
}

.prediction-stats .stat {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.prediction-stats .stat svg {
    width: 16px;
    height: 16px;
    color: var(--accent-primary);
}

.prediction-stats .stat.cost {
    color: var(--accent-warning);
}

.prediction-stats .stat.cost svg {
    color: var(--accent-warning);
}

.prediction-recommendation {
    font-size: 0.875rem;
    color: var(--text-secondary);
    line-height: 1.5;
}

.prediction-recommendation strong {
    color: var(--text-primary);
}

.prediction-footer {
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}

.btn-book {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    color: var(--bg-primary);
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

.btn-book:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(0, 212, 255, 0.4);
}

.total-cost-banner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 255, 170, 0.05));
    border: 1px solid var(--accent-primary);
    border-radius: 12px;
    font-size: 1.125rem;
    font-weight: 600;
}

.total-amount {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.5rem;
    color: var(--accent-primary);
}

/* Forecast Grid */
.forecast-grid {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.forecast-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.stat-card {
    padding: 1.5rem;
    background: rgba(0, 212, 255, 0.03);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.stat-card:hover {
    border-color: var(--accent-primary);
    transform: translateY(-2px);
}

.stat-card.highlighted {
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 255, 170, 0.05));
    border-color: var(--accent-primary);
}

.stat-card.success {
    border-color: var(--accent-success);
}

.stat-card.danger {
    border-color: var(--accent-danger);
}

.stat-card .stat-label {
    font-size: 0.75rem;
    color: var(--text-tertiary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.stat-card .stat-number {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
}

.stat-card .stat-number.trend-increasing {
    color: var(--accent-danger);
}

.stat-card .stat-number.trend-decreasing {
    color: var(--accent-success);
}

.stat-card .stat-number.trend-stable {
    color: var(--accent-primary);
}

/* Charts */
.chart-container {
    padding: 1.5rem;
    background: rgba(0, 212, 255, 0.02);
    border: 1px solid var(--border-color);
    border-radius: 12px;
}

.chart-container h3 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 1.5rem 0;
}

.chart-container canvas {
    height: 300px !important;
}

.charts-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 1.5rem;
}

/* Fuel Stats Grid */
.fuel-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

/* Recommendation Box */
.recommendation-box {
    padding: 1.5rem;
    background: rgba(0, 255, 170, 0.05);
    border-left: 4px solid var(--accent-secondary);
    border-radius: 8px;
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-top: 2rem;
}

.recommendation-box strong {
    color: var(--accent-secondary);
}

/* ROI Grid */
.roi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.roi-card {
    padding: 1.5rem;
    background: rgba(0, 212, 255, 0.03);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    transition: all 0.3s ease;
}

.roi-card:hover {
    border-color: var(--accent-primary);
    transform: translateY(-2px);
}

.roi-card.highlighted {
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 255, 170, 0.05));
    border-color: var(--accent-primary);
}

.roi-card h3 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 1rem 0;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--border-color);
}

.roi-details {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.roi-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.875rem;
}

.roi-row span {
    color: var(--text-secondary);
}

.roi-row strong {
    color: var(--text-primary);
    font-weight: 600;
}

.roi-row strong.success {
    color: var(--accent-success);
}

.roi-row strong.danger {
    color: var(--accent-danger);
}

/* Fleet Comparison */
.fleet-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.fleet-card {
    padding: 1.5rem;
    background: rgba(0, 212, 255, 0.03);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    transition: all 0.3s ease;
}

.fleet-card.active {
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 255, 170, 0.05));
    border-color: var(--accent-primary);
}

.fleet-card:hover {
    transform: translateY(-2px);
    border-color: var(--accent-primary);
}

.fleet-card h3 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 1rem 0;
}

.fleet-stats {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.fleet-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.875rem;
}

.fleet-stat .label {
    color: var(--text-secondary);
}

.fleet-stat .value {
    color: var(--text-primary);
    font-weight: 600;
}

.fleet-insights {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.insight-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: rgba(0, 212, 255, 0.05);
    border: 1px solid var(--accent-primary);
    border-radius: 12px;
}

.insight-card.success {
    background: rgba(0, 255, 136, 0.05);
    border-color: var(--accent-success);
}

.insight-card .icon {
    font-size: 2rem;
    flex-shrink: 0;
}

.insight-card strong {
    display: block;
    font-size: 0.875rem;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.insight-card p {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin: 0;
}

/* Empty States */
.empty-state {
    padding: 3rem;
    text-align: center;
    color: var(--text-secondary);
}

.empty-dashboard {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 2rem;
    text-align: center;
    min-height: 60vh;
}

.empty-icon {
    font-size: 5rem;
    margin-bottom: 1.5rem;
    opacity: 0.3;
}

.empty-dashboard h2 {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 0.75rem 0;
}

.empty-dashboard p {
    font-size: 1rem;
    color: var(--text-secondary);
    margin-bottom: 2rem;
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    padding: 1rem 2rem;
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    color: var(--bg-primary);
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(0, 212, 255, 0.4);
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.6s ease-out forwards;
    opacity: 0;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

/* Responsive Design */
@media (max-width: 1024px) {
    .vehicle-info-grid {
        grid-template-columns: 1fr 1fr;
    }
    
    .charts-row {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .page-title {
        font-size: 2rem;
    }
    
    .analytics-header {
        flex-direction: column;
        align-items: stretch;
    }
    
    .vehicle-info-grid {
        grid-template-columns: 1fr;
    }
    
    .prediction-grid,
    .forecast-stats,
    .fuel-stats-grid,
    .roi-grid,
    .fleet-grid {
        grid-template-columns: 1fr;
    }
    
    .analytics-section {
        padding: 1.5rem;
    }
}
    </style>
@endsection