@extends('layouts.app')

@section('title', $vehicle->full_name)

@section('content')
<div class="vehicle-show-page">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Navigation -->
        <div class="back-nav fade-in-up">
            <a href="{{ route('vehicles.index') }}" class="back-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Back to Garage</span>
            </a>
        </div>

        <!-- Page Header -->
        <div class="show-header fade-in-up" style="animation-delay: 0.1s;">
            <div>
                <h1 class="vehicle-title">{{ $vehicle->full_name }}</h1>
                <p class="vehicle-vin">VIN: {{ $vehicle->vin }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn-edit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>Edit Vehicle</span>
                </a>
                @unless($vehicle->is_primary)
                <form action="{{ route('vehicles.set-primary', $vehicle) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-set-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        <span>Set as Primary</span>
                    </button>
                </form>
                @endunless
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid-show fade-in-up" style="animation-delay: 0.2s;">
            <div class="stat-card-show">
                <div class="stat-icon-show total">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="stat-label-show">Total Expenses</p>
                    <p class="stat-value-show">${{ number_format($stats['total_expenses'], 2) }}</p>
                </div>
            </div>

            <div class="stat-card-show">
                <div class="stat-icon-show month">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="stat-label-show">This Month</p>
                    <p class="stat-value-show">${{ number_format($stats['this_month_expenses'], 2) }}</p>
                </div>
            </div>

            <div class="stat-card-show">
                <div class="stat-icon-show services">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <p class="stat-label-show">Total Services</p>
                    <p class="stat-value-show">{{ $stats['total_services'] }}</p>
                </div>
            </div>

            <div class="stat-card-show">
                <div class="stat-icon-show maintenance">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="stat-label-show">Pending Maintenance</p>
                    <p class="stat-value-show">{{ $stats['pending_maintenance'] }}</p>
                </div>
            </div>
        </div>

        <!-- AI Insights -->
        <div class="ai-section fade-in-up" style="animation-delay: 0.3s;">
            <div class="ai-header">
                <div>
                    <h2 class="ai-title">
                        <span class="ai-icon">✨</span>
                        AI Vehicle Insights
                    </h2>
                    <p class="ai-subtitle">Personalized analysis powered by machine learning</p>
                </div>
                @if($vehicle->aiInsight)
                <div class="peace-score">
                    <span class="score-label">Peace of Mind</span>
                    <span class="score-value">{{ $vehicle->aiInsight->peace_of_mind_score }}/100</span>
                </div>
                @endif
            </div>

            @if(!$vehicle->aiInsight)
            <div class="ai-empty">
                <p>AI insights haven't been generated yet.</p>
                <form method="POST" action="{{ route('vehicles.ai-insights.generate', $vehicle) }}">
                    @csrf
                    <button class="btn-generate">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span>Generate AI Insights</span>
                    </button>
                </form>
            </div>
            @else
            @php($ai = $vehicle->aiInsight)
            
            <div class="ai-content">
                <!-- Summary -->
                <div class="insight-summary">
                    <h3>Overview</h3>
                    <p>{{ $ai->summary }}</p>
                </div>

                <!-- Known Issues -->
                @if(count($ai->known_issues) > 0)
                <div class="insight-block">
                    <h3 class="insight-heading">Known Issues</h3>
                    <div class="issues-list">
                        @foreach($ai->known_issues as $issue)
                        <div class="issue-item">
                            <div class="issue-indicator {{ strtolower($issue['severity']) }}"></div>
                            <div class="issue-details">
                                <p class="issue-name">{{ $issue['issue'] }}</p>
                                <p class="issue-meta">{{ $issue['mileage_range'] }} • {{ $issue['severity'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Tips -->
                <div class="tips-grid">
                    <div class="tip-block">
                        <h3>Maintenance Tips</h3>
                        <ul class="tip-list">
                            @foreach($ai->maintenance_tips as $tip)
                            <li>{{ $tip }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="tip-block">
                        <h3>Owner Tips</h3>
                        <ul class="tip-list">
                            @foreach($ai->owner_tips as $tip)
                            <li>{{ $tip }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Cost Expectations -->
                <div class="cost-block">
                    <h3>Ownership Cost Expectations</h3>
                    <div class="cost-info">
                        <div class="cost-row">
                            <span class="cost-label">Yearly Range:</span>
                            <span class="cost-value">{{ $ai->cost_expectations['yearly_range'] ?? 'N/A' }}</span>
                        </div>
                        <div class="cost-row">
                            <span class="cost-label">High Cost Parts:</span>
                            <span class="cost-value">{{ implode(', ', $ai->cost_expectations['high_cost_parts'] ?? []) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Main Content Grid -->
        <div class="content-grid fade-in-up" style="animation-delay: 0.4s;">
            <!-- Vehicle Info -->
            <div class="info-panel">
                <h2 class="panel-title">Vehicle Information</h2>
                <div class="info-list">
                    <div class="info-row">
                        <span class="info-label">Make & Model</span>
                        <span class="info-value">{{ $vehicle->make }} {{ $vehicle->model }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Year</span>
                        <span class="info-value">{{ $vehicle->year }}</span>
                    </div>
                    @if($vehicle->trim)
                    <div class="info-row">
                        <span class="info-label">Trim</span>
                        <span class="info-value">{{ $vehicle->trim }}</span>
                    </div>
                    @endif
                    @if($vehicle->engine)
                    <div class="info-row">
                        <span class="info-label">Engine</span>
                        <span class="info-value">{{ $vehicle->engine }}</span>
                    </div>
                    @endif
                    @if($vehicle->transmission)
                    <div class="info-row">
                        <span class="info-label">Transmission</span>
                        <span class="info-value">{{ $vehicle->transmission }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="info-label">Current Mileage</span>
                        <span class="info-value">{{ number_format($vehicle->current_mileage) }} mi</span>
                    </div>
                    @if($vehicle->license_plate)
                    <div class="info-row">
                        <span class="info-label">License Plate</span>
                        <span class="info-value">{{ $vehicle->license_plate }}</span>
                    </div>
                    @endif
                    @if($vehicle->color)
                    <div class="info-row">
                        <span class="info-label">Color</span>
                        <span class="info-value">{{ $vehicle->color }}</span>
                    </div>
                    @endif
                </div>

                <!-- Update Mileage -->
                <div class="mileage-update">
                    <form action="{{ route('vehicles.update-mileage', $vehicle) }}" method="POST">
                        @csrf
                        <label class="mileage-label">Update Mileage</label>
                        <div class="mileage-group">
                            <input type="number" name="mileage" min="{{ $vehicle->current_mileage }}" 
                                   value="{{ $vehicle->current_mileage }}" class="mileage-input">
                            <button type="submit" class="btn-update">Update</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column -->
            <div class="right-column">
                <!-- Documents -->
                <div class="doc-panel">
                    <div class="panel-header">
                        <h3 class="panel-subtitle">Documents</h3>
                        <a href="{{ route('vehicles.documents.create', $vehicle) }}" class="link-add">+ Add</a>
                    </div>
                    @if($vehicle->documents->isEmpty())
                    <p class="empty-text">No documents uploaded</p>
                    @else
                    <div class="doc-list">
                        @foreach($vehicle->documents->take(5) as $document)
                        <div class="doc-item">
                            <div class="doc-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="doc-info">
                                <p class="doc-title">{{ $document->title }}</p>
                                <p class="doc-meta">
                                    {{ ucfirst($document->type) }}
                                    @if($document->expiry_date) • Expires {{ $document->expiry_date->format('M d, Y') }}@endif
                                </p>
                            </div>
                            <a href="{{ route('vehicles.documents.download', [$vehicle, $document]) }}" class="doc-download">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    <a href="{{ route('vehicles.documents.index', $vehicle) }}" class="view-all">View All Documents</a>
                    @endif
                </div>

                <!-- Maintenance -->
                <div class="maint-panel">
                    <div class="panel-header">
                        <h3 class="panel-subtitle">Upcoming Maintenance</h3>
                        <a href="{{ route('maintenance.index', ['vehicle_id' => $vehicle->id]) }}" class="link-add">View All</a>
                    </div>
                    @if($vehicle->maintenanceSchedules->isEmpty())
                    <p class="empty-text">No scheduled maintenance</p>
                    @else
                    <div class="maint-list">
                        @foreach($vehicle->maintenanceSchedules as $maintenance)
                        <div class="maint-item">
                            <div class="maint-info">
                                <p class="maint-title">{{ $maintenance->service_type }}</p>
                                <p class="maint-meta">
                                    Due: {{ $maintenance->due_date?->format('M d, Y') ?? 'N/A' }}
                                    @if($maintenance->due_mileage) | {{ number_format($maintenance->due_mileage) }} mi @endif
                                </p>
                            </div>
                            <span class="maint-badge {{ $maintenance->status }}">
                                {{ ucfirst($maintenance->status) }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Activity Grid -->
        <div class="activity-grid fade-in-up" style="animation-delay: 0.5s;">
            <!-- Services -->
            <div class="activity-panel">
                <h3 class="activity-title">Recent Services</h3>
                @if($vehicle->serviceRecords->isEmpty())
                <p class="empty-text">No service history</p>
                @else
                <div class="activity-list">
                    @foreach($vehicle->serviceRecords as $record)
                    <div class="activity-row">
                        <div>
                            <p class="activity-name">{{ $record->service_type }}</p>
                            <p class="activity-date">{{ $record->service_date->format('M d, Y') }}</p>
                        </div>
                        <span class="activity-amount">${{ number_format($record->cost, 2) }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Expenses -->
            <div class="activity-panel">
                <h3 class="activity-title">Recent Expenses</h3>
                @if($vehicle->expenses->isEmpty())
                <p class="empty-text">No expenses recorded</p>
                @else
                <div class="activity-list">
                    @foreach($vehicle->expenses as $expense)
                    <div class="activity-row">
                        <div>
                            <p class="activity-name">{{ $expense->description }}</p>
                            <p class="activity-date">{{ $expense->expense_date->format('M d, Y') }}</p>
                        </div>
                        <span class="activity-amount">${{ number_format($expense->amount, 2) }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300;400;500;600;700&family=Orbitron:wght@400;500;600;700;800&display=swap');

:root {
    --bg-primary: #0a0e1a;
    --bg-card: rgba(26, 32, 48, 0.8);
    --accent-primary: #00d4ff;
    --accent-secondary: #00ffaa;
    --accent-danger: #ff3366;
    --accent-warning: #ffaa00;
    --text-primary: #ffffff;
    --text-secondary: rgba(255, 255, 255, 0.7);
    --text-tertiary: rgba(255, 255, 255, 0.5);
    --border-color: rgba(0, 212, 255, 0.1);
}

.vehicle-show-page {
    min-height: 100vh;
    padding: 2rem 0;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.fade-in-up {
    animation: fadeInUp 0.6s ease-out forwards;
    opacity: 0;
}

/* Back Nav */
.back-nav {
    margin-bottom: 2rem;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.back-link:hover {
    color: var(--accent-primary);
    gap: 0.75rem;
}

.back-link svg {
    width: 20px;
    height: 20px;
}

/* Header */
.show-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.vehicle-title {
    font-family: 'Orbitron', sans-serif;
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.vehicle-vin {
    font-size: 0.875rem;
    color: var(--text-tertiary);
    font-family: monospace;
}

.header-actions {
    display: flex;
    gap: 1rem;
}

.btn-edit, .btn-set-primary {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1.75rem;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-edit {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--border-color);
    color: var(--text-primary);
}

.btn-edit:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(0, 212, 255, 0.3);
}

.btn-set-primary {
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    color: var(--bg-primary);
}

.btn-set-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(0, 212, 255, 0.5);
}

.btn-edit svg, .btn-set-primary svg {
    width: 20px;
    height: 20px;
}

/* Stats Grid */
.stats-grid-show {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.stat-card-show {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    background: var(--bg-card);
    backdrop-filter: blur(20px);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 1.75rem;
    transition: all 0.3s ease;
}

.stat-card-show:hover {
    border-color: rgba(0, 212, 255, 0.3);
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0, 212, 255, 0.2);
}

.stat-icon-show {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-icon-show.total {
    background: linear-gradient(135deg, rgba(255, 51, 102, 0.2), rgba(255, 51, 102, 0.1));
}

.stat-icon-show.month {
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.2), rgba(0, 212, 255, 0.1));
}

.stat-icon-show.services {
    background: linear-gradient(135deg, rgba(0, 255, 170, 0.2), rgba(0, 255, 170, 0.1));
}

.stat-icon-show.maintenance {
    background: linear-gradient(135deg, rgba(255, 170, 0, 0.2), rgba(255, 170, 0, 0.1));
}

.stat-icon-show svg {
    width: 28px;
    height: 28px;
    color: var(--accent-primary);
}

.stat-label-show {
    font-size: 0.875rem;
    color: var(--text-tertiary);
    margin-bottom: 0.25rem;
}

.stat-value-show {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
}

/* AI Section */
.ai-section {
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.05), rgba(0, 255, 170, 0.05));
    border: 1px solid rgba(0, 212, 255, 0.2);
    border-radius: 20px;
    padding: 2.5rem;
    margin-bottom: 3rem;
}

.ai-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.ai-title {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
}

.ai-icon {
    font-size: 2rem;
    filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.5));
}

.ai-subtitle {
    font-size: 0.95rem;
    color: var(--text-secondary);
}

.peace-score {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    padding: 1rem 1.5rem;
    background: rgba(0, 255, 136, 0.1);
    border: 1px solid rgba(0, 255, 136, 0.3);
    border-radius: 12px;
}

.score-label {
    font-size: 0.75rem;
    color: var(--text-tertiary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.score-value {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--accent-secondary);
}

.ai-empty {
    text-align: center;
    padding: 2rem;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 12px;
}

.ai-empty p {
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
}

.btn-generate {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 2rem;
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    color: var(--bg-primary);
    border: none;
    border-radius: 10px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-generate:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0, 212, 255, 0.5);
}

.btn-generate svg {
    width: 20px;
    height: 20px;
}

.ai-content {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.insight-summary {
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid var(--border-color);
    border-radius: 12px;
}

.insight-summary h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.75rem;
}

.insight-summary p {
    color: var(--text-secondary);
    line-height: 1.7;
}

.insight-heading {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.issues-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.issue-item {
    display: flex;
    gap: 1rem;
    padding: 1.25rem;
    background: rgba(255, 170, 0, 0.05);
    border-left: 3px solid var(--accent-warning);
    border-radius: 8px;
}

.issue-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-top: 6px;
    flex-shrink: 0;
}

.issue-indicator.high {
    background: var(--accent-danger);
    box-shadow: 0 0 10px var(--accent-danger);
}

.issue-indicator.medium {
    background: var(--accent-warning);
    box-shadow: 0 0 10px var(--accent-warning);
}

.issue-indicator.low {
    background: var(--accent-secondary);
    box-shadow: 0 0 10px var(--accent-secondary);
}

.issue-name {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.issue-meta {
    font-size: 0.875rem;
    color: var(--text-tertiary);
}

.tips-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.tip-block {
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid var(--border-color);
    border-radius: 12px;
}

.tip-block h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.tip-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.tip-list li {
    padding-left: 1.5rem;
    margin-bottom: 0.75rem;
    color: var(--text-secondary);
    position: relative;
    line-height: 1.6;
}

.tip-list li::before {
    content: '▸';
    position: absolute;
    left: 0;
    color: var(--accent-primary);
}

.cost-block {
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid var(--border-color);
    border-radius: 12px;
}

.cost-block h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.cost-info {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.cost-row {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
}

.cost-label {
    font-weight: 600;
    color: var(--text-secondary);
}

.cost-value {
    color: var(--text-primary);
}

/* Content Grid */
.content-grid {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 2rem;
    margin-bottom: 3rem;
}

@media (max-width: 1024px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
}

.info-panel, .doc-panel, .maint-panel {
    background: var(--bg-card);
    backdrop-filter: blur(20px);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 2rem;
}

.panel-title {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1.5rem;
}

.info-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-size: 0.875rem;
    color: var(--text-tertiary);
}

.info-value {
    font-weight: 600;
    color: var(--text-primary);
    text-align: right;
}

.mileage-update {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--border-color);
}

.mileage-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 0.75rem;
}

.mileage-group {
    display: flex;
    gap: 0.75rem;
}

.mileage-input {
    flex: 1;
    padding: 0.75rem 1rem;
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    color: var(--text-primary);
    font-family: 'Orbitron', monospace;
}

.mileage-input:focus {
    outline: none;
    border-color: var(--accent-primary);
}

.btn-update {
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    color: var(--bg-primary);
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-update:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 212, 255, 0.4);
}

.right-column {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.panel-subtitle {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
}

.link-add {
    color: var(--accent-primary);
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.link-add:hover {
    text-shadow: 0 0 10px var(--accent-primary);
}

.empty-text {
    color: var(--text-secondary);
    font-size: 0.875rem;
    text-align: center;
    padding: 2rem;
}

.doc-list, .maint-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.doc-item, .maint-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    transition: all 0.3s ease;
}

.doc-item:hover, .maint-item:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(0, 212, 255, 0.3);
}

.doc-icon {
    width: 40px;
    height: 40px;
    background: rgba(0, 212, 255, 0.1);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.doc-icon svg {
    width: 20px;
    height: 20px;
    color: var(--accent-primary);
}

.doc-info, .maint-info {
    flex: 1;
}

.doc-title, .maint-title {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.doc-meta, .maint-meta {
    font-size: 0.75rem;
    color: var(--text-tertiary);
}

.doc-download {
    width: 36px;
    height: 36px;
    background: rgba(0, 212, 255, 0.1);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.doc-download:hover {
    background: rgba(0, 212, 255, 0.2);
    transform: translateY(-2px);
}

.doc-download svg {
    width: 18px;
    height: 18px;
    color: var(--accent-primary);
}

.view-all {
    display: block;
    text-align: center;
    margin-top: 1rem;
    color: var(--accent-primary);
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.view-all:hover {
    text-shadow: 0 0 10px var(--accent-primary);
}

.maint-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.maint-badge.overdue {
    background: rgba(255, 51, 102, 0.2);
    color: var(--accent-danger);
}

.maint-badge.upcoming {
    background: rgba(255, 170, 0, 0.2);
    color: var(--accent-warning);
}

.maint-badge.completed {
    background: rgba(0, 255, 136, 0.2);
    color: var(--accent-secondary);
}

/* Activity Grid */
.activity-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
}

@media (max-width: 768px) {
    .activity-grid {
        grid-template-columns: 1fr;
    }
}

.activity-panel {
    background: var(--bg-card);
    backdrop-filter: blur(20px);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 2rem;
}

.activity-title {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1.5rem;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.activity-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.activity-row:last-child {
    border-bottom: none;
}

.activity-name {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.activity-date {
    font-size: 0.875rem;
    color: var(--text-tertiary);
}

.activity-amount {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--accent-primary);
    flex-shrink: 0;
}
</style>
@endsection