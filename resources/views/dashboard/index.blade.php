@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-container">
    <!-- Animated Background -->
    <div class="dashboard-bg">
        <div class="grid-overlay"></div>
        <div class="gradient-orb orb-1"></div>
        <div class="gradient-orb orb-2"></div>
        <div class="gradient-orb orb-3"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Hero Header with Animation -->
        <div class="hero-header fade-in-up">
            <div class="hero-content">
                <div class="welcome-badge">
                    <span class="pulse-dot"></span>
                    <span>Welcome Back</span>
                </div>
                <h1 class="hero-title">
                    <span class="title-line">Your Garage</span>
                    <span class="title-accent">Command Center</span>
                </h1>
                <p class="hero-subtitle">Monitor, maintain, and master your vehicle fleet</p>
            </div>
            <div class="hero-stats">
                <div class="quick-stat">
                    <span class="stat-value">{{ $stats['total_vehicles'] }}</span>
                    <span class="stat-label">Vehicles</span>
                </div>
                <div class="stat-divider"></div>
                <div class="quick-stat">
                    <span class="stat-value">${{ number_format($stats['month_expenses'], 0) }}</span>
                    <span class="stat-label">This Month</span>
                </div>
            </div>
        </div>

        <!-- Analytics Toggle Button -->
<div class="analytics-toggle-container fade-in-up" style="animation-delay: 0.15s;">
    <a href="{{ route('analytics.dashboard') }}" class="analytics-toggle-button">
        <div class="toggle-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <div class="toggle-content">
            <div class="toggle-badge">
                <span class="ai-pulse"></span>
                AI POWERED
            </div>
            <h3>Advanced Analytics</h3>
            <p>Predictive Maintenance • Cost Forecasting • ROI Analysis</p>
        </div>
        <div class="toggle-arrow">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </div>
    </a>
