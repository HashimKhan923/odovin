@extends('layouts.app')

@section('title', 'Add Vehicle')

@section('content')
<div class="vehicle-create-page">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="create-header fade-in-up">
            <div class="header-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <h1 class="page-title">Add New Vehicle</h1>
            <p class="page-subtitle">Enter your VIN to automatically retrieve vehicle specifications</p>
        </div>

        <!-- Form Card -->
        <div class="form-card fade-in-up" style="animation-delay: 0.2s;">
            <form action="{{ route('vehicles.store') }}" method="POST" x-data="vinDecoder()">
                @csrf

                <!-- VIN Decoder Section -->
                <div class="vin-section">
                    <div class="section-header">
                        <div class="header-icon-small">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="section-title">Vehicle Identification Number</h2>
                            <p class="section-description">Enter the 17-character VIN to decode vehicle details</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="vin" class="form-label">
                            VIN Number <span class="required">*</span>
                        </label>
                        <div class="vin-input-wrapper">
                            <input type="text" 
                                   id="vin" 
                                   name="vin" 
                                   x-model="vin"
                                   @input="vin = vin.toUpperCase()"
                                   maxlength="17"
                                   required
                                   class="vin-input @error('vin') error @enderror"
                                   placeholder="Enter 17-character VIN">
                            <div class="vin-counter" :class="vin.length === 17 ? 'complete' : 'incomplete'">
                                <span x-text="vin.length"></span>/17
                            </div>
                        </div>
                        @error('vin')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                        <p class="input-hint">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Find VIN on your registration, insurance card, or driver's side door frame</span>
                        </p>
                    </div>

                    <button type="button" 
                            @click="decodeVin()"
                            :disabled="loading || vin.length !== 17"
                            class="btn-decode">
                        <span x-show="!loading" class="btn-content">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <span>Decode VIN</span>
                        </span>
                        <span x-show="loading" class="btn-content">
                            <div class="spinner"></div>
                            <span>Decoding...</span>
                        </span>
                    </button>

                    <!-- Decoded Result -->
                    <div x-show="decoded" x-cloak class="decoded-result">
                        <div class="result-header">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span>Vehicle Decoded Successfully</span>
                        </div>
                        <div class="result-grid">
                            <div class="result-item">
                                <span class="result-label">Make:</span>
                                <span class="result-value" x-text="vehicleData.make"></span>
                            </div>
                            <div class="result-item">
                                <span class="result-label">Model:</span>
                                <span class="result-value" x-text="vehicleData.model"></span>
                            </div>
                            <div class="result-item">
                                <span class="result-label">Year:</span>
                                <span class="result-value" x-text="vehicleData.year"></span>
                            </div>
                            <div class="result-item">
                                <span class="result-label">Trim:</span>
                                <span class="result-value" x-text="vehicleData.trim || 'N/A'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Details Section -->
                <div class="details-section">
                    <div class="section-header">
                        <div class="header-icon-small">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="section-title">Additional Details</h2>
                            <p class="section-description">Optional information to complete your vehicle profile</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <!-- License Plate -->
                        <div class="form-group">
                            <label for="license_plate" class="form-label">License Plate</label>
                            <input type="text" 
                                   id="license_plate" 
                                   name="license_plate" 
                                   value="{{ old('license_plate') }}"
                                   class="form-input @error('license_plate') error @enderror"
                                   placeholder="ABC-1234">
                            @error('license_plate')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Color -->
                        <div class="form-group">
                            <label for="color" class="form-label">Color</label>
                            <input type="text" 
                                   id="color" 
                                   name="color" 
                                   value="{{ old('color') }}"
                                   class="form-input @error('color') error @enderror"
                                   placeholder="Black, White, Red...">
                            @error('color')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Purchase Date -->
                        <div class="form-group">
                            <label for="purchase_date" class="form-label">Purchase Date</label>
                            <input type="date" 
                                   id="purchase_date" 
                                   name="purchase_date" 
                                   value="{{ old('purchase_date') }}"
                                   class="form-input @error('purchase_date') error @enderror">
                            @error('purchase_date')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Purchase Price -->
                        <div class="form-group">
                            <label for="purchase_price" class="form-label">Purchase Price</label>
                            <div class="input-with-icon">
                                <span class="input-icon">$</span>
                                <input type="number" 
                                       id="purchase_price" 
                                       name="purchase_price" 
                                       step="0.01"
                                       value="{{ old('purchase_price') }}"
                                       class="form-input with-icon @error('purchase_price') error @enderror"
                                       placeholder="0.00">
                            </div>
                            @error('purchase_price')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Current Mileage -->
                        <div class="form-group">
                            <label for="current_mileage" class="form-label">Current Mileage</label>
                            <input type="number" 
                                   id="current_mileage" 
                                   name="current_mileage" 
                                   value="{{ old('current_mileage', 0) }}"
                                   class="form-input @error('current_mileage') error @enderror"
                                   placeholder="0">
                            @error('current_mileage')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Primary Vehicle Checkbox -->
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" 
                                   name="is_primary" 
                                   value="1"
                                   {{ old('is_primary') ? 'checked' : '' }}
                                   class="checkbox-input">
                            <span class="checkbox-text">
                                <svg class="checkbox-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                                Set as primary vehicle
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('vehicles.index') }}" class="btn-cancel">
                        <span>Cancel</span>
                    </a>
                    <button type="submit" class="btn-submit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Add Vehicle</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function vinDecoder() {
    return {
        vin: '',
        loading: false,
        decoded: false,
        vehicleData: {},
        
        async decodeVin() {
            if (this.vin.length !== 17) return;
            
            this.loading = true;
            
            try {
                const response = await fetch(`/vehicles/decode-vin/${this.vin}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.vehicleData = data.data;
                    this.decoded = true;
                } else {
                    alert('Failed to decode VIN: ' + data.message);
                }
            } catch (error) {
                alert('Error decoding VIN. Please try again.');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300;400;500;600;700&family=Orbitron:wght@400;500;600;700;800&display=swap');

:root {
    --bg-primary: #0a0e1a;
    --bg-card: rgba(26, 32, 48, 0.8);
    --accent-primary: #00d4ff;
    --accent-secondary: #00ffaa;
    --accent-danger: #ff3366;
    --text-primary: #ffffff;
    --text-secondary: rgba(255, 255, 255, 0.7);
    --text-tertiary: rgba(255, 255, 255, 0.5);
    --border-color: rgba(0, 212, 255, 0.1);
}

.vehicle-create-page {
    min-height: 100vh;
    padding: 2rem 0;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.fade-in-up {
    animation: fadeInUp 0.6s ease-out forwards;
    opacity: 0;
}

/* Header */
.create-header {
    text-align: center;
    margin-bottom: 3rem;
}

.header-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.2), rgba(0, 255, 170, 0.2));
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(0, 212, 255, 0.3);
}

.header-icon svg {
    width: 40px;
    height: 40px;
    color: var(--accent-primary);
}

.page-title {
    font-family: 'Orbitron', sans-serif;
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 0.75rem;
}

.page-subtitle {
    font-size: 1rem;
    color: var(--text-secondary);
}

/* Form Card */
.form-card {
    background: var(--bg-card);
    backdrop-filter: blur(20px);
    border: 1px solid var(--border-color);
    border-radius: 24px;
    padding: 3rem;
}

.vin-section, .details-section {
    padding-bottom: 2.5rem;
    margin-bottom: 2.5rem;
    border-bottom: 1px solid var(--border-color);
}

.details-section {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.section-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 2rem;
}

.header-icon-small {
    width: 48px;
    height: 48px;
    background: rgba(0, 212, 255, 0.1);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.header-icon-small svg {
    width: 24px;
    height: 24px;
    color: var(--accent-primary);
}

.section-title {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.section-description {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

/* Form Groups */
.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.required {
    color: var(--accent-danger);
}

.vin-input-wrapper {
    position: relative;
}

.vin-input, .form-input {
    width: 100%;
    padding: 1rem 1.5rem;
    background: rgba(0, 0, 0, 0.3);
    border: 2px solid var(--border-color);
    border-radius: 12px;
    color: var(--text-primary);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.vin-input {
    padding-right: 5rem;
    font-family: 'Orbitron', monospace;
    font-weight: 600;
    letter-spacing: 0.1em;
    font-size: 1.125rem;
}

.vin-input:focus, .form-input:focus {
    outline: none;
    border-color: var(--accent-primary);
    background: rgba(0, 0, 0, 0.4);
    box-shadow: 0 0 20px rgba(0, 212, 255, 0.2);
}

.vin-input.error, .form-input.error {
    border-color: var(--accent-danger);
}

.vin-counter {
    position: absolute;
    right: 1.5rem;
    top: 50%;
    transform: translateY(-50%);
    padding: 0.5rem 1rem;
    background: rgba(0, 212, 255, 0.1);
    border-radius: 8px;
    font-family: 'Orbitron', monospace;
    font-size: 0.875rem;
    font-weight: 700;
}

.vin-counter.complete {
    background: rgba(0, 255, 136, 0.2);
    color: var(--accent-secondary);
}

.vin-counter.incomplete {
    color: var(--text-tertiary);
}

.input-hint {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.75rem;
    font-size: 0.75rem;
    color: var(--text-tertiary);
}

.input-hint svg {
    width: 14px;
    height: 14px;
    flex-shrink: 0;
}

.error-message {
    margin-top: 0.75rem;
    font-size: 0.875rem;
    color: var(--accent-danger);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Decode Button */
.btn-decode {
    width: 100%;
    padding: 1.25rem;
    margin-top: 1.5rem;
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    color: var(--bg-primary);
    border: none;
    border-radius: 12px;
    font-size: 1.125rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-decode:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.btn-decode:not(:disabled):hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(0, 212, 255, 0.5);
}

.btn-content {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
}

.btn-content svg {
    width: 20px;
    height: 20px;
}

.spinner {
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Decoded Result */
.decoded-result {
    margin-top: 1.5rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, rgba(0, 255, 136, 0.1), rgba(0, 212, 255, 0.05));
    border: 1px solid rgba(0, 255, 136, 0.3);
    border-radius: 12px;
    animation: slideIn 0.4s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.result-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
    color: var(--accent-secondary);
    font-weight: 600;
}

.result-header svg {
    width: 24px;
    height: 24px;
}

.result-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.result-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.result-label {
    font-size: 0.75rem;
    color: var(--text-tertiary);
}

.result-value {
    font-weight: 600;
    color: var(--text-primary);
}

/* Form Grid */
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .result-grid {
        grid-template-columns: 1fr;
    }
}

.input-with-icon {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 1.5rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-tertiary);
    font-weight: 600;
}

.form-input.with-icon {
    padding-left: 3rem;
}

/* Checkbox */
.checkbox-group {
    margin-top: 2rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
}

.checkbox-input {
    appearance: none;
    width: 24px;
    height: 24px;
    background: rgba(0, 0, 0, 0.3);
    border: 2px solid var(--border-color);
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    transition: all 0.3s ease;
}

.checkbox-input:checked {
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    border-color: transparent;
}

.checkbox-input:checked + .checkbox-text .checkbox-icon {
    opacity: 1;
}

.checkbox-text {
    color: var(--text-secondary);
    font-size: 0.95rem;
    font-weight: 500;
    position: relative;
}

.checkbox-icon {
    position: absolute;
    left: -32px;
    width: 24px;
    height: 24px;
    color: var(--bg-primary);
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
}

/* Form Actions */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 3rem;
    padding-top: 2.5rem;
    border-top: 1px solid var(--border-color);
}

.btn-cancel, .btn-submit {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 2rem;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-cancel {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--border-color);
    color: var(--text-primary);
}

.btn-cancel:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(0, 212, 255, 0.3);
}

.btn-submit {
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    color: var(--bg-primary);
}

.btn-submit:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0, 212, 255, 0.5);
}

.btn-submit svg {
    width: 20px;
    height: 20px;
}
</style>
@endsection