@extends('layouts.app')

@section('title', 'Edit Fuel/Charge Log')

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
        --accent-danger: #ff3366;
        --accent-electric: #7c3aed;
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
        --accent-danger: #ff3366;
        --accent-electric: #6d28d9;
    }

    .form-container {
        max-width: 900px;
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
    }

    .page-header h1.electric {
        background: linear-gradient(135deg, var(--accent-electric), var(--accent-cyan));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Form Card */
    .form-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 2rem;
        animation: fadeInUp 0.6s ease-out 0.2s backwards;
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

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .required {
        color: var(--accent-danger);
        font-size: 1rem;
    }

    .form-input,
    .form-select,
    .form-textarea {
        padding: 0.875rem 1rem;
        background: var(--input-bg);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        color: var(--text-primary);
        font-size: 0.875rem;
        font-family: 'Chakra Petch', sans-serif;
        transition: all 0.3s ease;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: var(--accent-cyan);
        background: rgba(0, 212, 255, 0.08);
        box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
    }

    .form-select option {
        background: var(--card-bg);
        color: var(--text-primary);
    }

    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }

    .form-checkbox {
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

    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
    }

    .button {
        padding: 0.875rem 1.75rem;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid;
    }

    .button-secondary {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-secondary);
        border-color: var(--border-color);
    }

    .button-secondary:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-primary);
    }

    .button-primary {
        background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
    }

    .button-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(0, 212, 255, 0.5);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-container {
            padding: 1.5rem 1rem;
        }

        .page-header h1 {
            font-size: 2rem;
        }

        .form-card {
            padding: 1.5rem;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group.full-width {
            grid-column: 1;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .button {
            width: 100%;
        }
    }
</style>

<div class="form-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 id="page-title" class="{{ $fuelLog->vehicle->fuel_type === 'Electric' ? 'electric' : '' }}">
            {{ $fuelLog->vehicle->fuel_type === 'Electric' ? 'Edit Charge Log' : 'Edit Fuel Log' }}
        </h1>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form method="POST" action="{{ route('fuel.update', $fuelLog) }}">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <!-- Vehicle -->
                <div class="form-group full-width">
                    <label for="vehicle_id" class="form-label">
                        Vehicle <span class="required">*</span>
                    </label>
                    <select name="vehicle_id" id="vehicle_id" required class="form-select">
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" 
                                    {{ $fuelLog->vehicle_id == $vehicle->id ? 'selected' : '' }}
                                    data-fuel-type="{{ $vehicle->fuel_type }}">
                                {{ $vehicle->full_name }}
                                @if($vehicle->fuel_type === 'Electric')
                                    ⚡
                                @else
                                    🔥
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Fill Date -->
                <div class="form-group">
                    <label for="fill_date" class="form-label">
                        <span id="date-label">
                            {{ $fuelLog->vehicle->fuel_type === 'Electric' ? 'Charge Date' : 'Fill Date' }}
                        </span> <span class="required">*</span>
                    </label>
                    <input type="date" name="fill_date" id="fill_date" required
                           class="form-input"
                           value="{{ $fuelLog->fill_date->format('Y-m-d') }}">
                </div>

                <!-- Odometer -->
                <div class="form-group">
                    <label for="odometer" class="form-label">
                        Odometer <span class="required">*</span>
                    </label>
                    <input type="number" name="odometer" id="odometer" required
                           placeholder="Current mileage"
                           value="{{ $fuelLog->odometer }}"
                           class="form-input">
                </div>

                <!-- Gallons/kWh -->
                <div class="form-group">
                    <label for="gallons" class="form-label">
                        <span id="quantity-label">
                            {{ $fuelLog->vehicle->fuel_type === 'Electric' ? 'kWh' : 'Gallons' }}
                        </span> <span class="required">*</span>
                    </label>
                    <input type="number" step="0.01" name="gallons" id="gallons" required
                           placeholder="{{ $fuelLog->vehicle->fuel_type === 'Electric' ? 'e.g., 45.50' : 'e.g., 12.50' }}"
                           value="{{ $fuelLog->gallons }}"
                           class="form-input">
                </div>

                <!-- Price per Gallon/kWh -->
                <div class="form-group">
                    <label for="price_per_gallon" class="form-label">
                        <span id="price-label">
                            {{ $fuelLog->vehicle->fuel_type === 'Electric' ? 'Price / kWh' : 'Price / Gallon' }}
                        </span> <span class="required">*</span>
                    </label>
                    <input type="number" step="0.01" name="price_per_gallon" id="price_per_gallon" required
                           placeholder="{{ $fuelLog->vehicle->fuel_type === 'Electric' ? 'e.g., 0.28' : 'e.g., 3.49' }}"
                           value="{{ $fuelLog->price_per_gallon }}"
                           class="form-input">
                </div>

                <!-- Total Cost -->
                <div class="form-group">
                    <label for="total_cost" class="form-label">
                        Total Cost <span class="required">*</span>
                    </label>
                    <input type="number" step="0.01" name="total_cost" id="total_cost" required
                           placeholder="e.g., 43.63"
                           value="{{ $fuelLog->total_cost }}"
                           class="form-input">
                </div>

                <!-- Full Tank/Charge Checkbox -->
                <div class="form-group">
                    <div class="form-checkbox">
                        <input type="checkbox" name="is_full_tank" value="1" 
                               {{ $fuelLog->is_full_tank ? 'checked' : '' }}
                               class="checkbox-input" id="is_full_tank">
                        <label for="is_full_tank" class="checkbox-label" id="full-tank-label">
                            {{ $fuelLog->vehicle->fuel_type === 'Electric' ? 'Full charge' : 'Full tank fill-up' }}
                        </label>
                    </div>
                </div>

                <!-- Charge Type (for electric vehicles) -->
                @if($fuelLog->vehicle->fuel_type === 'Electric')
                <div class="form-group" id="charge-type-group">
                    <label for="charge_type" class="form-label">Charge Type</label>
                    <select name="charge_type" id="charge_type" class="form-select">
                        <option value="">Select charge type</option>
                        <option value="level1" {{ $fuelLog->charge_type === 'level1' ? 'selected' : '' }}>Level 1 (120V)</option>
                        <option value="level2" {{ $fuelLog->charge_type === 'level2' ? 'selected' : '' }}>Level 2 (240V)</option>
                        <option value="dcfast" {{ $fuelLog->charge_type === 'dcfast' ? 'selected' : '' }}>DC Fast Charging</option>
                        <option value="supercharger" {{ $fuelLog->charge_type === 'supercharger' ? 'selected' : '' }}>Supercharger</option>
                    </select>
                </div>
                @else
                <div class="form-group" id="charge-type-group" style="display: none;">
                    <label for="charge_type" class="form-label">Charge Type</label>
                    <select name="charge_type" id="charge_type" class="form-select">
                        <option value="">Select charge type</option>
                        <option value="level1">Level 1 (120V)</option>
                        <option value="level2">Level 2 (240V)</option>
                        <option value="dcfast">DC Fast Charging</option>
                        <option value="supercharger">Supercharger</option>
                    </select>
                </div>
                @endif

                <!-- Gas Station / Charging Station -->
                <div class="form-group full-width">
                    <label for="gas_station" class="form-label">
                        <span id="station-label">
                            {{ $fuelLog->vehicle->fuel_type === 'Electric' ? 'Charging Station' : 'Gas Station' }}
                        </span>
                    </label>
                    <input type="text" name="gas_station" id="gas_station"
                           placeholder="{{ $fuelLog->vehicle->fuel_type === 'Electric' ? 'e.g., Tesla Supercharger, ChargePoint' : 'e.g., Shell, Chevron, BP' }}"
                           value="{{ $fuelLog->gas_station }}"
                           class="form-input">
                </div>

                <!-- Notes -->
                <div class="form-group full-width">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea name="notes" id="notes" rows="3"
                              placeholder="Any additional notes..."
                              class="form-textarea">{{ $fuelLog->notes }}</textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('fuel.index') }}" class="button button-secondary">
                    Cancel
                </a>
                <button type="submit" class="button button-primary">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const vehicleSelect = document.getElementById('vehicle_id');
    const pageTitle = document.getElementById('page-title');
    const dateLabel = document.getElementById('date-label');
    const quantityLabel = document.getElementById('quantity-label');
    const priceLabel = document.getElementById('price-label');
    const stationLabel = document.getElementById('station-label');
    const fullTankLabel = document.getElementById('full-tank-label');
    const chargeTypeGroup = document.getElementById('charge-type-group');
    const gallonsInput = document.getElementById('gallons');
    const priceInput = document.getElementById('price_per_gallon');
    const stationInput = document.getElementById('gas_station');

    function updateFormLabels(fuelType) {
        if (fuelType === 'Electric') {
            pageTitle.textContent = 'Edit Charge Log';
            pageTitle.classList.add('electric');
            dateLabel.textContent = 'Charge Date';
            quantityLabel.textContent = 'kWh';
            priceLabel.textContent = 'Price / kWh';
            stationLabel.textContent = 'Charging Station';
            fullTankLabel.textContent = 'Full charge';
            gallonsInput.placeholder = 'e.g., 45.50';
            priceInput.placeholder = 'e.g., 0.28';
            stationInput.placeholder = 'e.g., Tesla Supercharger, ChargePoint';
            chargeTypeGroup.style.display = 'block';
            
        } else {
            pageTitle.textContent = 'Edit Fuel Log';
            pageTitle.classList.remove('electric');
            dateLabel.textContent = 'Fill Date';
            quantityLabel.textContent = 'Gallons';
            priceLabel.textContent = 'Price / Gallon';
            stationLabel.textContent = 'Gas Station';
            fullTankLabel.textContent = 'Full tank fill-up';
            gallonsInput.placeholder = 'e.g., 12.50';
            priceInput.placeholder = 'e.g., 3.49';
            stationInput.placeholder = 'e.g., Shell, Chevron, BP';
            chargeTypeGroup.style.display = 'none';
        }
    }

    vehicleSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const fuelType = selectedOption.getAttribute('data-fuel-type');
        
        if (fuelType) {
            updateFormLabels(fuelType);
        }
    });

    // Auto-calculate total cost
    const calculateTotal = () => {
        const quantity = parseFloat(gallonsInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const total = quantity * price;
        
        if (total > 0) {
            document.getElementById('total_cost').value = total.toFixed(2);
        }
    };

    gallonsInput.addEventListener('input', calculateTotal);
    priceInput.addEventListener('input', calculateTotal);
});
</script>
@endsection