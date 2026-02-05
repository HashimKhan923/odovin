@extends('layouts.app')

@section('title', 'Maintenance History')

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
    }

    .filter-select {
        padding: 0.75rem 1rem;
        background: var(--input-bg);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        color: var(--text-primary);
        font-size: 0.875rem;
        min-width: 250px;
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

    .table-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
    }

    .records-table {
        width: 100%;
        border-collapse: collapse;
    }

    .records-table thead {
        background: rgba(0, 212, 255, 0.05);
        border-bottom: 1px solid var(--border-color);
    }

    .records-table th {
        padding: 1.25rem 1.5rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-tertiary);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .records-table tbody tr {
        border-bottom: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .records-table tbody tr:hover {
        background: rgba(0, 212, 255, 0.03);
    }

    .records-table td {
        padding: 1.25rem 1.5rem;
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .cost-value {
        font-family: 'Orbitron', sans-serif;
        font-weight: 700;
        color: var(--text-primary);
    }

    .empty-state {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-state p {
        color: var(--text-tertiary);
        font-size: 1rem;
    }

    @media (max-width: 768px) {
        .report-container {
            padding: 1.5rem 1rem;
        }

        .page-title {
            font-size: 2rem;
        }

        .table-card {
            overflow-x: auto;
        }

        .records-table {
            min-width: 800px;
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

    <h1 class="page-title">Maintenance History</h1>

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
            <button type="submit" class="filter-button">Filter</button>
        </form>
    </div>

    @if($records->isEmpty())
        <div class="empty-state">
            <p>No service records found</p>
        </div>
    @else
        <div class="table-card">
            <table class="records-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Vehicle</th>
                        <th>Service</th>
                        <th>Provider</th>
                        <th>Mileage</th>
                        <th>Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $record)
                    <tr>
                        <td>{{ $record->service_date->format('M d, Y') }}</td>
                        <td>{{ $record->vehicle->full_name }}</td>
                        <td>{{ $record->service_type }}</td>
                        <td>{{ $record->serviceProvider->name ?? 'N/A' }}</td>
                        <td>{{ number_format($record->mileage_at_service) }}</td>
                        <td>
                            <span class="cost-value">${{ number_format($record->cost, 2) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection