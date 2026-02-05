@extends('layouts.app')

@section('title', 'My Vehicles')

@section('content')
<div class="vehicles-page">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="page-header fade-in-up">
            <div>
                <div class="header-badge">
                    <span class="pulse-dot"></span>
                    <span>Fleet Management</span>
                </div>
                <h1 class="page-title">My Garage</h1>
                <p class="page-subtitle">Manage all your vehicles in one powerful dashboard</p>
            </div>
            <a href="{{ route('vehicles.create') }}" class="btn-add-vehicle">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add Vehicle</span>
            </a>
        </div>

        @if($vehicles->isEmpty())
            <!-- Empty State -->
            <div class="empty-state fade-in-up" style="animation-delay: 0.2s;">
                <div class="empty-icon-wrapper">
                    <div class="empty-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                    </div>
                    <div class="glow-ring"></div>
                </div>
                <h3 class="empty-title">Your Garage is Empty</h3>
                <p class="empty-description">Add your first vehicle to unlock powerful fleet management features</p>
                <a href="{{ route('vehicles.create') }}" class="btn-empty-cta">
                    <span>Add Your First Vehicle</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
        @else
            <!-- Vehicles Grid -->
            <div class="vehicles-grid">
                @foreach($vehicles as $index => $vehicle)
                <div class="vehicle-card fade-in-up" style="animation-delay: {{ 0.1 * ($index + 1) }}s;">
                    @if($vehicle->is_primary)
                    <div class="primary-badge">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        <span>Primary</span>
                    </div>
                    @endif

                    <div class="card-glow"></div>

                    <!-- Vehicle Header -->
                    <div class="vehicle-header">
                        <div class="vehicle-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div class="vehicle-menu" x-data="{ open: false }">
                            <button @click="open = !open" class="menu-button">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak class="menu-dropdown">
                                <a href="{{ route('vehicles.show', $vehicle) }}" class="menu-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>View Details</span>
                                </a>
                                <a href="{{ route('vehicles.edit', $vehicle) }}" class="menu-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span>Edit</span>
                                </a>
                                @unless($vehicle->is_primary)
                                <form action="{{ route('vehicles.set-primary', $vehicle) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="menu-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                        </svg>
                                        <span>Set as Primary</span>
                                    </button>
                                </form>
                                @endunless
                                <div class="menu-divider"></div>
                                <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this vehicle?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="menu-item danger">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span>Delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Info -->
                    <div class="vehicle-info">
                        <h3 class="vehicle-name">{{ $vehicle->full_name }}</h3>
                        <p class="vehicle-vin">{{ $vehicle->vin }}</p>
                    </div>

                    <!-- Vehicle Stats -->
                    <div class="vehicle-stats">
                        <div class="stat-item">
                            <span class="stat-label">Mileage</span>
                            <span class="stat-value">{{ number_format($vehicle->current_mileage) }} mi</span>
                        </div>
                        @if($vehicle->health_status)
                        <div class="stat-item">
                            <span class="stat-label">Health</span>
                            <span class="stat-value health-{{ $vehicle->health_status }}">
                                @if($vehicle->health_status === 'excellent')
                                    <svg viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                    </svg>
                                    Excellent
                                @elseif($vehicle->health_status === 'good')
                                    <svg viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z"/>
                                    </svg>
                                    Good
                                @else
                                    <svg viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                                    </svg>
                                    Needs Attention
                                @endif
                            </span>
                        </div>
                        @endif
                        @if($vehicle->license_plate)
                        <div class="stat-item">
                            <span class="stat-label">Plate</span>
                            <span class="stat-value">{{ $vehicle->license_plate }}</span>
                        </div>
                        @endif
                        @if($vehicle->color)
                        <div class="stat-item">
                            <span class="stat-label">Color</span>
                            <span class="stat-value">{{ $vehicle->color }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Activity Metrics -->
                    <div class="activity-metrics">
                        <div class="metric">
                            <div class="metric-icon maintenance">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div class="metric-content">
                                <p class="metric-value">{{ $vehicle->maintenanceSchedules->count() }}</p>
                                <p class="metric-label">Maintenance</p>
                            </div>
                        </div>

                        <div class="metric">
                            <div class="metric-icon services">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div class="metric-content">
                                <p class="metric-value">{{ $vehicle->serviceRecords->count() }}</p>
                                <p class="metric-label">Services</p>
                            </div>
                        </div>

                        <div class="metric">
                            <div class="metric-icon expenses">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="metric-content">
                                <p class="metric-value">{{ $vehicle->expenses->count() }}</p>
                                <p class="metric-label">Expenses</p>
                            </div>
                        </div>
                    </div>

                    <!-- View Details Button -->
                    <a href="{{ route('vehicles.show', $vehicle) }}" class="btn-view-details">
                        <span>View Complete Details</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300;400;500;600;700&family=Orbitron:wght@400;500;600;700;800&display=swap');

:root {
    --bg-primary: #0a0e1a;
    --bg-secondary: #121827;
    --bg-card: rgba(26, 32, 48, 0.8);
    --accent-primary: #00d4ff;
    --accent-secondary: #00ffaa;
    --accent-danger: #ff3366;
    --text-primary: #ffffff;
    --text-secondary: rgba(255, 255, 255, 0.7);
    --text-tertiary: rgba(255, 255, 255, 0.5);
    --border-color: rgba(0, 212, 255, 0.1);
}

.vehicles-page {
    min-height: 100vh;
    padding: 2rem 0;
}

/* Page Header */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 3rem;
    flex-wrap: wrap;
    gap: 2rem;
}

