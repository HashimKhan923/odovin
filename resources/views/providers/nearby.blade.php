@extends('layouts.app')

@section('title', 'Nearby Service Providers')

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

    .nearby-container {
        max-width: 1800px;
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

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .page-header h1 {
        font-family: 'Orbitron', sans-serif;
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--accent-cyan);
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .back-link:hover {
        text-shadow: 0 0 10px var(--accent-cyan);
        transform: translateX(-4px);
    }

    .back-link svg {
        width: 20px;
        height: 20px;
    }

    .location-info {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 12px;
        margin-bottom: 2rem;
    }

    .location-icon {
        width: 24px;
        height: 24px;
        color: var(--accent-cyan);
    }

    .location-text {
        flex: 1;
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    .location-text strong {
        color: var(--text-primary);
        font-weight: 700;
    }

    /* Filters Bar */
    .filters-bar {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
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

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        align-items: end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .filter-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .filter-select {
        padding: 0.875rem 1rem;
        background: var(--input-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        color: var(--text-primary);
        font-size: 0.875rem;
        font-family: 'Chakra Petch', sans-serif;
        transition: all 0.3s ease;
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--accent-cyan);
        background: rgba(0, 212, 255, 0.08);
        box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
    }

    .filter-select option {
        background: var(--card-bg);
        color: var(--text-primary);
    }

    /* Map & List Layout */
    .content-layout {
        display: grid;
        grid-template-columns: 600px 1fr;
        gap: 2rem;
        min-height: 700px;
    }

    /* Providers List */
    .providers-list {
        animation: fadeInUp 0.6s ease-out 0.2s backwards;
    }

    .providers-scroll {
        max-height: 700px;
        overflow-y: auto;
        padding-right: 1rem;
    }

    .providers-scroll::-webkit-scrollbar {
        width: 8px;
    }

    .providers-scroll::-webkit-scrollbar-track {
        background: var(--input-bg);
        border-radius: 4px;
    }

    .providers-scroll::-webkit-scrollbar-thumb {
        background: var(--accent-cyan);
        border-radius: 4px;
    }

    .providers-scroll::-webkit-scrollbar-thumb:hover {
        background: var(--accent-green);
    }

    .provider-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .provider-card:hover,
    .provider-card.active {
        transform: translateX(4px);
        border-color: var(--accent-cyan);
        box-shadow: 0 8px 30px rgba(0, 212, 255, 0.2);
    }

    .provider-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
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

    .badge-distance {
        background: rgba(0, 212, 255, 0.15);
        color: var(--accent-cyan);
        border: 1px solid rgba(0, 212, 255, 0.3);
        font-size: 0.875rem;
        font-family: 'Orbitron', sans-serif;
    }

    .badge-verified {
        background: rgba(0, 255, 170, 0.15);
        color: var(--accent-green);
        border: 1px solid rgba(0, 255, 170, 0.3);
    }

    .badge svg {
        width: 14px;
        height: 14px;
    }

    .provider-rating {
        text-align: right;
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
        gap: 0.5rem;
        margin-bottom: 1rem;
        font-size: 0.875rem;
        color: var(--text-secondary);
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

    .btn-directions {
        padding: 0.75rem 1rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        color: var(--text-secondary);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .btn-directions:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-primary);
    }

    .btn-directions svg {
        width: 20px;
        height: 20px;
    }

    /* Map Card */
    .map-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1rem;
        position: sticky;
        top: 2rem;
        height: fit-content;
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

    #map {
        width: 100%;
        height: 650px;
        border-radius: 12px;
        overflow: hidden;
    }

    /* Empty State */
    .empty-state {
        grid-column: 1 / -1;
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 4rem 2rem;
        text-align: center;
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
        margin-bottom: 2rem;
    }

    .empty-state .btn-view {
        display: inline-block;
        width: auto;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .content-layout {
            grid-template-columns: 1fr;
        }

        .map-card {
            position: relative;
            top: 0;
            order: -1;
        }

        #map {
            height: 400px;
        }

        .providers-scroll {
            max-height: none;
        }
    }

    @media (max-width: 768px) {
        .nearby-container {
            padding: 1.5rem 1rem;
        }

        .page-header h1 {
            font-size: 2rem;
        }

        .header-content {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .filters-bar {
            padding: 1rem;
        }

        .filters-grid {
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

<div class="nearby-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <div>
                <h1>Nearby Service Providers</h1>
            </div>
            <a href="{{ route('providers.index') }}" class="back-link">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to All Providers
            </a>
        </div>

        <!-- Location Info -->
        <div class="location-info">
            <svg class="location-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <div class="location-text">
                <strong>{{ $providers->count() }} providers found</strong> within your selected radius
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-bar">
        <form method="GET">
            <input type="hidden" name="latitude" value="{{ request('latitude') }}">
            <input type="hidden" name="longitude" value="{{ request('longitude') }}">
            
            <div class="filters-grid">
                <div class="filter-group">
                    <label for="radius" class="filter-label">Radius</label>
                    <select name="radius" id="radius" class="filter-select" onchange="this.form.submit()">
                        <option value="5" {{ request('radius') == '5' ? 'selected' : '' }}>Within 5 miles</option>
                        <option value="10" {{ request('radius') == '10' ? 'selected' : '' }}>Within 10 miles</option>
                        <option value="25" {{ request('radius') == '25' || !request('radius') ? 'selected' : '' }}>Within 25 miles</option>
                        <option value="50" {{ request('radius') == '50' ? 'selected' : '' }}>Within 50 miles</option>
                        <option value="100" {{ request('radius') == '100' ? 'selected' : '' }}>Within 100 miles</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="type" class="filter-label">Provider Type</label>
                    <select name="type" id="type" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Types</option>
                        <option value="ev_specialist" {{ request('type') == 'ev_specialist' ? 'selected' : '' }}>EV Specialist</option>
                        <option value="mechanic" {{ request('type') == 'mechanic' ? 'selected' : '' }}>Mechanic</option>
                        <option value="dealership" {{ request('type') == 'dealership' ? 'selected' : '' }}>Dealership</option>
                        <option value="body_shop" {{ request('type') == 'body_shop' ? 'selected' : '' }}>Body Shop</option>
                        <option value="detailing" {{ request('type') == 'detailing' ? 'selected' : '' }}>Detailing</option>
                        <option value="towing" {{ request('type') == 'towing' ? 'selected' : '' }}>Towing</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Options</label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.875rem 0;">
                        <input type="checkbox" name="verified" value="1" {{ request('verified') ? 'checked' : '' }} 
                               onchange="this.form.submit()"
                               style="width: 20px; height: 20px; accent-color: var(--accent-cyan); cursor: pointer;">
                        <span style="font-size: 0.875rem; color: var(--text-secondary);">Verified Only</span>
                    </label>
                </div>
            </div>
        </form>
    </div>

    @if($providers->isEmpty())
        <!-- Empty State -->
        <div class="empty-state">
            <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <h3>No Providers Found Nearby</h3>
            <p>Try increasing your search radius or check back later</p>
            <a href="{{ route('providers.index') }}" class="btn-view">
                Browse All Providers
            </a>
        </div>
    @else
        <!-- Map & List Layout -->
        <div class="content-layout">
            <!-- Providers List -->
            <div class="providers-list">
                <div class="providers-scroll">
                    @foreach($providers as $provider)
                    <div class="provider-card" data-provider-id="{{ $provider->id }}" 
                         data-lat="{{ $provider->latitude }}" 
                         data-lng="{{ $provider->longitude }}"
                         onclick="selectProvider({{ $provider->id }})">
                        <div class="provider-header">
                            <div>
                                <h3 class="provider-name">{{ $provider->name }}</h3>
                                <div class="provider-badges">
                                    <span class="badge badge-distance">
                                        📍 {{ number_format($provider->distance, 1) }} mi
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
                            <div>📍 {{ $provider->address }}, {{ $provider->city }}, {{ $provider->state }}</div>
                            <div>📞 {{ $provider->phone }}</div>
                            <div>🔧 {{ ucfirst(str_replace('_', ' ', $provider->type)) }}</div>
                        </div>

                        <div class="provider-actions">
                            <a href="{{ route('providers.show', $provider) }}" class="btn-view">
                                View Details & Book
                            </a>
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $provider->latitude }},{{ $provider->longitude }}" 
                               target="_blank" 
                               class="btn-directions" 
                               title="Get Directions">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Map -->
            <div class="map-card">
                <div class="map-header">
                    <h3 class="map-title">Map View</h3>
                </div>
                <div id="map"></div>
            </div>
        </div>
    @endif
</div>

<script>
let map;
let markers = {};
let infoWindows = {};

function initMap() {
    const userLat = {{ request('latitude') }};
    const userLng = {{ request('longitude') }};
    
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 11,
        center: { lat: userLat, lng: userLng },
        styles: [
            {
                featureType: 'all',
                elementType: 'geometry',
                stylers: [{ color: '#1a202c' }]
            },
            {
                featureType: 'all',
                elementType: 'labels.text.fill',
                stylers: [{ color: '#ffffff' }]
            },
            {
                featureType: 'all',
                elementType: 'labels.text.stroke',
                stylers: [{ color: '#000000' }]
            },
            {
                featureType: 'water',
                elementType: 'geometry',
                stylers: [{ color: '#0a3d62' }]
            }
        ]
    });
    
    // User location marker
    new google.maps.Marker({
        position: { lat: userLat, lng: userLng },
        map: map,
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 12,
            fillColor: '#00d4ff',
            fillOpacity: 1,
            strokeColor: '#ffffff',
            strokeWeight: 3
        },
        title: 'Your Location',
        zIndex: 1000
    });
    
    // Provider markers
    const providers = @json($providers);
    providers.forEach(provider => {
        const position = { lat: parseFloat(provider.latitude), lng: parseFloat(provider.longitude) };
        
        const marker = new google.maps.Marker({
            position: position,
            map: map,
            title: provider.name,
            icon: {
                url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="50" viewBox="0 0 40 50">
                        <path fill="${provider.is_verified ? '#00ffaa' : '#ffaa00'}" stroke="#ffffff" stroke-width="2"
                              d="M20 0 C9 0 0 9 0 20 C0 32 20 50 20 50 S40 32 40 20 C40 9 31 0 20 0 Z"/>
                        <circle cx="20" cy="20" r="8" fill="#ffffff"/>
                    </svg>
                `),
                scaledSize: new google.maps.Size(40, 50),
                anchor: new google.maps.Point(20, 50)
            }
        });
        
        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div style="padding: 0.75rem; color: #000; min-width: 200px;">
                    <strong style="font-size: 1rem;">${provider.name}</strong><br>
                    <small style="color: #666;">${provider.city}, ${provider.state}</small><br>
                    <small style="color: #666;">📍 ${provider.distance.toFixed(1)} miles away</small><br>
                    <div style="margin-top: 0.5rem;">
                        <strong style="color: #ffaa00;">⭐ ${provider.rating}</strong>
                        <small style="color: #999;"> (${provider.total_reviews} reviews)</small>
                    </div>
                    <a href="/providers/${provider.id}" 
                       style="display: inline-block; margin-top: 0.5rem; padding: 0.5rem 1rem; 
                              background: linear-gradient(135deg, #00d4ff, #00ffaa); color: white; 
                              text-decoration: none; border-radius: 6px; font-size: 0.875rem;">
                        View Details
                    </a>
                </div>
            `
        });
        
        markers[provider.id] = marker;
        infoWindows[provider.id] = infoWindow;
        
        marker.addListener('click', () => {
            // Close all info windows
            Object.values(infoWindows).forEach(iw => iw.close());
            // Open this one
            infoWindow.open(map, marker);
            // Highlight card
            selectProvider(provider.id);
        });
    });
}

function selectProvider(providerId) {
    // Remove active class from all cards
    document.querySelectorAll('.provider-card').forEach(card => {
        card.classList.remove('active');
    });
    
    // Add active class to selected card
    const card = document.querySelector(`[data-provider-id="${providerId}"]`);
    if (card) {
        card.classList.add('active');
        card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    
    // Close all info windows and open selected one
    Object.values(infoWindows).forEach(iw => iw.close());
    if (infoWindows[providerId]) {
        infoWindows[providerId].open(map, markers[providerId]);
        map.panTo(markers[providerId].getPosition());
    }
}

// Load Google Maps
@if(!$providers->isEmpty())
if (!window.google) {
    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY', 'YOUR_GOOGLE_MAPS_API_KEY') }}&callback=initMap`;
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
} else {
    initMap();
}
@endif
</script>
@endsection