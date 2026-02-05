@extends('layouts.app')

@section('title', 'Reports')

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
        --accent-purple: #b794f6;
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
        --accent-purple: #9333ea;
    }

    .reports-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

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

    .reports-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
    }

    .report-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 2rem;
        text-decoration: none;
        transition: all 0.3s ease;
        animation: fadeInUp 0.6s ease-out backwards;
        display: flex;
        flex-direction: column;
    }

    .report-card:nth-child(1) { animation-delay: 0.1s; }
    .report-card:nth-child(2) { animation-delay: 0.2s; }
    .report-card:nth-child(3) { animation-delay: 0.3s; }
    .report-card:nth-child(4) { animation-delay: 0.4s; }
    .report-card:nth-child(5) { animation-delay: 0.5s; }

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

    .report-card:hover {
        transform: translateY(-4px);
        border-color: var(--accent-cyan);
        box-shadow: 0 8px 30px rgba(0, 212, 255, 0.2);
    }

    .report-card-header {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .report-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .report-icon.blue {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.2), rgba(0, 212, 255, 0.1));
        border: 1px solid rgba(0, 212, 255, 0.3);
    }

    .report-icon.green {
        background: linear-gradient(135deg, rgba(0, 255, 170, 0.2), rgba(0, 255, 170, 0.1));
        border: 1px solid rgba(0, 255, 170, 0.3);
    }

    .report-icon.purple {
        background: linear-gradient(135deg, rgba(183, 148, 246, 0.2), rgba(183, 148, 246, 0.1));
        border: 1px solid rgba(183, 148, 246, 0.3);
    }

    .report-icon svg {
        width: 28px;
        height: 28px;
        color: var(--accent-cyan);
    }

    .report-icon.green svg {
        color: var(--accent-green);
    }

    .report-icon.purple svg {
        color: var(--accent-purple);
    }

    .report-content h3 {
        font-family: 'Orbitron', sans-serif;
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.375rem;
    }

    .report-content p {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .report-link {
        margin-top: auto;
        padding-top: 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--accent-cyan);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .report-card:hover .report-link {
        gap: 0.75rem;
        text-shadow: 0 0 10px var(--accent-cyan);
    }

    @media (max-width: 768px) {
        .reports-container {
            padding: 1.5rem 1rem;
        }

        .page-header h1 {
            font-size: 2rem;
        }

        .reports-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="reports-container">
    <div class="page-header">
        <h1>Reports & Analytics</h1>
        <p>Analyze your vehicle expenses and maintenance</p>
    </div>

    <div class="reports-grid">
        <a href="{{ route('reports.expense-summary') }}" class="report-card">
            <div class="report-card-header">
                <div class="report-icon blue">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="report-content">
                    <h3>Expense Summary</h3>
                    <p>View detailed expense breakdown</p>
                </div>
            </div>
            <div class="report-link">
                View Report
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>

        <a href="{{ route('reports.maintenance-history') }}" class="report-card">
            <div class="report-card-header">
                <div class="report-icon green">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div class="report-content">
                    <h3>Maintenance History</h3>
                    <p>Complete service records</p>
                </div>
            </div>
            <div class="report-link">
                View Report
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>

        @foreach($vehicles as $vehicle)
        <a href="{{ route('reports.vehicle-analytics', $vehicle->id) }}" class="report-card">
            <div class="report-card-header">
                <div class="report-icon purple">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="report-content">
                    <h3>{{ $vehicle->full_name }}</h3>
                    <p>Vehicle analytics</p>
                </div>
            </div>
            <div class="report-link">
                View Analytics
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endsection