.header-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(0, 212, 255, 0.1);
    border: 1px solid rgba(0, 212, 255, 0.3);
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--accent-primary);
    margin-bottom: 1rem;
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

.page-title {
    font-family: 'Orbitron', sans-serif;
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.page-subtitle {
    font-size: 1rem;
    color: var(--text-secondary);
}

.btn-add-vehicle {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 2rem;
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    color: var(--bg-primary);
    text-decoration: none;
    border-radius: 12px;
    font-weight: 700;
    transition: all 0.3s ease;
}

.btn-add-vehicle svg {
    width: 20px;
    height: 20px;
}

.btn-add-vehicle:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0, 212, 255, 0.5);
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

/* Empty State */
.empty-state {
    background: var(--bg-card);
    backdrop-filter: blur(20px);
    border: 1px solid var(--border-color);
    border-radius: 24px;
    padding: 4rem 2rem;
    text-align: center;
}

.empty-icon-wrapper {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto 2rem;
}

.empty-icon {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.2), rgba(0, 255, 170, 0.2));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid rgba(0, 212, 255, 0.3);
    position: relative;
    z-index: 2;
}

.empty-icon svg {
    width: 60px;
    height: 60px;
    color: var(--accent-primary);
}

.glow-ring {
    position: absolute;
    inset: -10px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    opacity: 0.3;
    filter: blur(20px);
    animation: pulse 3s ease-in-out infinite;
}

.empty-title {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.75rem;
}

.empty-description {
    font-size: 1rem;
    color: var(--text-secondary);
    margin-bottom: 2rem;
}

.btn-empty-cta {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1.25rem 2.5rem;
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    color: var(--bg-primary);
    text-decoration: none;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1.125rem;
    transition: all 0.3s ease;
}

.btn-empty-cta svg {
    width: 20px;
    height: 20px;
}

.btn-empty-cta:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(0, 212, 255, 0.5);
}

/* Vehicles Grid */
.vehicles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 2rem;
}

.vehicle-card {
    background: var(--bg-card);
    backdrop-filter: blur(20px);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 2rem;
    position: relative;
    overflow: hidden;
    transition: all 0.4s ease;
}

.vehicle-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--accent-primary), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.vehicle-card:hover {
    transform: translateY(-10px);
    border-color: rgba(0, 212, 255, 0.3);
    box-shadow: 0 20px 60px rgba(0, 212, 255, 0.2);
}

.vehicle-card:hover::before {
    opacity: 1;
}

