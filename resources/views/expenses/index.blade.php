@extends('layouts.app')

@section('title', 'Expenses')

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

    .expenses-container {
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

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        transition: all 0.3s ease;
        animation: fadeInUp 0.6s ease-out backwards;
    }

    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }

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

    .stat-card:hover {
        transform: translateY(-4px);
        border-color: var(--accent-cyan);
        box-shadow: 0 8px 30px rgba(0, 212, 255, 0.2);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .stat-icon.blue {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.2), rgba(0, 212, 255, 0.1));
        border: 1px solid rgba(0, 212, 255, 0.3);
    }

    .stat-icon.green {
        background: linear-gradient(135deg, rgba(0, 255, 170, 0.2), rgba(0, 255, 170, 0.1));
        border: 1px solid rgba(0, 255, 170, 0.3);
    }

    .stat-icon svg {
        width: 28px;
        height: 28px;
        color: var(--accent-cyan);
    }

    .stat-icon.green svg {
        color: var(--accent-green);
    }

    .stat-content {
        flex: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-family: 'Orbitron', sans-serif;
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    /* Filters */
    .filters-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        animation: fadeInUp 0.6s ease-out 0.3s backwards;
    }

    .filters-form {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: flex-end;
    }

    .filter-select,
    .filter-input {
        padding: 0.75rem 1rem;
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        color: var(--text-primary);
        font-size: 0.875rem;
        min-width: 180px;
        transition: all 0.3s ease;
    }

    .filter-select:focus,
    .filter-input:focus {
        outline: none;
        border-color: var(--accent-cyan);
        box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
    }

    .filter-select option {
        background: var(--card-bg);
        color: var(--text-primary);
    }

    .filter-button {
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
        border: 1px solid;
    }

    .filter-button.primary {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.2), rgba(0, 255, 170, 0.2));
        color: var(--accent-cyan);
        border-color: var(--accent-cyan);
    }

    .filter-button.primary:hover {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.3), rgba(0, 255, 170, 0.3));
        box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
    }

    .filter-button.secondary {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-secondary);
        border-color: var(--border-color);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .filter-button.secondary:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-primary);
    }

    /* Table */
    .table-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
        animation: fadeInUp 0.6s ease-out 0.4s backwards;
    }

    .expenses-table {
        width: 100%;
        border-collapse: collapse;
    }

    .expenses-table thead {
        background: rgba(0, 212, 255, 0.05);
        border-bottom: 1px solid var(--border-color);
    }

    .expenses-table th {
        padding: 1.25rem 1.5rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-tertiary);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .expenses-table th:last-child {
        text-align: right;
    }

    .expenses-table tbody tr {
        border-bottom: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .expenses-table tbody tr:hover {
        background: rgba(0, 212, 255, 0.03);
    }

    .expenses-table td {
        padding: 1.25rem 1.5rem;
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .category-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: capitalize;
        background: rgba(0, 212, 255, 0.1);
        color: var(--accent-cyan);
        border: 1px solid rgba(0, 212, 255, 0.3);
    }

    .amount-value {
        font-family: 'Orbitron', sans-serif;
        font-weight: 700;
        color: var(--text-primary);
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

    .action-button {
        background: none;
        border: none;
        color: var(--accent-danger);
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: 'Chakra Petch', sans-serif;
    }

    .action-button:hover {
        text-shadow: 0 0 10px var(--accent-danger);
    }

    /* Empty State */
    .empty-state {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 4rem 2rem;
        text-align: center;
        animation: fadeInUp 0.6s ease-out 0.4s backwards;
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
        .expenses-container {
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

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .filters-form {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-select,
        .filter-input {
            min-width: 100%;
        }

        .table-card {
            overflow-x: auto;
        }

        .expenses-table {
            min-width: 800px;
        }
    }
</style>

<div class="expenses-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>Expenses</h1>
            <p>Track all your vehicle expenses</p>
        </div>

        <a href="{{ route('expenses.create') }}" class="add-button">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Expense
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Expenses</div>
                <div class="stat-value">${{ number_format($totalExpenses, 2) }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">This Month</div>
                <div class="stat-value">${{ number_format($monthExpenses, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-card">
        <form method="GET" class="filters-form">
            <select name="vehicle_id" class="filter-select">
                <option value="">All Vehicles</option>
                @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                        {{ $vehicle->full_name }}
                    </option>
                @endforeach
            </select>
            
            <select name="category" class="filter-select">
                <option value="">All Categories</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <input type="date" name="start_date" value="{{ request('start_date') }}" 
                   class="filter-input" placeholder="Start Date">
            
            <input type="date" name="end_date" value="{{ request('end_date') }}" 
                   class="filter-input" placeholder="End Date">
            
            <button type="submit" class="filter-button primary">Filter</button>
            <a href="{{ route('expenses.index') }}" class="filter-button secondary">Clear</a>
        </form>
    </div>

    <!-- Content -->
    @if($expenses->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3>No expenses yet</h3>
            <p>Start tracking your vehicle expenses</p>
            <a href="{{ route('expenses.create') }}" class="add-button">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add First Expense
            </a>
        </div>
    @else
        <div class="table-card">
            <table class="expenses-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Vehicle</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $expense)
                    <tr>
                        <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                        <td>{{ $expense->vehicle->full_name }}</td>
                        <td>
                            <span class="category-badge">{{ ucfirst($expense->category) }}</span>
                        </td>
                        <td>{{ $expense->description }}</td>
                        <td>
                            <span class="amount-value">${{ number_format($expense->amount, 2) }}</span>
                        </td>
                        <td>
                            <div class="actions-cell">
                                <a href="{{ route('expenses.edit', $expense) }}" class="action-link">Edit</a>
                                <form action="{{ route('expenses.destroy', $expense) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this expense?')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-button">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($expenses->hasPages())
            <div style="margin-top: 2rem;">
                {{ $expenses->links() }}
            </div>
        @endif
    @endif
</div>
@endsection