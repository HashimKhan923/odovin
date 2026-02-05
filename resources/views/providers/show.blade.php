@extends('layouts.app')

@section('title', $provider->name)

@section('content')
<style>
    /* Page Variables */
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
        --accent-warning: #ffaa00;
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
        --accent-warning: #ff9500;
    }

    .provider-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Back Button */
    .back-button {
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

    .back-button:hover {
        text-shadow: 0 0 10px var(--accent-cyan);
        transform: translateX(-4px);
    }

    .back-button svg {
        width: 20px;
        height: 20px;
    }

    /* Page Title */
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

    /* Layout Grid */
    .provider-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 2rem;
    }

    /* Card Styles */
    .card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 2rem;
        animation: fadeInUp 0.6s ease-out backwards;
    }

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

    .card:nth-child(1) { animation-delay: 0.1s; }
    .card:nth-child(2) { animation-delay: 0.2s; }
    .card:nth-child(3) { animation-delay: 0.3s; }

    .card-title {
        font-family: 'Orbitron', sans-serif;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
    }

    /* Provider Header */
    .provider-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 2rem;
    }

    .provider-badges {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .badge {
        padding: 0.375rem 1rem;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .badge-type {
        background: rgba(0, 212, 255, 0.15);
        color: var(--accent-cyan);
        border: 1px solid rgba(0, 212, 255, 0.3);
    }

    .badge-verified {
        background: rgba(0, 255, 170, 0.15);
        color: var(--accent-green);
        border: 1px solid rgba(0, 255, 170, 0.3);
    }

    .badge-verified svg {
        width: 16px;
        height: 16px;
    }

    .provider-rating {
        text-align: right;
        min-width: 100px;
    }

    .rating-value {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        font-family: 'Orbitron', sans-serif;
        font-size: 2rem;
        font-weight: 700;
        color: var(--accent-warning);
        margin-bottom: 0.25rem;
    }

    .rating-value svg {
        width: 28px;
        height: 28px;
        margin-right: 0.5rem;
    }

    .rating-reviews {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    /* Contact Info */
    .contact-section {
        margin-bottom: 2rem;
    }

    .section-title {
        font-family: 'Orbitron', sans-serif;
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1rem;
    }

    .contact-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .contact-item {
        display: flex;
        align-items: start;
        gap: 1rem;
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    .contact-icon {
        width: 24px;
        height: 24px;
        color: var(--accent-cyan);
        flex-shrink: 0;
        margin-top: 2px;
    }

    .contact-item a {
        color: var(--accent-cyan);
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .contact-item a:hover {
        text-shadow: 0 0 10px var(--accent-cyan);
    }

    /* Services */
    .services-text {
        color: var(--text-secondary);
        font-size: 0.875rem;
        line-height: 1.6;
    }

    /* Reviews */
    .reviews-section {
        margin-top: 2rem;
    }

    .review-item {
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }

    .review-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .review-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.75rem;
    }

    .review-stars {
        display: flex;
        gap: 0.25rem;
    }

    .review-stars svg {
        width: 18px;
        height: 18px;
    }

    .review-date {
        font-size: 0.875rem;
        color: var(--text-tertiary);
    }

    .review-text {
        color: var(--text-secondary);
        font-size: 0.875rem;
        line-height: 1.6;
        margin-bottom: 0.5rem;
    }

    .review-service {
        font-size: 0.75rem;
        color: var(--text-tertiary);
    }

    /* Booking Sidebar */
    .booking-sidebar {
        position: sticky;
        top: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .form-select {
        width: 100%;
        padding: 0.875rem 1rem;
        background: var(--input-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        color: var(--text-primary);
        font-size: 0.875rem;
        font-family: 'Chakra Petch', sans-serif;
        transition: all 0.3s ease;
    }

    .form-select:focus {
        outline: none;
        border-color: var(--accent-cyan);
        background: rgba(0, 212, 255, 0.08);
        box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
    }

    .form-select option {
        background: var(--card-bg);
        color: var(--text-primary);
    }

    .btn-book {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
        font-family: 'Chakra Petch', sans-serif;
    }

    .btn-book:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(0, 212, 255, 0.5);
    }

    .contact-divider {
        margin: 2rem 0;
        padding-top: 2rem;
        border-top: 1px solid var(--border-color);
        text-align: center;
    }

    .direct-contact {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-bottom: 0.75rem;
    }

    .phone-number {
        display: block;
        font-family: 'Orbitron', sans-serif;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--accent-cyan);
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .phone-number:hover {
        text-shadow: 0 0 10px var(--accent-cyan);
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .provider-grid {
            grid-template-columns: 1fr;
        }

        .booking-sidebar {
            position: relative;
            top: 0;
        }
    }

    @media (max-width: 768px) {
        .provider-container {
            padding: 1.5rem 1rem;
        }

        .page-title {
            font-size: 2rem;
        }

        .card {
            padding: 1.5rem;
        }

        .provider-header {
            flex-direction: column;
            gap: 1rem;
        }

        .provider-rating {
            text-align: left;
        }
    }
</style>

<div class="provider-container">
    <!-- Back Button -->
    <a href="{{ route('providers.index') }}" class="back-button">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Providers
    </a>

    <!-- Page Title -->
    <h1 class="page-title">{{ $provider->name }}</h1>

    <!-- Grid Layout -->
    <div class="provider-grid">
        <!-- Main Content -->
        <div>
            <!-- Provider Details Card -->
            <div class="card">
                <div class="provider-header">
                    <div>
                        <div class="provider-badges">
                            <span class="badge badge-type">
                                {{ ucfirst(str_replace('_', ' ', $provider->type)) }}
                            </span>
                            @if($provider->is_verified)
                                <span class="badge badge-verified">
                                    <svg fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                    </svg>
                                    Verified Provider
                                </span>
                            @endif
                        </div>
                    </div>
                    @if($provider->rating > 0)
                        <div class="provider-rating">
                            <div class="rating-value">
                                <svg fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                {{ number_format($provider->rating, 1) }}
                            </div>
                            <p class="rating-reviews">{{ $provider->total_reviews }} reviews</p>
                        </div>
                    @endif
                </div>

                <!-- Contact Information -->
                <div class="contact-section">
                    <h3 class="section-title">Contact Information</h3>
                    <div class="contact-list">
                        <div class="contact-item">
                            <svg class="contact-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <a href="tel:{{ $provider->phone }}">{{ $provider->phone }}</a>
                        </div>
                        @if($provider->email)
                        <div class="contact-item">
                            <svg class="contact-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <a href="mailto:{{ $provider->email }}">{{ $provider->email }}</a>
                        </div>
                        @endif
                        <div class="contact-item">
                            <svg class="contact-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>
                                {{ $provider->address }}<br>
                                {{ $provider->city }}, {{ $provider->state }} {{ $provider->zip_code }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Services Offered -->
                @if($provider->services_offered)
                <div>
                    <h3 class="section-title">Services Offered</h3>
                    <p class="services-text">{{ $provider->services_offered }}</p>
                </div>
                @endif
            </div>

            <!-- Reviews Section -->
            @if($provider->bookings->isNotEmpty())
            <div class="card reviews-section">
                <h3 class="card-title">Recent Reviews</h3>
                @foreach($provider->bookings as $booking)
                    <div class="review-item">
                        <div class="review-header">
                            <div class="review-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg fill="currentColor" viewBox="0 0 20 20" style="color: {{ $i <= $booking->rating ? 'var(--accent-warning)' : 'var(--border-color)' }}">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="review-date">{{ $booking->updated_at->format('M d, Y') }}</span>
                        </div>
                        @if($booking->review)
                            <p class="review-text">{{ $booking->review }}</p>
                        @endif
                        <p class="review-service">Service: {{ $booking->service_type }}</p>
                    </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Booking Sidebar -->
        <div class="booking-sidebar">
            <div class="card">
                <h3 class="card-title">Book a Service</h3>
                
                <form action="{{ route('bookings.create') }}" method="GET">
                    <input type="hidden" name="service_provider_id" value="{{ $provider->id }}">
                    
                    <div class="form-group">
                        <label for="vehicle_select" class="form-label">
                            Select Your Vehicle
                        </label>
                        <select id="vehicle_select" name="vehicle_id" required class="form-select">
                            <option value="">Choose a vehicle</option>
                            @foreach($userVehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">
                                    {{ $vehicle->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn-book">
                        Continue to Booking
                    </button>
                </form>

                <div class="contact-divider">
                    <p class="direct-contact">Or call us directly at</p>
                    <a href="tel:{{ $provider->phone }}" class="phone-number">
                        {{ $provider->phone }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection