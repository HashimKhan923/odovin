@extends('layouts.app')

@section('title', 'Booking Details')

@section('content')
<style>
.booking-details-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--accent-cyan);
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
}

.back-link:hover {
    gap: 0.75rem;
    text-shadow: 0 0 10px var(--accent-cyan);
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.page-header-left h1 {
    font-family: 'Orbitron', sans-serif;
    font-size: 2rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
}

.page-header-left p {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.header-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
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

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    border: none;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-secondary {
    background: rgba(0, 212, 255, 0.1);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}

.btn-secondary:hover {
    background: rgba(0, 212, 255, 0.15);
    border-color: var(--accent-cyan);
}

.btn-danger {
    background: rgba(255, 51, 102, 0.15);
    color: #ff3366;
    border: 1px solid rgba(255, 51, 102, 0.3);
}

.btn-danger:hover {
    background: rgba(255, 51, 102, 0.25);
}

.details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.detail-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 2rem;
    backdrop-filter: blur(20px);
}

.section-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.detail-item {
    margin-bottom: 1rem;
}

.detail-item:last-child {
    margin-bottom: 0;
}

.detail-label {
    font-size: 0.75rem;
    color: var(--text-tertiary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.375rem;
}

.detail-value {
    font-size: 0.875rem;
    color: var(--text-primary);
    font-weight: 600;
}

.detail-value.large {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.25rem;
    color: var(--accent-cyan);
}

.detail-value a {
    color: var(--accent-cyan);
    text-decoration: none;
}

.detail-value a:hover {
    text-decoration: underline;
}

.rating-section {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 2rem;
    backdrop-filter: blur(20px);
}

.rating-form {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.stars {
    display: flex;
    gap: 0.25rem;
}

.star {
    font-size: 2rem;
    cursor: pointer;
    color: rgba(122, 132, 153, 0.3);
    transition: all 0.2s ease;
}

.star.filled {
    color: #ffaa00;
}

.star:hover {
    transform: scale(1.1);
}

.rating-textarea {
    flex: 1;
    min-width: 250px;
}

.rating-textarea textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    background: var(--input-bg);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    color: var(--text-primary);
    font-family: 'Chakra Petch', sans-serif;
    font-size: 0.875rem;
    resize: vertical;
    min-height: 80px;
}

.btn-primary {
    background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
    color: white;
    font-family: 'Orbitron', sans-serif;
    font-weight: 700;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(0, 212, 255, 0.5);
}

@media (max-width: 768px) {
    .booking-details-container {
        padding: 1.5rem 1rem;
    }

    .details-grid {
        grid-template-columns: 1fr;
    }

    .header-actions {
        flex-direction: column;
        width: 100%;
    }

    .header-actions .btn {
        width: 100%;
        justify-content: center;
    }

    .rating-form {
        flex-direction: column;
        align-items: stretch;
    }

    .rating-textarea {
        width: 100%;
    }
}
</style>

<div class="booking-details-container">
    <a href="{{ route('bookings.index') }}" class="back-link">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Bookings
    </a>

    <div class="page-header">
        <div class="page-header-left">
            <h1>Booking #{{ $booking->booking_number }}</h1>
            <p>{{ $booking->service_type }}</p>
        </div>
        <div class="header-actions">
            <span class="status-badge status-{{ $booking->status }}">
                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
            </span>
            @if(in_array($booking->status, ['pending', 'confirmed']))
                <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-secondary">Edit</a>
                <form action="{{ route('bookings.cancel', $booking) }}" method="POST" 
                      onsubmit="return confirm('Cancel this booking?')" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger">Cancel</button>
                </form>
            @endif
        </div>
    </div>

    <div class="details-grid">
        <!-- Booking Info -->
        <div class="detail-card">
            <div class="section-title">📋 Booking Information</div>
            
            <div class="detail-item">
                <div class="detail-label">Vehicle</div>
                <div class="detail-value">{{ $booking->vehicle->full_name }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Scheduled</div>
                <div class="detail-value">
                    {{ $booking->scheduled_date->format('l, F j, Y \a\t g:i A') }}
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Service Type</div>
                <div class="detail-value">{{ $booking->service_type }}</div>
            </div>

            @if($booking->estimated_cost)
                <div class="detail-item">
                    <div class="detail-label">Estimated Cost</div>
                    <div class="detail-value large">${{ number_format($booking->estimated_cost, 2) }}</div>
                </div>
            @endif

            @if($booking->final_cost)
                <div class="detail-item">
                    <div class="detail-label">Final Cost</div>
                    <div class="detail-value large" style="color: var(--accent-green);">
                        ${{ number_format($booking->final_cost, 2) }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Provider Info -->
        <div class="detail-card">
            <div class="section-title">🏪 Service Provider</div>
            
            <div class="detail-item">
                <div class="detail-label">Provider</div>
                <div class="detail-value">{{ $booking->serviceProvider->name }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Location</div>
                <div class="detail-value">
                    {{ $booking->serviceProvider->city }}, {{ $booking->serviceProvider->state }}
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Phone</div>
                <div class="detail-value">
                    <a href="tel:{{ $booking->serviceProvider->phone }}">
                        {{ $booking->serviceProvider->phone }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($booking->description || $booking->customer_notes)
        <div class="detail-card">
            <div class="section-title">📝 Details</div>
            
            @if($booking->description)
                <div class="detail-item">
                    <div class="detail-label">Description</div>
                    <div class="detail-value">{{ $booking->description }}</div>
                </div>
            @endif

            @if($booking->customer_notes)
                <div class="detail-item">
                    <div class="detail-label">Notes</div>
                    <div class="detail-value">{{ $booking->customer_notes }}</div>
                </div>
            @endif
        </div>
    @endif

    @if($booking->status === 'completed')
        <div class="rating-section" style="margin-top: 1.5rem;">
            <div class="section-title">⭐ Rate Your Experience</div>
            <form action="{{ route('bookings.rate', $booking) }}" method="POST" class="rating-form">
                @csrf
                <div class="stars" id="starRating">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="star {{ $i <= ($booking->rating ?? 0) ? 'filled' : '' }}" 
                              onclick="setRating({{ $i }})">★</span>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="ratingInput" value="{{ $booking->rating ?? 0 }}" />
                
                <div class="rating-textarea">
                    <textarea name="review" placeholder="Share your experience...">{{ $booking->review ?? '' }}</textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">SUBMIT REVIEW</button>
            </form>
        </div>

        <script>
            function setRating(val) {
                document.getElementById('ratingInput').value = val;
                const stars = document.querySelectorAll('#starRating .star');
                stars.forEach((star, index) => {
                    star.classList.toggle('filled', index < val);
                });
            }
        </script>
    @endif
</div>
@endsection