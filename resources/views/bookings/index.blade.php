@extends('layouts.app')

@section('title', 'Service Bookings')

@section('content')
<style>
/* Use global theme variables from layout */
.bookings-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.page-header h1 {
    font-family: 'Orbitron', sans-serif;
    font-size: 2rem;
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

.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
    color: white;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(0, 212, 255, 0.5);
}

.filter-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    backdrop-filter: blur(20px);
}

.filter-form {
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
    min-width: 200px;
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

.btn-secondary {
    padding: 0.75rem 1.5rem;
    background: rgba(0, 212, 255, 0.1);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background: rgba(0, 212, 255, 0.15);
    border-color: var(--accent-cyan);
}

.empty-state {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 4rem 2rem;
    text-align: center;
    backdrop-filter: blur(20px);
}

.empty-state svg {
    width: 80px;
    height: 80px;
    color: var(--text-tertiary);
    margin: 0 auto 1.5rem;
}

.empty-state h3 {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.75rem;
}

.empty-state p {
    color: var(--text-secondary);
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
}

.bookings-grid {
    display: grid;
    gap: 1.5rem;
}

.booking-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 1.5rem;
    backdrop-filter: blur(20px);
    transition: all 0.3s ease;
}

.booking-card:hover {
    transform: translateY(-4px);
    border-color: var(--accent-cyan);
    box-shadow: 0 8px 30px rgba(0, 212, 255, 0.2);
}

.booking-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
}

.booking-content {
    flex: 1;
}

.booking-title-row {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.booking-title {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--text-primary);
}

.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: capitalize;
}

.status-completed {
    background: rgba(0, 255, 136, 0.15);
    color: var(--accent-green);
    border: 1px solid rgba(0, 255, 136, 0.3);
}

.status-confirmed {
    background: rgba(0, 212, 255, 0.15);
    color: var(--accent-cyan);
    border: 1px solid rgba(0, 212, 255, 0.3);
}

.status-in_progress {
    background: rgba(255, 170, 0, 0.15);
    color: #ffaa00;
    border: 1px solid rgba(255, 170, 0, 0.3);
}

.status-cancelled {
    background: rgba(255, 51, 102, 0.15);
    color: #ff3366;
    border: 1px solid rgba(255, 51, 102, 0.3);
}

.status-pending {
    background: rgba(122, 132, 153, 0.15);
    color: var(--text-secondary);
    border: 1px solid var(--border-color);
}

.booking-detail {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
}

.booking-detail strong {
    color: var(--text-primary);
}

.booking-actions {
    display: flex;
    gap: 0.75rem;
}

.action-btn {
    padding: 0.5rem;
    background: rgba(0, 212, 255, 0.1);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    color: var(--accent-cyan);
    transition: all 0.3s ease;
}

.action-btn:hover {
    background: rgba(0, 212, 255, 0.2);
    border-color: var(--accent-cyan);
}

.action-btn svg {
    width: 20px;
    height: 20px;
}

@media (max-width: 768px) {
    .bookings-container {
        padding: 1.5rem 1rem;
    }

    .page-header {
        flex-direction: column;
    }

    .page-header h1 {
        font-size: 1.5rem;
    }

    .filter-form {
        flex-direction: column;
    }

    .filter-select {
        width: 100%;
    }
}
</style>

<div class="bookings-container">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1>Service Bookings</h1>
            <p>Manage your service appointments</p>
        </div>
        <a href="{{ route('bookings.create') }}" class="btn-primary">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Booking
        </a>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" class="filter-form">
            <select name="vehicle_id" class="filter-select">
                <option value="">All Vehicles</option>
                @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                        {{ $vehicle->full_name }}
                    </option>
                @endforeach
            </select>
            
            <select name="status" class="filter-select">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            
            <button type="submit" class="btn-secondary">Filter</button>
            <a href="{{ route('bookings.index') }}" class="btn-secondary" style="text-decoration: none;">Clear</a>
        </form>
    </div>

    <!-- Bookings List -->
    @if($bookings->isEmpty())
        <div class="empty-state">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3>No bookings yet</h3>
            <p>Book your first service appointment</p>
            <a href="{{ route('bookings.create') }}" class="btn-primary">Create Booking</a>
        </div>
    @else
        <div class="bookings-grid">
            @foreach($bookings as $booking)
            <div class="booking-card">
                <div class="booking-header">
                    <div class="booking-content">
                        <div class="booking-title-row">
                            <h3 class="booking-title">{{ $booking->service_type }}</h3>
                            <span class="status-badge status-{{ $booking->status }}">
                                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                            </span>
                        </div>
                        
                        <p class="booking-detail">{{ $booking->vehicle->full_name }}</p>
                        <p class="booking-detail">
                            <strong>Provider:</strong> {{ $booking->serviceProvider->name }}
                        </p>
                        <p class="booking-detail">
                            <strong>Scheduled:</strong> {{ $booking->scheduled_date->format('M d, Y g:i A') }}
                        </p>
                        @if($booking->estimated_cost)
                            <p class="booking-detail">
                                <strong>Estimated Cost:</strong> ${{ number_format($booking->estimated_cost, 2) }}
                            </p>
                        @endif
                    </div>
                    
                    <div class="booking-actions">
                        <a href="{{ route('bookings.show', $booking) }}" class="action-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        @if(in_array($booking->status, ['pending', 'confirmed']))
                            <a href="{{ route('bookings.edit', $booking) }}" class="action-btn">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div style="margin-top: 2rem;">
            {{ $bookings->links() }}
        </div>
    @endif
</div>
@endsection