</div>

        @if($stats['open_recalls'] > 0)
        <div class="alert-banner critical fade-in-up" style="animation-delay: 0.1s;">
            <div class="alert-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="alert-content">
                <h3>Safety Alert</h3>
                <p>{{ $stats['open_recalls'] }} open safety recall(s) require immediate attention</p>
            </div>
            <a href="{{ route('recalls.index') }}" class="alert-action">
                Review Now
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        @endif

        <!-- Premium Stats Grid -->
        <div class="stats-grid fade-in-up" style="animation-delay: 0.2s;">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon vehicles">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                    </div>
                    <span class="stat-trend up">+2 this year</span>
                </div>
                <div class="stat-body">
                    <h3 class="stat-title">Total Fleet</h3>
                    <p class="stat-number">{{ $stats['total_vehicles'] }}</p>
                    <p class="stat-subtitle">Active Vehicles</p>
                </div>
                <div class="stat-footer">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 75%;"></div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon bookings">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <span class="stat-trend neutral">On schedule</span>
                </div>
                <div class="stat-body">
                    <h3 class="stat-title">Active Bookings</h3>
                    <p class="stat-number">{{ $stats['active_bookings'] }}</p>
                    <p class="stat-subtitle">Scheduled Services</p>
                </div>
                <div class="stat-footer">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 45%;"></div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon maintenance">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    @if($stats['pending_maintenance'] > 0)
                        <span class="stat-trend down">Needs attention</span>
                    @else
                        <span class="stat-trend up">All clear</span>
                    @endif
                </div>
                <div class="stat-body">
                    <h3 class="stat-title">Maintenance</h3>
                    <p class="stat-number">{{ $stats['pending_maintenance'] }}</p>
                    <p class="stat-subtitle">Pending Tasks</p>
                </div>
                <div class="stat-footer">
                    <div class="progress-bar">
                        <div class="progress-fill warning" style="width: 60%;"></div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon expenses">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="stat-trend down">-12% vs last month</span>
                </div>
                <div class="stat-body">
                    <h3 class="stat-title">Monthly Spend</h3>
                    <p class="stat-number">${{ number_format($stats['month_expenses'], 0) }}</p>
                    <p class="stat-subtitle">Total Expenses</p>
                </div>
                <div class="stat-footer">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 88%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fuel Analytics Section -->
        <div class="fuel-analytics fade-in-up" style="animation-delay: 0.3s;">
            <div class="section-header">
                <h2 class="section-title">
                    <span class="title-icon">⛽</span>
                    Fuel Analytics
                </h2>
                <a href="{{ route('fuel.index') }}" class="section-link">
                    View All Logs
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="fuel-grid">
                <div class="fuel-card primary">
                    <div class="fuel-header">
                        <h3>This Month</h3>
                        <div class="fuel-icon">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M3 3h8v18H3V3zm8 0h4l3 3v6a2 2 0 002 2v5a2 2 0 01-2 2h-1m-6-9h4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="fuel-value">${{ number_format($stats['fuel_cost_month'], 2) }}</div>
                    <div class="fuel-chart">
                        <div class="mini-bars">
                            <div class="bar" style="height: 40%"></div>
                            <div class="bar" style="height: 65%"></div>
                            <div class="bar" style="height: 45%"></div>
                            <div class="bar" style="height: 80%"></div>
                            <div class="bar" style="height: 60%"></div>
                            <div class="bar" style="height: 90%"></div>
                            <div class="bar active" style="height: 75%"></div>
                        </div>
                    </div>
                </div>

                <div class="fuel-card">
                    <div class="fuel-header">
                        <h3>Avg MPG</h3>
                        <span class="fuel-period">30 days</span>
                    </div>
                    <div class="fuel-value">{{ $fuelInsights['avg_mpg_30'] ?? '—' }}</div>
                    @if(isset($fuelInsights['avg_mpg_30']))
                    <div class="fuel-gauge">
                        <div class="gauge-fill" style="width: {{ min($fuelInsights['avg_mpg_30'] ?? 0, 50) * 2 }}%"></div>
                    </div>
                    @endif
                </div>

                <div class="fuel-card">
                    <div class="fuel-header">
                        <h3>Cost Per Mile</h3>
                        @if($fuelInsights['total_miles'])
                        <span class="fuel-period">{{ number_format($fuelInsights['total_miles']) }} mi</span>
                        @endif
                    </div>
                    @if($fuelInsights['fuel_cost_mile'])
                    <div class="fuel-value">${{ number_format($fuelInsights['fuel_cost_mile'], 3) }}</div>
                    @else
                    <div class="fuel-value fuel-empty">—</div>
                    <p class="fuel-subtitle">Not enough data</p>
                    @endif
                </div>

                <div class="fuel-card trend-card">
                    <div class="fuel-header">
                        <h3>Efficiency Trend</h3>
                    </div>
                    @if($fuelInsights['mpg_trend'] === 'up')
                    <div class="trend-indicator improving">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                        <span>Improving</span>
                    </div>
                    <p class="trend-message">Fuel efficiency is trending up</p>
                    @elseif($fuelInsights['mpg_trend'] === 'down')
                    <div class="trend-indicator declining">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                        <span>Declining</span>
                    </div>
                    <p class="trend-message">Check driving habits or maintenance</p>
                    @elseif($fuelInsights['mpg_trend'] === 'stable')
                    <div class="trend-indicator stable">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 12h14"/>
                        </svg>
                        <span>Stable</span>
                    </div>
                    <p class="trend-message">Consistent performance</p>
                    @else
                    <div class="fuel-value fuel-empty">—</div>
                    <p class="fuel-subtitle">Not enough data</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Primary Vehicle & Maintenance Section -->
        <div class="main-grid fade-in-up" style="animation-delay: 0.4s;">
            <!-- Primary Vehicle Showcase -->
            <div class="vehicle-showcase">
                @if($primaryVehicle)
                <div class="vehicle-card-premium">
                    <div class="vehicle-badge">Primary Vehicle</div>
                    <!-- <div class="vehicle-image-container">
                        <div class="vehicle-image-bg"></div>
                        <div class="vehicle-glow"></div>
                    </div> -->
                    <div class="vehicle-info">
                        <h2 class="vehicle-name">{{ $primaryVehicle->full_name }}</h2>
                        <p class="vehicle-vin">{{ $primaryVehicle->vin }}</p>
                        
                        <div class="vehicle-specs">
                            <div class="spec-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <div>
                                    <span class="spec-label">Mileage</span>
                                    <span class="spec-value">{{ number_format($primaryVehicle->current_mileage) }} mi</span>
                                </div>
                            </div>
                            <div class="spec-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <div>
                                    <span class="spec-label">Plate</span>
                                    <span class="spec-value">{{ $primaryVehicle->license_plate ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="spec-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10" stroke-width="2"/>
                                    <circle cx="12" cy="12" r="6" fill="currentColor"/>
                                </svg>
                                <div>
                                    <span class="spec-label">Color</span>
                                    <span class="spec-value">{{ $primaryVehicle->color ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('vehicles.show', $primaryVehicle) }}" class="btn-premium">
                            <span>View Full Details</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
                @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <h3>No Vehicles Yet</h3>
                    <p>Start by adding your first vehicle to the garage</p>
                    <a href="{{ route('vehicles.create') }}" class="btn-premium">
                        <span>Add Vehicle</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </a>
                </div>
                @endif
            </div>

            <!-- Upcoming Maintenance -->
            <div class="maintenance-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Upcoming Maintenance</h2>
                    <a href="{{ route('maintenance.index') }}" class="panel-link">View All</a>
                </div>
                <div class="maintenance-list">
                    @forelse($upcomingMaintenance as $maintenance)
                    <div class="maintenance-item">
                        <div class="maintenance-indicator {{ $maintenance->status === 'overdue' ? 'overdue' : 'upcoming' }}"></div>
                        <div class="maintenance-content">
                            <h4>{{ $maintenance->service_type }}</h4>
                            <p class="maintenance-vehicle">{{ $maintenance->vehicle->full_name }}</p>
                            <div class="maintenance-meta">
                                <span class="meta-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $maintenance->due_date?->format('M d, Y') ?? 'N/A' }}
                                </span>
                                @if($maintenance->due_mileage)
                                <span class="meta-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    {{ number_format($maintenance->due_mileage) }} mi
                                </span>
                                @endif
                            </div>
                        </div>
                        <span class="maintenance-badge {{ $maintenance->status }}">
                            {{ ucfirst($maintenance->status) }}
                        </span>
                    </div>
                    @empty
                    <div class="empty-list">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>All caught up! No upcoming maintenance</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Activity Grid -->
        <div class="activity-grid fade-in-up" style="animation-delay: 0.5s;">
            <!-- Recent Bookings -->
            <div class="activity-panel">
                <div class="panel-header">
                    <div class="header-left">
                        <div class="panel-icon bookings">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h2 class="panel-title">Recent Bookings</h2>
                    </div>
                    <a href="{{ route('bookings.index') }}" class="panel-link">View All</a>
                </div>
                <div class="activity-list">
                    @forelse($recentBookings as $booking)
                    <div class="activity-item">
                        <div class="activity-dot"></div>
                        <div class="activity-content">
                            <h4>{{ $booking->service_type }}</h4>
                            <p class="activity-subtitle">{{ $booking->serviceProvider->name }}</p>
                            <p class="activity-time">{{ $booking->scheduled_date->format('M d, Y g:i A') }}</p>
                        </div>
                        <span class="activity-badge {{ $booking->status }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>
                    @empty
                    <div class="empty-list">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p>No bookings yet</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Expenses -->
            <div class="activity-panel">
                <div class="panel-header">
                    <div class="header-left">
                        <div class="panel-icon expenses">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="panel-title">Recent Expenses</h2>
                    </div>
                    <a href="{{ route('expenses.index') }}" class="panel-link">View All</a>
                </div>
                <div class="activity-list">
                    @forelse($recentExpenses as $expense)
                    <div class="activity-item">
                        <div class="activity-dot expense"></div>
                        <div class="activity-content">
                            <h4>{{ $expense->description }}</h4>
                            <p class="activity-subtitle">{{ ucfirst($expense->category) }} • {{ $expense->vehicle->full_name }}</p>
                            <p class="activity-time">{{ $expense->expense_date->format('M d, Y') }}</p>
                        </div>
                        <span class="expense-amount">${{ number_format($expense->amount, 2) }}</span>
                    </div>
                    @empty
                    <div class="empty-list">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>No expenses recorded</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Reminders & Documents -->
        @if($upcomingReminders->count() || $expiringDocuments->count())
        <div class="alerts-grid fade-in-up" style="animation-delay: 0.6s;">
            @if($upcomingReminders->count())
            <div class="alert-panel">
                <div class="panel-header">
                    <h3>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        Upcoming Reminders
                    </h3>
                    <a href="{{ route('reminders.index') }}">View All</a>
                </div>
                <div class="alert-list">
                    @foreach($upcomingReminders as $reminder)
                    <div class="alert-item">
                        <div class="alert-marker"></div>
                        <div>
                            <h4>{{ $reminder->title }}</h4>
                            <p>{{ $reminder->vehicle->full_name }}</p>
                        </div>
                        <span class="alert-date">{{ $reminder->due_date->format('M d') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($expiringDocuments->count())
            <div class="alert-panel documents">
                <div class="panel-header">
                    <h3>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Expiring Documents
                    </h3>
                </div>
                <div class="alert-list">
                    @foreach($expiringDocuments as $doc)
                    <div class="alert-item">
                        <div class="alert-marker warning"></div>
                        <div>
                            <h4>{{ $doc->name }}</h4>
                            <p>{{ $doc->vehicle->full_name }}</p>
                        </div>
                        <span class="alert-date urgent">{{ $doc->expiry_date->format('M d') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="quick-actions fade-in-up" style="animation-delay: 0.7s;">
            <div class="section-header">
                <h2 class="section-title">Quick Actions</h2>
            </div>
            <div class="actions-grid">
                <a href="{{ route('bookings.create') }}" class="action-card">
                    <div class="action-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span>Book Service</span>
                    <div class="action-arrow">→</div>
                </a>

                <a href="{{ route('expenses.create') }}" class="action-card">
                    <div class="action-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <span>Add Expense</span>
                    <div class="action-arrow">→</div>
                </a>

                <a href="{{ route('vehicles.create') }}" class="action-card">
                    <div class="action-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <span>Add Vehicle</span>
                    <div class="action-arrow">→</div>
                </a>

                <a href="{{ route('providers.index') }}" class="action-card">
                    <div class="action-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <span>Find Services</span>
                    <div class="action-arrow">→</div>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Import Premium Fonts */
@import url('https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300;400;500;600;700&family=Orbitron:wght@400;500;600;700;800;900&display=swap');

:root {
    --bg-primary: #0a0e1a;
    --bg-secondary: #121827;
    --bg-tertiary: #1a2030;
    --bg-card: rgba(26, 32, 48, 0.8);
    --bg-card-hover: rgba(30, 38, 58, 0.9);
    
    --accent-primary: #00d4ff;
    --accent-secondary: #00ffaa;
    --accent-danger: #ff3366;
    --accent-warning: #ffaa00;
    --accent-success: #00ff88;
    
    --text-primary: #ffffff;
    --text-secondary: rgba(255, 255, 255, 0.7);
    --text-tertiary: rgba(255, 255, 255, 0.5);
    
    --border-color: rgba(0, 212, 255, 0.1);
    --border-color-hover: rgba(0, 212, 255, 0.3);
    
    --glow-primary: 0 0 20px rgba(0, 212, 255, 0.3);
    --glow-secondary: 0 0 30px rgba(0, 255, 170, 0.2);
    --glow-intense: 0 0 40px rgba(0, 212, 255, 0.5);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.dashboard-container {
    min-height: 100vh;
    background: var(--bg-primary);
    position: relative;
    overflow: hidden;
    font-family: 'Chakra Petch', sans-serif;
}

/* Animated Background */
.dashboard-bg {
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
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: 
        linear-gradient(rgba(0, 212, 255, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0, 212, 255, 0.03) 1px, transparent 1px);
    background-size: 50px 50px;
    animation: gridMove 20s linear infinite;
}

@keyframes gridMove {
    0% { transform: translateY(0); }
    100% { transform: translateY(50px); }
}

.gradient-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.3;
    animation: float 20s ease-in-out infinite;
}

.orb-1 {
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, var(--accent-primary), transparent);
    top: -250px;
    right: -250px;
    animation-delay: 0s;
}

.orb-2 {
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, var(--accent-secondary), transparent);
    bottom: -200px;
    left: -200px;
    animation-delay: 7s;
}

.orb-3 {
    width: 350px;
    height: 350px;
    background: radial-gradient(circle, var(--accent-primary), transparent);
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    animation-delay: 14s;
}

@keyframes float {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(100px, -100px) scale(1.1); }
    66% { transform: translate(-100px, 100px) scale(0.9); }
}

/* Fade In Animations */
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

/* Hero Header */
.hero-header {
    padding: 4rem 0 3rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 2rem;
    flex-wrap: wrap;
}

.hero-content {
    flex: 1;
    min-width: 300px;
}

.welcome-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(0, 212, 255, 0.1);
    border: 1px solid var(--border-color);
    border-radius: 50px;
    color: var(--accent-primary);
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 1.5rem;
    backdrop-filter: blur(10px);
}

.pulse-dot {
    width: 8px;
    height: 8px;
    background: var(--accent-primary);
    border-radius: 50%;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; box-shadow: 0 0 10px var(--accent-primary); }
    50% { opacity: 0.5; box-shadow: 0 0 20px var(--accent-primary); }
}

.hero-title {
    font-family: 'Orbitron', sans-serif;
    font-size: 3.5rem;
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 1rem;
}

.title-line {
    display: block;
    color: var(--text-primary);
    text-shadow: 0 0 30px rgba(255, 255, 255, 0.3);
}

.title-accent {
    display: block;
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: shimmer 3s ease-in-out infinite;
}

@keyframes shimmer {
    0%, 100% { filter: brightness(1); }
    50% { filter: brightness(1.3); }
}

.hero-subtitle {
    font-size: 1.125rem;
    color: var(--text-secondary);
    font-weight: 300;
}

.hero-stats {
    display: flex;
    gap: 2rem;
    align-items: center;
    padding: 2rem;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    backdrop-filter: blur(20px);
}

.quick-stat {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.stat-value {
    font-family: 'Orbitron', sans-serif;
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--accent-primary);
    line-height: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--text-tertiary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.stat-divider {
    width: 1px;
    height: 50px;
    background: var(--border-color);
}

/* Alert Banner */
.alert-banner {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 1.5rem 2rem;
    background: rgba(255, 51, 102, 0.1);
    border: 1px solid rgba(255, 51, 102, 0.3);
    border-radius: 16px;
    margin-bottom: 3rem;
    backdrop-filter: blur(20px);
    position: relative;
    overflow: hidden;
}

.alert-banner::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--accent-danger);
    animation: alertPulse 2s ease-in-out infinite;
}

@keyframes alertPulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.alert-icon {
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    background: rgba(255, 51, 102, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.alert-icon svg {
    width: 24px;
    height: 24px;
    color: var(--accent-danger);
}

.alert-content {
    flex: 1;
}

.alert-content h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.alert-content p {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.alert-action {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: var(--accent-danger);
    color: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.alert-action:hover {
    transform: translateX(5px);
    box-shadow: 0 10px 30px rgba(255, 51, 102, 0.4);
}

.alert-action svg {
    width: 16px;
    height: 16px;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.stat-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 1.5rem;
    backdrop-filter: blur(20px);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.05), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.stat-card:hover {
    border-color: var(--border-color-hover);
    transform: translateY(-5px);
    box-shadow: var(--glow-primary);
}

.stat-card:hover::before {
    opacity: 1;
}

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.stat-icon.vehicles {
    background: rgba(0, 212, 255, 0.1);
}

.stat-icon.bookings {
    background: rgba(0, 255, 170, 0.1);
}

.stat-icon.maintenance {
    background: rgba(255, 170, 0, 0.1);
}

.stat-icon.expenses {
    background: rgba(255, 51, 102, 0.1);
}

.stat-icon svg {
    width: 24px;
    height: 24px;
    color: var(--accent-primary);
}

.stat-icon.bookings svg {
    color: var(--accent-secondary);
}

.stat-icon.maintenance svg {
    color: var(--accent-warning);
}

.stat-icon.expenses svg {
    color: var(--accent-danger);
}

.stat-trend {
    font-size: 0.75rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-weight: 500;
}

.stat-trend.up {
    background: rgba(0, 255, 136, 0.1);
    color: var(--accent-success);
}

.stat-trend.down {
    background: rgba(255, 51, 102, 0.1);
    color: var(--accent-danger);
}

.stat-trend.neutral {
    background: rgba(255, 170, 0, 0.1);
    color: var(--accent-warning);
}

.stat-body {
    margin-bottom: 1rem;
}

.stat-title {
    font-size: 0.875rem;
    color: var(--text-tertiary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
}

.stat-number {
    font-family: 'Orbitron', sans-serif;
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-subtitle {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.stat-footer {
    margin-top: 1rem;
}

.progress-bar {
    width: 100%;
    height: 4px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary));
    border-radius: 10px;
    transition: width 1s ease;
    animation: shimmerProgress 2s ease-in-out infinite;
}

.progress-fill.warning {
    background: linear-gradient(90deg, var(--accent-warning), var(--accent-danger));
}

@keyframes shimmerProgress {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* Fuel Analytics */
.fuel-analytics {
    margin-bottom: 3rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.section-title {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.title-icon {
    font-size: 2rem;
    filter: drop-shadow(0 0 10px rgba(255, 170, 0, 0.5));
}

.section-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--accent-primary);
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.section-link:hover {
    gap: 0.75rem;
    text-shadow: 0 0 10px var(--accent-primary);
}

.section-link svg {
    width: 16px;
    height: 16px;
}

.fuel-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.fuel-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 1.5rem;
    backdrop-filter: blur(20px);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.fuel-card.primary {
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 255, 170, 0.05));
    border-color: var(--accent-primary);
}

.fuel-card:hover {
    transform: translateY(-5px);
    border-color: var(--border-color-hover);
    box-shadow: var(--glow-primary);
}

.fuel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.fuel-header h3 {
    font-size: 0.875rem;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.fuel-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--accent-warning);
}

.fuel-icon svg {
    width: 20px;
    height: 20px;
}

.fuel-period {
    font-size: 0.75rem;
    color: var(--text-tertiary);
    padding: 0.25rem 0.5rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 6px;
}

.fuel-value {
    font-family: 'Orbitron', sans-serif;
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.fuel-value.fuel-empty {
    color: var(--text-tertiary);
}

.fuel-subtitle {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.fuel-chart {
    margin-top: 1.5rem;
}

.mini-bars {
    display: flex;
    align-items: flex-end;
    gap: 6px;
    height: 60px;
}

.bar {
    flex: 1;
    background: rgba(0, 212, 255, 0.3);
    border-radius: 4px 4px 0 0;
    transition: all 0.3s ease;
    animation: barGrow 0.6s ease-out forwards;
    transform-origin: bottom;
}

.bar.active {
    background: linear-gradient(to top, var(--accent-primary), var(--accent-secondary));
    box-shadow: 0 0 15px var(--accent-primary);
}

@keyframes barGrow {
    from {
        transform: scaleY(0);
    }
    to {
        transform: scaleY(1);
    }
}

.fuel-gauge {
    width: 100%;
    height: 6px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    overflow: hidden;
    margin-top: 1rem;
}

.gauge-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--accent-secondary), var(--accent-primary));
    border-radius: 10px;
    transition: width 1s ease;
    box-shadow: 0 0 10px var(--accent-primary);
}

.trend-card {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.trend-indicator {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.5rem;
}

.trend-indicator svg {
    width: 48px;
    height: 48px;
}

.trend-indicator.improving {
    color: var(--accent-success);
}

.trend-indicator.declining {
    color: var(--accent-danger);
}

.trend-indicator.stable {
    color: var(--accent-warning);
}

.trend-indicator span {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.5rem;
    font-weight: 700;
}

.trend-message {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

/* Main Grid */
.main-grid {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 2rem;
    margin-bottom: 3rem;
}

@media (max-width: 1024px) {
    .main-grid {
        grid-template-columns: 1fr;
    }
}

/* Vehicle Showcase */
.vehicle-showcase {
    min-height: 400px;
}

.vehicle-card-premium {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 24px;
    padding: 2rem;
    backdrop-filter: blur(20px);
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 63%;
}

.vehicle-card-premium:hover {
    border-color: var(--accent-primary);
    box-shadow: var(--glow-intense);
    transform: translateY(-5px);
}

.vehicle-badge {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--bg-primary);
    z-index: 2;
}

.vehicle-image-container {
    position: relative;
    width: 100%;
    height: 200px;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.vehicle-image-bg {
    position: absolute;
    width: 180px;
    height: 180px;
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    border-radius: 50%;
    opacity: 0.1;
    filter: blur(40px);
    animation: vehicleGlow 4s ease-in-out infinite;
}

@keyframes vehicleGlow {
    0%, 100% { transform: scale(1); opacity: 0.1; }
    50% { transform: scale(1.2); opacity: 0.2; }
}

.vehicle-glow {
    position: relative;
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    box-shadow: 0 10px 40px rgba(0, 212, 255, 0.4);
    animation: float 3s ease-in-out infinite;
}

.vehicle-glow::before {
    content: '🚗';
    filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.5));
}

.vehicle-info {
    position: relative;
    z-index: 1;
}

.vehicle-name {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    margin-top: 2.5rem;
}

.vehicle-vin {
    font-size: 0.875rem;
    color: var(--text-tertiary);
    font-family: monospace;
    margin-bottom: 2rem;
}

.vehicle-specs {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: rgba(0, 212, 255, 0.03);
    border: 1px solid var(--border-color);
    border-radius: 12px;
}

.spec-item {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.spec-item svg {
    width: 20px;
    height: 20px;
    color: var(--accent-primary);
    flex-shrink: 0;
}

.spec-item > div {
    display: flex;
    justify-content: space-between;
    flex: 1;
    gap: 1rem;
}

.spec-label {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.spec-value {
    font-size: 0.875rem;
    color: var(--text-primary);
    font-weight: 600;
}

.btn-premium {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    width: 100%;
    padding: 1rem;
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    color: var(--bg-primary);
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-premium::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
}

.btn-premium:hover::before {
    left: 100%;
}

.btn-premium:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(0, 212, 255, 0.5);
}

.btn-premium svg {
    width: 20px;
    height: 20px;
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    background: var(--bg-card);
    border: 2px dashed var(--border-color);
    border-radius: 24px;
    text-align: center;
    min-height: 400px;
}

.empty-icon {
    width: 80px;
    height: 80px;
    background: rgba(0, 212, 255, 0.1);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
}

.empty-icon svg {
    width: 40px;
    height: 40px;
    color: var(--accent-primary);
}

.empty-state h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.empty-state p {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 2rem;
}

/* Maintenance Panel */
.maintenance-panel {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 2rem;
    backdrop-filter: blur(20px);
    height: 100%;
}

.panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.panel-title {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
}

.panel-link {
    color: var(--accent-primary);
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.panel-link:hover {
    text-shadow: 0 0 10px var(--accent-primary);
}

.maintenance-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.maintenance-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    background: rgba(0, 212, 255, 0.03);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    transition: all 0.3s ease;
}

.maintenance-item:hover {
    background: rgba(0, 212, 255, 0.05);
    border-color: var(--border-color-hover);
    transform: translateX(5px);
}

.maintenance-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-top: 6px;
    flex-shrink: 0;
    animation: pulse 2s ease-in-out infinite;
}

.maintenance-indicator.upcoming {
    background: var(--accent-warning);
    box-shadow: 0 0 10px var(--accent-warning);
}

.maintenance-indicator.overdue {
    background: var(--accent-danger);
    box-shadow: 0 0 10px var(--accent-danger);
}

.maintenance-content {
    flex: 1;
}

.maintenance-content h4 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.maintenance-vehicle {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
}

.maintenance-meta {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    color: var(--text-tertiary);
}

.meta-item svg {
    width: 14px;
    height: 14px;
}

.maintenance-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    flex-shrink: 0;
}

.maintenance-badge.overdue {
    background: rgba(255, 51, 102, 0.1);
    color: var(--accent-danger);
}

.maintenance-badge.upcoming {
    background: rgba(255, 170, 0, 0.1);
    color: var(--accent-warning);
}

.empty-list {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem 1rem;
    text-align: center;
}

.empty-list svg {
    width: 48px;
    height: 48px;
    color: var(--text-tertiary);
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-list p {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

/* Activity Grid */
.activity-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

@media (max-width: 768px) {
    .activity-grid {
        grid-template-columns: 1fr;
    }
}

.activity-panel {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 2rem;
    backdrop-filter: blur(20px);
    transition: all 0.3s ease;
}

.activity-panel:hover {
    border-color: var(--border-color-hover);
}

.header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.panel-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.panel-icon.bookings {
    background: rgba(0, 212, 255, 0.1);
}

.panel-icon.expenses {
    background: rgba(255, 51, 102, 0.1);
}

.panel-icon svg {
    width: 20px;
    height: 20px;
    color: var(--accent-primary);
}

.panel-icon.expenses svg {
    color: var(--accent-danger);
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    background: rgba(0, 212, 255, 0.02);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    transition: all 0.3s ease;
    position: relative;
}

.activity-item:hover {
    background: rgba(0, 212, 255, 0.05);
    border-color: var(--border-color-hover);
}

.activity-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--accent-primary);
    margin-top: 6px;
    flex-shrink: 0;
    box-shadow: 0 0 10px var(--accent-primary);
}

.activity-dot.expense {
    background: var(--accent-danger);
    box-shadow: 0 0 10px var(--accent-danger);
}

.activity-content {
    flex: 1;
}

.activity-content h4 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.activity-subtitle {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 0.25rem;
}

.activity-time {
    font-size: 0.75rem;
    color: var(--text-tertiary);
}

.activity-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: capitalize;
    flex-shrink: 0;
}

.activity-badge.completed {
    background: rgba(0, 255, 136, 0.1);
    color: var(--accent-success);
}

.activity-badge.confirmed {
    background: rgba(0, 212, 255, 0.1);
    color: var(--accent-primary);
}

.activity-badge.pending {
    background: rgba(255, 170, 0, 0.1);
    color: var(--accent-warning);
}

.expense-amount {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--text-primary);
    flex-shrink: 0;
}

