@extends('layouts.app')

@section('title', 'Expense Summary')

@section('content')
<style>
    :root[data-theme="dark"] {
        --page-bg: #0a0e1a;
        --card-bg: rgba(26, 32, 48, 0.8);
        --border-color: rgba(0, 212, 255, 0.1);
        --input-bg: rgba(0, 212, 255, 0.05);
        --text-primary: #ffffff;
        --text-secondary: rgba(255, 255, 255, 0.7);
        --text-tertiary: rgba(255, 255, 255, 0.5);
        --accent-cyan: #00d4ff;
        --accent-green: #00ffaa;
    }

    :root[data-theme="light"] {
        --page-bg: #f8fafc;
        --card-bg: rgba(255, 255, 255, 0.9);
        --border-color: rgba(0, 0, 0, 0.1);
        --input-bg: rgba(0, 0, 0, 0.02);
        --text-primary: #1a1f36;
        --text-secondary: rgba(26, 31, 54, 0.7);
        --text-tertiary: rgba(26, 31, 54, 0.5);
        --accent-cyan: #0066ff;
        --accent-green: #00cc88;
    }

    .report-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--accent-cyan);
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .back-link:hover {
        gap: 0.75rem;
        text-shadow: 0 0 10px var(--accent-cyan);
    }

    .page-title {
        font-family: 'Orbitron', sans-serif;
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 2rem;
    }

    .filters-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .filters-form {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: flex-end;
    }

    .filter-select, .filter-input {
        padding: 0.75rem 1rem;
        background: var(--input-bg);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        color: var(--text-primary);
        font-size: 0.875rem;
        min-width: 200px;
        transition: all 0.3s ease;
    }

    .filter-select:focus, .filter-input:focus {
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
        background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
    }

    .filter-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(0, 212, 255, 0.5);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
    }

    .stat-card:hover {
        transform: translateY(-4px);
        border-color: var(--accent-cyan);
        box-shadow: 0 8px 30px rgba(0, 212, 255, 0.2);
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
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .data-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 1.5rem;
    }

    .data-card h2 {
        font-family: 'Orbitron', sans-serif;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
    }

    .data-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid var(--border-color);
    }

    .data-item:last-child {
        border-bottom: none;
    }

    .data-label {
        font-size: 0.875rem;
        color: var(--text-secondary);
        text-transform: capitalize;
    }

    .data-value {
        font-family: 'Orbitron', sans-serif;
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    @media (max-width: 768px) {
        .report-container {
            padding: 1.5rem 1rem;
        }

        .page-title {
            font-size: 2rem;
        }

        .filters-form {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-select, .filter-input {
            min-width: 100%;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="report-container">
    <a href="{{ route('reports.index') }}" class="back-link">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Reports
    </a>

    <h1 class="page-title">Expense Summary</h1>

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
            
            <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" 
                   class="filter-input" placeholder="Start Date">
            
            <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" 
                   class="filter-input" placeholder="End Date">
            
            <button type="submit" class="filter-button">Filter</button>
        </form>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Expenses</div>
            <div class="stat-value">${{ number_format($totalExpenses, 2) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Transactions</div>
            <div class="stat-value">{{ $expenses->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Average Expense</div>
            <div class="stat-value">${{ $expenses->count() > 0 ? number_format($totalExpenses / $expenses->count(), 2) : '0.00' }}</div>
        </div>
    </div>

    <div class="data-card">
        <h2>Expenses by Category</h2>
        @forelse($byCategory as $category => $total)
            <div class="data-item">
                <span class="data-label">{{ ucfirst($category) }}</span>
                <span class="data-value">${{ number_format($total, 2) }}</span>
            </div>
        @empty
            <p style="color: var(--text-tertiary); text-align: center; padding: 2rem;">No data available</p>
        @endforelse
    </div>

    <div class="data-card">
        <h2>Monthly Breakdown</h2>
        @forelse($byMonth as $month => $total)
            <div class="data-item">
                <span class="data-label">{{ date('F Y', strtotime($month . '-01')) }}</span>
                <span class="data-value">${{ number_format($total, 2) }}</span>
            </div>
        @empty
            <p style="color: var(--text-tertiary); text-align: center; padding: 2rem;">No data available</p>
        @endforelse
    </div>
</div>
@endsection