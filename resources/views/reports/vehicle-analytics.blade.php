@extends('layouts.app')

@section('title', 'Vehicle Analytics')

@section('content')
<style>
    :root[data-theme="dark"] {
        --page-bg: #0a0e1a;
        --card-bg: rgba(26, 32, 48, 0.8);
        --border-color: rgba(0, 212, 255, 0.1);
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
        margin-bottom: 0.5rem;
    }

    .page-subtitle {
        color: var(--text-secondary);
        font-size: 0.875rem;
        margin-bottom: 2rem;
    }

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
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .data-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
        gap: 1.5rem;
    }

    .data-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 2rem;
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

        .stats-grid, .data-grid {
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

    <h1 class="page-title">{{ $vehicle->full_name }}</h1>
    <p class="page-subtitle">Complete analytics and statistics</p>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Expenses</div>
            <div class="stat-value">${{ number_format($totalExpenses, 2) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Services</div>
            <div class="stat-value">{{ $serviceCount }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Maintenance Items</div>
            <div class="stat-value">{{ $maintenanceCount }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Current Mileage</div>
            <div class="stat-value">{{ number_format($vehicle->current_mileage) }}</div>
        </div>
    </div>

    <div class="data-grid">
        <div class="data-card">
            <h2>Expenses by Category</h2>
            @forelse($expensesByCategory as $expense)
                <div class="data-item">
                    <span class="data-label">{{ ucfirst($expense->category) }}</span>
                    <span class="data-value">${{ number_format($expense->total, 2) }}</span>
                </div>
            @empty
                <p style="color: var(--text-tertiary); text-align: center; padding: 2rem;">No data available</p>
            @endforelse
        </div>

        <div class="data-card">
            <h2>Monthly Expenses (Last 12 Months)</h2>
            @forelse($monthlyExpenses as $expense)
                <div class="data-item">
                    <span class="data-label">{{ date('M Y', strtotime($expense->month . '-01')) }}</span>
                    <span class="data-value">${{ number_format($expense->total, 2) }}</span>
                </div>
            @empty
                <p style="color: var(--text-tertiary); text-align: center; padding: 2rem;">No data available</p>
            @endforelse
        </div>
    </div>
</div>
@endsection