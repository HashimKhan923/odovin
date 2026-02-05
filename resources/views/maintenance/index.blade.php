@extends('layouts.app')

@section('title', 'Maintenance')

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
    }

    .maintenance-container {
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

    .page-title-section h1 {
        font-family: 'Orbitron', sans-serif;
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
    }

    .page-title-section p {
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    .add-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.875rem 1.75rem;
        background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
    }

    .add-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(0, 212, 255, 0.5);
    }

    /* Analytics Cards */
    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .analytics-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        animation: fadeInUp 0.6s ease-out backwards;
    }

    .analytics-card:nth-child(1) { animation-delay: 0.1s; }
    .analytics-card:nth-child(2) { animation-delay: 0.2s; }
    .analytics-card:nth-child(3) { animation-delay: 0.3s; }
    .analytics-card:nth-child(4) { animation-delay: 0.4s; }

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

    .analytics-card:hover {
        transform: translateY(-4px);
        border-color: var(--accent-cyan);
        box-shadow: 0 8px 30px rgba(0, 212, 255, 0.2);
    }

    .analytics-card-label {
        font-size: 0.75rem;
        color: var(--text-tertiary);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.75rem;
        font-weight: 600;
    }

    .analytics-card-value {
        font-family: 'Orbitron', sans-serif;
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .analytics-card-value.danger {
        color: var(--accent-danger);
    }

    .analytics-card-value.warning {
        color: var(--accent-warning);
    }

    .analytics-card-description {
        font-size: 0.75rem;
        color: var(--text-secondary);
    }

    /* Filters Section */
    .filters-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        animation: fadeInUp 0.6s ease-out 0.5s backwards;
    }

    .filters-form {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        align-items: flex-end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .filter-label {
        font-size: 0.75rem;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
    }

    .filter-select {
        padding: 0.75rem 1rem;
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        color: var(--text-primary);
        font-size: 0.875rem;
        min-width: 200px;
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

    .filter-buttons {
        display: flex;
        gap: 0.75rem;
    }

    .filter-button {
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
    }

    .filter-button.primary {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.2), rgba(0, 255, 170, 0.2));
        color: var(--accent-cyan);
        border: 1px solid var(--accent-cyan);
    }

    .filter-button.primary:hover {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.3), rgba(0, 255, 170, 0.3));
        box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
    }

    .filter-button.secondary {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .filter-button.secondary:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-primary);
    }

    /* Table Card */
    .table-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
        animation: fadeInUp 0.6s ease-out 0.6s backwards;
    }

    .maintenance-table {
        width: 100%;
        border-collapse: collapse;
    }

    .maintenance-table thead {
        background: rgba(0, 212, 255, 0.05);
        border-bottom: 1px solid var(--border-color);
    }

    .maintenance-table th {
        padding: 1.25rem 1.5rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-tertiary);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .maintenance-table th:last-child {
        text-align: right;
    }

    .maintenance-table tbody tr {
        border-bottom: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .maintenance-table tbody tr:hover {
        background: rgba(0, 212, 255, 0.03);
    }

    .maintenance-table td {
        padding: 1.25rem 1.5rem;
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .maintenance-table td:first-child {
        font-weight: 600;
        color: var(--text-primary);
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .status-badge.completed {
        background: rgba(0, 255, 136, 0.1);
        color: #00ff88;
        border: 1px solid rgba(0, 255, 136, 0.3);
    }

    .status-badge.overdue {
        background: rgba(255, 51, 102, 0.1);
        color: #ff3366;
        border: 1px solid rgba(255, 51, 102, 0.3);
    }

    .status-badge.pending {
        background: rgba(255, 170, 0, 0.1);
        color: #ffaa00;
        border: 1px solid rgba(255, 170, 0, 0.3);
    }

    /* Action Buttons */
    .actions-cell {
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .action-button {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        transition: all 0.3s ease;
        border: 1px solid;
        cursor: pointer;
        text-decoration: none;
    }

    .action-button.complete {
        background: rgba(0, 255, 136, 0.1);
        color: #00ff88;
        border-color: rgba(0, 255, 136, 0.3);
    }

    .action-button.complete:hover {
        background: rgba(0, 255, 136, 0.2);
        box-shadow: 0 4px 15px rgba(0, 255, 136, 0.3);
    }

    .action-button.edit {
        background: rgba(0, 212, 255, 0.1);
        color: var(--accent-cyan);
        border-color: rgba(0, 212, 255, 0.3);
    }

    .action-button.edit:hover {
        background: rgba(0, 212, 255, 0.2);
        box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
    }

    .action-button.delete {
        background: rgba(255, 51, 102, 0.1);
        color: #ff3366;
        border-color: rgba(255, 51, 102, 0.3);
    }

    .action-button.delete:hover {
        background: rgba(255, 51, 102, 0.2);
        box-shadow: 0 4px 15px rgba(255, 51, 102, 0.3);
    }

    /* Empty State */
    .empty-state {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 4rem 2rem;
        text-align: center;
        animation: fadeInUp 0.6s ease-out 0.6s backwards;
    }

    .empty-state-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 255, 170, 0.1));
        border: 2px solid var(--border-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    .empty-state-icon svg {
        width: 40px;
        height: 40px;
        color: var(--accent-cyan);
    }

    .empty-state h3 {
        font-family: 'Orbitron', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.75rem;
    }

    .empty-state p {
        color: var(--text-secondary);
        margin-bottom: 2rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .maintenance-container {
            padding: 1.5rem 1rem;
        }

        .page-header {
            flex-direction: column;
            gap: 1.5rem;
            align-items: flex-start;
        }

        .page-title-section h1 {
            font-size: 2rem;
        }

        .analytics-grid {
            grid-template-columns: 1fr;
        }

        .filters-form {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-select {
            min-width: 100%;
        }

        .filter-buttons {
            flex-direction: column;
        }

        .filter-button {
            width: 100%;
        }

        .table-card {
            overflow-x: auto;
        }

        .maintenance-table {
            min-width: 800px;
        }

        .actions-cell {
            flex-direction: column;
            align-items: flex-end;
        }
    }
</style>

<div class="maintenance-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>Maintenance</h1>
            <p>Track upcoming and completed maintenance tasks</p>
        </div>

        <a href="{{ route('maintenance.create') }}" class="add-button">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Maintenance
        </a>
    </div>

    <!-- Analytics Cards -->
    <div class="analytics-grid">
        <div class="analytics-card">
            <div class="analytics-card-label">This Month</div>
            <div class="analytics-card-value">${{ number_format($analytics['month_cost'], 2) }}</div>
            <div class="analytics-card-description">Total maintenance cost</div>
        </div>

        <div class="analytics-card">
            <div class="analytics-card-label">Overdue Tasks</div>
            <div class="analytics-card-value danger">{{ $analytics['overdue_count'] }}</div>
            <div class="analytics-card-description">Require immediate attention</div>
        </div>

        <div class="analytics-card">
            <div class="analytics-card-label">Due in 30 Days</div>
            <div class="analytics-card-value warning">{{ $analytics['upcoming_30_days'] }}</div>
            <div class="analytics-card-description">Upcoming maintenance</div>
        </div>

        <div class="analytics-card">
            <div class="analytics-card-label">Top Service</div>
            @if($analytics['top_service'])
                <div class="analytics-card-value" style="font-size: 1.25rem;">{{ Str::limit($analytics['top_service']->description, 20) }}</div>
                <div class="analytics-card-description">${{ number_format($analytics['top_service']->total, 2) }} total</div>
            @else
                <div class="analytics-card-value" style="font-size: 1.25rem;">No data</div>
                <div class="analytics-card-description">No services recorded</div>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-card">
        <form method="GET" class="filters-form">
            <div class="filter-group">
                <label class="filter-label">Vehicle</label>
                <select name="vehicle_id" class="filter-select">
                    <option value="">All Vehicles</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                            {{ $vehicle->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select name="status" class="filter-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>

            <div class="filter-buttons">
                <button type="submit" class="filter-button primary">Apply Filters</button>
                <a href="{{ route('maintenance.index') }}" class="filter-button secondary">Clear All</a>
            </div>
        </form>
    </div>

    <!-- Content -->
    @if($schedules->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <h3>No maintenance records found</h3>
            <p>Add your first maintenance task to start tracking</p>
            <a href="{{ route('maintenance.create') }}" class="add-button">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Maintenance
            </a>
        </div>
    @else
        <div class="table-card">
            <table class="maintenance-table">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Vehicle</th>
                        <th>Due Date</th>
                        <th>Due Mileage</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedules as $schedule)
                        <tr>
                            <td>{{ $schedule->service_type }}</td>
                            <td>{{ $schedule->vehicle->full_name }}</td>
                            <td>{{ $schedule->due_date ? $schedule->due_date->format('M d, Y') : 'N/A' }}</td>
                            <td>{{ $schedule->due_mileage ? number_format($schedule->due_mileage).' mi' : 'N/A' }}</td>
                            <td>
                                @if($schedule->status === 'completed')
                                    <span class="status-badge completed">Completed</span>
                                @elseif($schedule->status === 'overdue')
                                    <span class="status-badge overdue">Overdue</span>
                                @else
                                    <span class="status-badge pending">Pending</span>
                                @endif
                            </td>
                            <td>
                                <div class="actions-cell">
                                    @if($schedule->status !== 'completed')
                                        <form action="{{ route('maintenance.complete', $schedule) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Mark this maintenance as completed?')" class="action-button complete">
                                                ✓ Complete
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('maintenance.edit', $schedule) }}" class="action-button edit">
                                        ✎ Edit
                                    </a>

                                    @if(!$schedule->is_system_generated)
                                        <form action="{{ route('maintenance.destroy', $schedule) }}" method="POST" onsubmit="return confirm('Delete this maintenance task?')" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-button delete">
                                                🗑 Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($schedules->hasPages())
            <div style="margin-top: 2rem;">
                {{ $schedules->links() }}
            </div>
        @endif
    @endif
</div>
@endsection