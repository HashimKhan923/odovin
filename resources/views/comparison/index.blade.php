@extends('layouts.app')

@section('title', 'Compare Vehicles')

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
        --accent-warning: #ffaa00;
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
        --accent-warning: #ff9500;
    }

    .comparison-container {
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

    /* Warning Card */
    .warning-card {
        background: linear-gradient(135deg, rgba(255, 170, 0, 0.1), rgba(255, 170, 0, 0.05));
        border: 1px solid rgba(255, 170, 0, 0.3);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
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

    .warning-card svg {
        width: 32px;
        height: 32px;
        color: var(--accent-warning);
        flex-shrink: 0;
    }

    .warning-card p {
        color: var(--accent-warning);
        font-size: 0.875rem;
        font-weight: 600;
    }

    /* Vehicle Grid */
    .vehicle-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .vehicle-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 2px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        animation: fadeInUp 0.6s ease-out backwards;
        position: relative;
        overflow: hidden;
    }

    .vehicle-card:nth-child(1) { animation-delay: 0.2s; }
    .vehicle-card:nth-child(2) { animation-delay: 0.3s; }
    .vehicle-card:nth-child(3) { animation-delay: 0.4s; }
    .vehicle-card:nth-child(4) { animation-delay: 0.5s; }
    .vehicle-card:nth-child(5) { animation-delay: 0.6s; }
    .vehicle-card:nth-child(6) { animation-delay: 0.7s; }

    .vehicle-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--accent-cyan), var(--accent-green));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .vehicle-card:hover {
        transform: translateY(-4px);
        border-color: var(--accent-cyan);
        box-shadow: 0 8px 30px rgba(0, 212, 255, 0.2);
    }

    .vehicle-card.selected {
        border-color: var(--accent-cyan);
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.08), rgba(0, 255, 170, 0.08));
        box-shadow: 0 8px 30px rgba(0, 212, 255, 0.3);
    }

    .vehicle-card.selected::before {
        opacity: 1;
    }

    .vehicle-card-header {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .vehicle-checkbox {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        border: 2px solid var(--border-color);
        background: var(--card-bg);
        cursor: pointer;
        transition: all 0.3s ease;
        flex-shrink: 0;
        margin-top: 0.125rem;
        position: relative;
    }

    .vehicle-card.selected .vehicle-checkbox {
        background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
        border-color: var(--accent-cyan);
    }

    .vehicle-checkbox::after {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0);
        color: white;
        font-weight: 700;
        font-size: 0.875rem;
        transition: transform 0.2s ease;
    }

    .vehicle-card.selected .vehicle-checkbox::after {
        transform: translate(-50%, -50%) scale(1);
    }

    .vehicle-info h3 {
        font-family: 'Orbitron', sans-serif;
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.375rem;
    }

    .vehicle-detail {
        font-size: 0.875rem;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .vehicle-detail svg {
        width: 16px;
        height: 16px;
        color: var(--accent-cyan);
    }

    /* Real checkbox hidden but functional */
    .vehicle-card input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
    }

    /* Error Message */
    .error-message {
        background: rgba(255, 51, 102, 0.1);
        border: 1px solid rgba(255, 51, 102, 0.3);
        border-radius: 12px;
        padding: 1rem 1.5rem;
        margin-top: 1rem;
        color: #ff3366;
        font-size: 0.875rem;
        font-weight: 600;
    }

    /* Submit Button */
    .submit-section {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 1.5rem;
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        animation: fadeInUp 0.6s ease-out 0.8s backwards;
    }

    .selected-count {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .selected-count span {
        font-family: 'Orbitron', sans-serif;
        font-weight: 700;
        color: var(--accent-cyan);
    }

    .compare-button {
        padding: 0.875rem 2rem;
        background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .compare-button:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(0, 212, 255, 0.5);
    }

    .compare-button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .compare-button svg {
        width: 20px;
        height: 20px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .comparison-container {
            padding: 1.5rem 1rem;
        }

        .page-header h1 {
            font-size: 2rem;
        }

        .vehicle-grid {
            grid-template-columns: 1fr;
        }

        .submit-section {
            flex-direction: column;
            align-items: stretch;
        }

        .compare-button {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="comparison-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Compare Vehicles</h1>
        <p>Select vehicles to compare their performance and costs</p>
    </div>

    @if ($vehicles->count() < 2)
        <!-- Warning Card -->
        <div class="warning-card">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p>You need at least two vehicles to use comparison.</p>
        </div>
    @else
        <form method="POST" action="{{ route('comparison.compare') }}" id="comparisonForm">
            @csrf

            <!-- Vehicle Grid -->
            <div class="vehicle-grid">
                @foreach ($vehicles as $vehicle)
                    <label class="vehicle-card" data-vehicle-id="{{ $vehicle->id }}">
                        <div class="vehicle-card-header">
                            <div class="vehicle-checkbox"></div>
                            <div class="vehicle-info">
                                <h3>{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}</h3>
                                <div class="vehicle-detail">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                    Mileage: {{ number_format($vehicle->current_mileage) }} km
                                </div>
                            </div>
                        </div>
                        <input type="checkbox" name="vehicle_ids[]" value="{{ $vehicle->id }}">
                    </label>
                @endforeach
            </div>

            @error('vehicle_ids')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <!-- Submit Section -->
            <div class="submit-section">
                <div class="selected-count">
                    <span id="selectedCount">0</span> vehicles selected
                </div>
                <button type="submit" class="compare-button" id="compareButton" disabled>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Compare Selected Vehicles
                </button>
            </div>
        </form>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('comparisonForm');
    if (!form) return;

    const cards = document.querySelectorAll('.vehicle-card');
    const compareButton = document.getElementById('compareButton');
    const selectedCountEl = document.getElementById('selectedCount');

    function updateSelection() {
        const checkedBoxes = form.querySelectorAll('input[type="checkbox"]:checked');
        const count = checkedBoxes.length;
        
        selectedCountEl.textContent = count;
        compareButton.disabled = count < 2;
    }

    cards.forEach(card => {
        const checkbox = card.querySelector('input[type="checkbox"]');
        
        // Handle card click
        card.addEventListener('click', function(e) {
            // Prevent double trigger if clicking the actual input
            if (e.target === checkbox) {
                return;
            }
            
            // Prevent default label behavior
            e.preventDefault();
            
            // Toggle checkbox
            checkbox.checked = !checkbox.checked;
            
            // Trigger change event
            checkbox.dispatchEvent(new Event('change'));
        });

        // Handle checkbox change
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
            updateSelection();
        });
    });

    // Initial update
    updateSelection();
});
</script>
@endsection