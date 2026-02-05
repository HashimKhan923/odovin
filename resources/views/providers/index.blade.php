@extends('layouts.app')

@section('title', 'Service Providers')

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
        --accent-danger: #ff3366;
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
        --accent-danger: #ff3366;
    }

    .providers-container {
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

    /* Search Card */
    .search-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        animation: fadeInUp 0.6s ease-out 0.1s backwards;
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

    .search-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .form-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .form-input,
    .form-select {
        padding: 0.875rem 1rem;
        background: var(--input-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        color: var(--text-primary);
        font-size: 0.875rem;
        font-family: 'Chakra Petch', sans-serif;
        transition: all 0.3s ease;
    }

    .form-input:focus,
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

    .checkbox-container {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.875rem 0;
    }

    .checkbox-input {
        width: 20px;
        height: 20px;
        accent-color: var(--accent-cyan);
        cursor: pointer;
    }

    .checkbox-label {
        font-size: 0.875rem;
        color: var(--text-secondary);
        cursor: pointer;
    }

    .search-actions {
        display: flex;
        gap: 1rem;
    }

    .btn {
        padding: 0.875rem 1.75rem;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        font-family: 'Chakra Petch', sans-serif;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
        color: white;
        box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(0, 212, 255, 0.5);
    }

    .btn-secondary {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-primary);
    }

    /* Empty State */
    .empty-state {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 4rem 2rem;
        text-align: center;
        animation: fadeInUp 0.6s ease-out 0.2s backwards;
    }

    .empty-state-icon {
        width: 96px;
        height: 96px;
        margin: 0 auto 1.5rem;
        opacity: 0.3;
    }

    .empty-state h3 {
        font-family: 'Orbitron', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    /* Provider Grid */
    .providers-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 1.5rem;
    }

    .provider-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        animation: fadeInUp 0.6s ease-out backwards;
    }

    .provider-card:nth-child(1) { animation-delay: 0.2s; }
    .provider-card:nth-child(2) { animation-delay: 0.25s; }
    .provider-card:nth-child(3) { animation-delay: 0.3s; }
    .provider-card:nth-child(4) { animation-delay: 0.35s; }
    .provider-card:nth-child(5) { animation-delay: 0.4s; }
    .provider-card:nth-child(6) { animation-delay: 0.45s; }

    .provider-card:hover {
        transform: translateY(-4px);
        border-color: var(--accent-cyan);
        box-shadow: 0 8px 30px rgba(0, 212, 255, 0.2);
    }

    .provider-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
    }

    .provider-title {
        flex: 1;
    }

    .provider-name {
        font-family: 'Orbitron', sans-serif;
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .provider-badges {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 0.75rem;
    }

    .badge {
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
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
        width: 14px;
        height: 14px;
    }

    .provider-rating {
        text-align: right;
        min-width: 70px;
    }

    .rating-value {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        font-family: 'Orbitron', sans-serif;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--accent-warning);
        margin-bottom: 0.25rem;
    }

    .rating-value svg {
        width: 20px;
        height: 20px;
        margin-right: 0.25rem;
    }

    .rating-reviews {
        font-size: 0.75rem;
        color: var(--text-tertiary);
    }

    .provider-info {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .info-item {
        display: flex;
        align-items: start;
        gap: 0.75rem;
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    .info-icon {
        width: 20px;
        height: 20px;
        color: var(--accent-cyan);
        flex-shrink: 0;
        margin-top: 2px;
    }

    .provider-actions {
        display: flex;
        gap: 0.75rem;
    }

    .btn-view {
        flex: 1;
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
        color: white;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 600;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
    }

    .btn-view:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(0, 212, 255, 0.5);
    }

    /* Pagination */
    .pagination-wrapper {
        margin-top: 2rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .providers-container {
            padding: 1.5rem 1rem;
        }

        .page-header h1 {
            font-size: 2rem;
        }

        .search-card {
            padding: 1.5rem;
        }

        .search-grid {
            grid-template-columns: 1fr;
        }

        .search-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }

        .providers-grid {
            grid-template-columns: 1fr;
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

<div class="providers-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Service Providers</h1>
        <p>Find trusted mechanics and service centers</p>
    </div>

    <!-- Search and Filters -->
    <div class="search-card">
        <form method="GET">
            <div class="search-grid">
                <div class="form-group">
                    <label for="search" class="form-label">Search</label>
                    <input 
                        type="text" 
                        id="search" 
                        name="search" 
                        value="{{ request('search') }}"
                        class="form-input"
                        placeholder="Name, city, or services..."
                    />
                </div>
                
                <div class="form-group">
                    <label for="type" class="form-label">Type</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">All Types</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Filters</label>
                    <div class="checkbox-container">
                        <input 
                            type="checkbox" 
                            name="verified" 
                            value="1" 
                            id="verified"
                            {{ request('verified') ? 'checked' : '' }}
                            class="checkbox-input"
                        />
                        <label for="verified" class="checkbox-label">Verified Providers Only</label>
                    </div>
                </div>
            </div>
            
            <div class="search-actions">
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 0.5rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Search
                </button>
                <a href="{{ route('providers.index') }}" class="btn btn-secondary">
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    @if($providers->isEmpty())
        <!-- Empty State -->
        <div class="empty-state">
            <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <h3>No Providers Found</h3>
            <p>Try adjusting your search filters or check back later</p>
        </div>
    @else
        <!-- Providers Grid -->
        <div class="providers-grid">
            @foreach($providers as $provider)
            <div class="provider-card">
                <div class="provider-header">
                    <div class="provider-title">
                        <h3 class="provider-name">{{ $provider->name }}</h3>
                        <div class="provider-badges">
                            <span class="badge badge-type">
                                {{ ucfirst(str_replace('_', ' ', $provider->type)) }}
                            </span>
                            @if($provider->is_verified)
                                <span class="badge badge-verified">
                                    <svg fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                    </svg>
                                    Verified
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

                <div class="provider-info">
                    <div class="info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>{{ $provider->city }}, {{ $provider->state }}</span>
                    </div>
                    <div class="info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span>{{ $provider->phone }}</span>
                    </div>
                </div>

                <div class="provider-actions">
                    <a href="{{ route('providers.show', $provider) }}" class="btn-view">
                        View Details & Book
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $providers->links() }}
        </div>
    @endif
</div>
@endsection