/* Alerts Grid */
.alerts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

@media (max-width: 768px) {
    .alerts-grid {
        grid-template-columns: 1fr;
    }
}

.alert-panel {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 1.5rem;
    backdrop-filter: blur(20px);
}

.alert-panel.documents {
    border-color: rgba(255, 170, 0, 0.3);
}

.alert-panel .panel-header h3 {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
}

.alert-panel .panel-header svg {
    width: 20px;
    height: 20px;
    color: var(--accent-primary);
}

.alert-panel.documents .panel-header svg {
    color: var(--accent-warning);
}

.alert-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.alert-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    background: rgba(0, 212, 255, 0.02);
    border-radius: 8px;
    transition: all 0.3s ease;
}

.alert-item:hover {
    background: rgba(0, 212, 255, 0.05);
}

.alert-marker {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--accent-primary);
    flex-shrink: 0;
}

.alert-marker.warning {
    background: var(--accent-warning);
}

.alert-item > div {
    flex: 1;
}

.alert-item h4 {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.125rem;
}

.alert-item p {
    font-size: 0.75rem;
    color: var(--text-secondary);
}

.alert-date {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-tertiary);
    flex-shrink: 0;
}

.alert-date.urgent {
    color: var(--accent-warning);
}

/* Quick Actions */
.quick-actions {
    margin-bottom: 4rem;
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.action-card {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    padding: 2rem 1.5rem;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    text-decoration: none;
    transition: all 0.3s ease;
    overflow: hidden;
}

.action-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.action-card:hover {
    border-color: var(--accent-primary);
    transform: translateY(-10px);
    box-shadow: var(--glow-primary);
}

.action-card:hover::before {
    opacity: 1;
}

.action-icon {
    width: 64px;
    height: 64px;
    background: rgba(0, 212, 255, 0.1);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.action-card:hover .action-icon {
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    transform: scale(1.1) rotate(5deg);
}

.action-icon svg {
    width: 32px;
    height: 32px;
    color: var(--accent-primary);
    transition: color 0.3s ease;
}

.action-card:hover .action-icon svg {
    color: var(--bg-primary);
}

.action-card span {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    text-align: center;
    transition: all 0.3s ease;
}

.action-card:hover span {
    color: var(--accent-primary);
}

.action-arrow {
    position: absolute;
    bottom: 1rem;
    right: 1rem;
    font-size: 1.5rem;
    color: var(--accent-primary);
    opacity: 0;
    transform: translateX(-10px);
    transition: all 0.3s ease;
}

.action-card:hover .action-arrow {
    opacity: 1;
    transform: translateX(0);
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-stats {
        flex-direction: column;
        gap: 1rem;
    }
    
    .stat-divider {
        display: none;
    }
    
    .stats-grid,
    .fuel-grid,
    .actions-grid {
        grid-template-columns: 1fr;
    }
    
    .main-grid {
        grid-template-columns: 1fr;
    }
}

/* Scrollbar Styling */
::-webkit-scrollbar {
    width: 13px;
}

::-webkit-scrollbar-track {
    background: var(--bg-secondary);
}

::-webkit-scrollbar-thumb {
    background: var(--accent-primary);
    border-radius: 5px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--accent-secondary);
}

/* Analytics Toggle Button */
.analytics-toggle-container {
    margin-bottom: 2rem;
}

.analytics-toggle-button {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 255, 170, 0.05));
    border: 2px solid transparent;
    border-radius: 20px;
    text-decoration: none;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    cursor: pointer;
}

.analytics-toggle-button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(0, 212, 255, 0.2), transparent);
    transition: left 0.6s ease;
}

.analytics-toggle-button:hover::before {
    left: 100%;
}

.analytics-toggle-button:hover {
    border-color: var(--accent-primary);
    transform: translateX(10px);
    box-shadow: 0 20px 60px rgba(0, 212, 255, 0.3);
}

.toggle-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.4s ease;
    position: relative;
}

.toggle-icon::after {
    content: '';
    position: absolute;
    inset: -2px;
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    border-radius: 18px;
    opacity: 0;
    filter: blur(8px);
    transition: opacity 0.4s ease;
}

.analytics-toggle-button:hover .toggle-icon::after {
    opacity: 0.6;
    animation: iconGlow 2s ease-in-out infinite;
}

@keyframes iconGlow {
    0%, 100% { opacity: 0.4; }
    50% { opacity: 0.8; }
}

.analytics-toggle-button:hover .toggle-icon {
    transform: scale(1.1) rotate(5deg);
}

.toggle-icon svg {
    width: 32px;
    height: 32px;
    color: var(--bg-primary);
    position: relative;
    z-index: 1;
}

.toggle-content {
    flex: 1;
}

.toggle-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0.75rem;
    background: rgba(0, 212, 255, 0.15);
    border: 1px solid var(--accent-primary);
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    color: var(--accent-primary);
    margin-bottom: 0.5rem;
}