.card-glow {
    position: absolute;
    inset: -2px;
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    opacity: 0;
    filter: blur(20px);
    transition: opacity 0.3s ease;
    z-index: -1;
}

.vehicle-card:hover .card-glow {
    opacity: 0.1;
}

.primary-badge {
    position: absolute;
    top: 1.5rem;
    right: 5.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, rgba(255, 215, 0, 0.2), rgba(255, 165, 0, 0.2));
    border: 1px solid rgba(255, 215, 0, 0.4);
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    color: #ffd700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.primary-badge svg {
    width: 14px;
    height: 14px;
}

/* Vehicle Header */
.vehicle-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.vehicle-icon {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.2), rgba(0, 255, 170, 0.2));
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(0, 212, 255, 0.3);
}

.vehicle-icon svg {
    width: 28px;
    height: 28px;
    color: var(--accent-primary);
}

.vehicle-menu {
    position: relative;
}

.menu-button {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.menu-button:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(0, 212, 255, 0.3);
}

.menu-button svg {
    width: 20px;
    height: 20px;
    color: var(--text-secondary);
}

.menu-dropdown {
    position: absolute;
    right: 0;
    top: calc(100% + 0.5rem);
    width: 220px;
    background: rgba(18, 24, 39, 0.98);
    backdrop-filter: blur(20px);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 0.5rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    z-index: 50;
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    width: 100%;
    padding: 0.75rem 1rem;
    background: none;
    border: none;
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: left;
}

.menu-item:hover {
    background: rgba(0, 212, 255, 0.1);
    color: var(--text-primary);
}

.menu-item svg {
    width: 18px;
    height: 18px;
}

.menu-item.danger {
    color: var(--accent-danger);
}

.menu-item.danger:hover {
    background: rgba(255, 51, 102, 0.1);
}

.menu-divider {
    height: 1px;
    background: var(--border-color);
    margin: 0.5rem 0;
}

/* Vehicle Info */
.vehicle-info {
    margin-bottom: 1.5rem;
}

.vehicle-name {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.vehicle-vin {
    font-size: 0.875rem;
    color: var(--text-tertiary);
    font-family: monospace;
}

/* Vehicle Stats */
.vehicle-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding: 1.5rem;
    background: rgba(0, 212, 255, 0.03);
    border: 1px solid var(--border-color);
    border-radius: 12px;
}

.stat-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.stat-label {
    font-size: 0.75rem;
    color: var(--text-tertiary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.stat-value {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.stat-value svg {
    width: 16px;
    height: 16px;
}

.stat-value.health-excellent {
    color: var(--accent-secondary);
}

.stat-value.health-good {
    color: #ffaa00;
}

.stat-value.health-poor {
    color: var(--accent-danger);
}

/* Activity Metrics */
.activity-metrics {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color);
}

.metric {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.metric-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.metric-icon.maintenance {
    background: rgba(0, 212, 255, 0.1);
}

.metric-icon.services {
    background: rgba(0, 255, 170, 0.1);
}

.metric-icon.expenses {
    background: rgba(255, 51, 102, 0.1);
}

.metric-icon svg {
    width: 20px;
    height: 20px;
    color: var(--accent-primary);
}

.metric-icon.services svg {
    color: var(--accent-secondary);
}

.metric-icon.expenses svg {
    color: var(--accent-danger);
}

.metric-content {
    display: flex;
    flex-direction: column;
}

.metric-value {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1;
}

.metric-label {
    font-size: 0.75rem;
    color: var(--text-tertiary);
}

/* View Details Button */
.btn-view-details {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    width: 100%;
    padding: 1rem;
    background: rgba(0, 212, 255, 0.1);
    border: 1px solid rgba(0, 212, 255, 0.3);
    border-radius: 10px;
    color: var(--accent-primary);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-view-details svg {
    width: 18px;
    height: 18px;
}

.btn-view-details:hover {
    background: rgba(0, 212, 255, 0.2);
    border-color: var(--accent-primary);
    transform: translateX(5px);
}

/* Responsive */
@media (max-width: 768px) {
    .vehicles-grid {
        grid-template-columns: 1fr;
    }
    
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
@endsection