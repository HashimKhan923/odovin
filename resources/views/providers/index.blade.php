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
        max-width: 1600px;
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
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
        flex-wrap: wrap;
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

    /* Sort Tabs */
    .sort-tabs {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        animation: fadeInUp 0.6s ease-out 0.2s backwards;
        flex-wrap: wrap;
    }

    .sort-tab {
        padding: 0.75rem 1.5rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .sort-tab:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-primary);
    }

    .sort-tab.active {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.2), rgba(0, 255, 170, 0.2));
        border-color: var(--accent-cyan);
        color: var(--accent-cyan);
    }

    .sort-tab svg {
        width: 16px;
        height: 16px;
    }

    /* Map & Grid Container */
    .content-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    .content-layout.with-map {
        grid-template-columns: 1fr 450px;
    }

    /* Map Card */
    .map-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1rem;
        height: fit-content;
        position: sticky;
        top: 2rem;
        animation: fadeInUp 0.6s ease-out 0.3s backwards;
    }

    .map-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .map-title {
        font-family: 'Orbitron', sans-serif;
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .map-toggle {
        padding: 0.5rem 1rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .map-toggle:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-primary);
    }

    #map {
        width: 100%;
        height: 600px;
        border-radius: 12px;
        overflow: hidden;
    }

    .location-status {
        margin-top: 1rem;
        padding: 0.75rem;
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 8px;
        font-size: 0.75rem;
        color: var(--text-secondary);
        text-align: center;
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

    .badge-distance {
        background: rgba(255, 170, 0, 0.15);
        color: var(--accent-warning);
        border: 1px solid rgba(255, 170, 0, 0.3);
    }

    .badge svg {
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
    @media (max-width: 1200px) {
        .content-layout.with-map {
            grid-template-columns: 1fr;
        }

        .map-card {
            position: relative;
            top: 0;
        }

        #map {
            height: 400px;
        }
    }

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

        .sort-tabs {
            overflow-x: auto;
            flex-wrap: nowrap;
            -webkit-overflow-scrolling: touch;
        }

        .sort-tab {
            white-space: nowrap;
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
        <form method="GET" id="search-form">
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
                    <label for="radius" class="form-label">Distance</label>
                    <select name="radius" id="radius" class="form-select">
                        <option value="">Any Distance</option>
                        <option value="5" {{ request('radius') == '5' ? 'selected' : '' }}>Within 5 miles</option>
                        <option value="10" {{ request('radius') == '10' ? 'selected' : '' }}>Within 10 miles</option>
                        <option value="25" {{ request('radius') == '25' ? 'selected' : '' }}>Within 25 miles</option>
                        <option value="50" {{ request('radius') == '50' ? 'selected' : '' }}>Within 50 miles</option>
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
                        <label for="verified" class="checkbox-label">Verified Only</label>
                    </div>
                </div>
            </div>
            
            <input type="hidden" name="latitude" id="latitude" value="{{ request('latitude') }}">
            <input type="hidden" name="longitude" id="longitude" value="{{ request('longitude') }}">
            
            <div class="search-actions">
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 0.5rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Search
                </button>
                <button type="button" id="location-btn" class="btn btn-secondary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 0.5rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Use My Location
                </button>
                <a href="{{ route('providers.index') }}" class="btn btn-secondary">
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Sort Tabs -->
    <div class="sort-tabs">
        <a href="{{ route('providers.index', array_merge(request()->except('sort'), ['sort' => 'rating'])) }}" 
           class="sort-tab {{ request('sort') === 'rating' || !request('sort') ? 'active' : '' }}">
            <svg fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            Highest Rated
        </a>
        
        <a href="{{ route('providers.index', array_merge(request()->except('sort'), ['sort' => 'distance'])) }}" 
           class="sort-tab {{ request('sort') === 'distance' ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Nearest First
        </a>
        
        <a href="{{ route('providers.index', array_merge(request()->except('sort'), ['sort' => 'reviews'])) }}" 
           class="sort-tab {{ request('sort') === 'reviews' ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
            </svg>
            Most Reviewed
        </a>
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
        <!-- Content Layout -->
        <div class="content-layout {{ request('latitude') ? 'with-map' : '' }}">
            <!-- Providers Grid -->
            <div>
                <div class="providers-grid">
                    
                    @foreach($providers as $provider)
                    <div class="provider-card" data-lat="{{ $provider->latitude }}" data-lng="{{ $provider->longitude }}">
                        <div class="provider-header">
                            <div class="provider-title">
                                <h3 class="provider-name">{{ $provider->business_name }}</h3>
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
                                    @if(isset($provider->distance))
                                        <span class="badge badge-distance">
                                            📍 {{ number_format($provider->distance, 1) }} mi
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
                    {{ $providers->appends(request()->query())->links() }}
                </div>
            </div>

            <!-- Map Sidebar -->
            @if(request('latitude'))
            <div class="map-card">
                <div class="map-header">
                    <h3 class="map-title">Map View</h3>
                </div>
                <div id="map"></div>
                <div class="location-status" id="location-status">
                    📍 Showing providers near your location
                </div>
            </div>
            @endif
        </div>
    @endif
</div>

<script>
/* ===============================
   LOCATION BUTTON HANDLING
=================================*/
document.getElementById('location-btn')?.addEventListener('click', function () {

    if (!navigator.geolocation) {
        alert('Geolocation is not supported by your browser.');
        return;
    }

    this.textContent = 'Getting location...';
    this.disabled = true;

    navigator.geolocation.getCurrentPosition(
        (position) => {

            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;

            // Auto-set radius if empty
            if (!document.getElementById('radius').value) {
                document.getElementById('radius').value = '25';
            }

            document.getElementById('search-form').submit();
        },
        (error) => {
            alert('Unable to get your location. Please allow location access.');
            this.textContent = 'Use My Location';
            this.disabled = false;
        }
    );
});


/* ===============================
   GOOGLE MAP INITIALIZATION
=================================*/
@if(request('latitude') && !$providers->isEmpty())

window.initMap = function () {

    const userLat = parseFloat({{ request('latitude') }});
    const userLng = parseFloat({{ request('longitude') }});

    const map = new google.maps.Map(document.getElementById('map'), {
        zoom: 11,
        center: { lat: userLat, lng: userLng }
    });

    /* ===== USER MARKER ===== */
    new google.maps.Marker({
        position: { lat: userLat, lng: userLng },
        map: map,
        title: 'Your Location',
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 8,
            fillColor: '#00d4ff',
            fillOpacity: 1,
            strokeColor: '#ffffff',
            strokeWeight: 2
        }
    });

    /* ===== PROVIDER MARKERS ===== */
    const providers = @json($providers->items());

    providers.forEach(provider => {

        if (!provider.latitude || !provider.longitude) return;

        const marker = new google.maps.Marker({
            position: {
                lat: parseFloat(provider.latitude),
                lng: parseFloat(provider.longitude)
            },
            map: map,
            title: provider.name,
            icon: {
                path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                scale: 5,
                fillColor: provider.is_verified ? '#00ffaa' : '#ffaa00',
                fillOpacity: 1,
                strokeColor: '#ffffff',
                strokeWeight: 1,
                rotation: 180
            }
        });

        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div style="padding:8px; color:#000;">
                    <strong>${provider.name}</strong><br>
                    <small>${provider.city ?? ''}, ${provider.state ?? ''}</small><br>
                    <small>Rating: ${provider.rating ?? 0} ⭐</small>
                </div>
            `
        });

        marker.addListener('click', () => {
            infoWindow.open(map, marker);
        });
    });
};


/* ===============================
   LOAD GOOGLE MAPS SCRIPT
=================================*/
if (!window.google || !window.google.maps) {

    const script = document.createElement('script');
    script.src = "https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap";
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);

} else {
    window.initMap();
}

@endif
</script>

@endsection