.ai-pulse {
    width: 6px;
    height: 6px;
    background: var(--accent-primary);
    border-radius: 50%;
    animation: pulse 2s ease-in-out infinite;
}

.toggle-content h3 {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 0.25rem 0;
    transition: color 0.3s ease;
}

.analytics-toggle-button:hover .toggle-content h3 {
    color: var(--accent-primary);
}

.toggle-content p {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin: 0;
}

.toggle-arrow {
    width: 40px;
    height: 40px;
    background: rgba(0, 212, 255, 0.1);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.4s ease;
}

.analytics-toggle-button:hover .toggle-arrow {
    background: var(--accent-primary);
    transform: translateX(5px);
}

.toggle-arrow svg {
    width: 24px;
    height: 24px;
    color: var(--accent-primary);
    transition: color 0.3s ease;
}

.analytics-toggle-button:hover .toggle-arrow svg {
    color: var(--bg-primary);
}

/* Responsive */
@media (max-width: 768px) {
    .analytics-toggle-button {
        padding: 1.25rem 1.5rem;
        gap: 1rem;
    }
    
    .toggle-icon {
        width: 48px;
        height: 48px;
    }
    
    .toggle-icon svg {
        width: 24px;
        height: 24px;
    }
    
    .toggle-content h3 {
        font-size: 1.125rem;
    }
    
    .toggle-content p {
        font-size: 0.8rem;
    }
}
</style>
@endsection