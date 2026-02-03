@extends('layouts.app')

@section('title', 'Edit Vehicle')

@section('content')
<div class="vehicle-edit-page">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="edit-header fade-in-up">
            <div class="header-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <h1 class="page-title">Edit Vehicle</h1>
            <p class="page-subtitle">Update your vehicle information</p>
        </div>

        <!-- Form Card -->
        <div class="form-card fade-in-up" style="animation-delay: 0.2s;">
            <form action="{{ route('vehicles.update', $vehicle) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- VIN Section (Read-only) -->
                <div class="vin-section">
                    <div class="section-header">
                        <div class="header-icon-small locked">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="section-title">Vehicle Identification Number</h2>
                            <p class="section-description">VIN cannot be modified once added</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">VIN Number</label>
                        <input type="text" 
                               value="{{ $vehicle->vin }}" 
                               disabled
                               class="form-input disabled">
                        <p class="input-hint locked">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>This field is locked for data integrity</span>
                        </p>
                    </div>
                </div>

                <!-- Vehicle Info Display -->
                <div class="vehicle-info-display">
                    <div class="info-badge">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Verified Vehicle</span>
                    </div>
                    <h3 class="vehicle-name">{{ $vehicle->full_name }}</h3>
                    <div class="vehicle-specs">
                        @if($vehicle->trim)
                        <div class="spec-chip">
                            <span class="spec-label">Trim:</span>
                            <span class="spec-value">{{ $vehicle->trim }}</span>
                        </div>
                        @endif
                        @if($vehicle->engine)
                        <div class="spec-chip">
                            <span class="spec-label">Engine:</span>
                            <span class="spec-value">{{ $vehicle->engine }}</span>
                        </div>
                        @endif
                        @if($vehicle->transmission)
                        <div class="spec-chip">
                            <span class="spec-label">Transmission:</span>
                            <span class="spec-value">{{ $vehicle->transmission }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Editable Fields Section -->
                <div class="editable-section">
                    <div class="section-header">
                        <div class="header-icon-small">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="section-title">Editable Information</h2>
                            <p class="section-description">Update vehicle details as needed</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <!-- License Plate -->
                        <div class="form-group">
                            <label for="license_plate" class="form-label">License Plate</label>
                            <input type="text" 
                                   id="license_plate" 
                                   name="license_plate" 
                                   value="{{ old('license_plate', $vehicle->license_plate) }}"
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
                                   value="{{ old('color', $vehicle->color) }}"
                                   class="form-input @error('color') error @enderror"
                                   placeholder="Black, White, Red...">
                            @error('color')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Current Mileage -->
                        <div class="form-group">
                            <label for="current_mileage" class="form-label">Current Mileage</label>
                            <input type="number" 
                                   id="current_mileage" 
                                   name="current_mileage" 
                                   min="{{ $vehicle->current_mileage }}"
                                   value="{{ old('current_mileage', $vehicle->current_mileage) }}"
                                   class="form-input @error('current_mileage') error @enderror"
                                   placeholder="0">
                            <p class="input-hint">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Cannot be less than {{ number_format($vehicle->current_mileage) }} mi</span>
                            </p>
                            @error('current_mileage')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label for="status" class="form-label">Vehicle Status</label>
                            <div class="select-wrapper">
                                <select id="status" 
                                        name="status" 
                                        class="form-select @error('status') error @enderror">
                                    <option value="active" {{ $vehicle->status === 'active' ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="inactive" {{ $vehicle->status === 'inactive' ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                    <option value="sold" {{ $vehicle->status === 'sold' ? 'selected' : '' }}>
                                        Sold
                                    </option>
                                </select>
                                <svg class="select-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                            @error('status')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('vehicles.show', $vehicle) }}" class="btn-cancel">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span>Cancel</span>
                    </a>
                    <button type="submit" class="btn-submit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Update Vehicle</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300;400;500;600;700&family=Orbitron:wght@400;500;600;700;800&display=swap');

:root {
    --bg-primary: #0a0e1a;
    --bg-card: rgba(26, 32, 48, 0.8);
    --accent-primary: #00d4ff;
    --accent-secondary: #00ffaa;
    --accent-danger: #ff3366;
    --accent-warning: #ffaa00;
    --text-primary: #ffffff;
    --text-secondary: rgba(255, 255, 255, 0.7);
    --text-tertiary: rgba(255, 255, 255, 0.5);
    --border-color: rgba(0, 212, 255, 0.1);
}

.vehicle-edit-page {
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
.edit-header {
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

.vin-section, .editable-section {
    padding-bottom: 2.5rem;
    margin-bottom: 2.5rem;
    border-bottom: 1px solid var(--border-color);
}

.editable-section {
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

.header-icon-small.locked {
    background: rgba(255, 170, 0, 0.1);
}

.header-icon-small svg {
    width: 24px;
    height: 24px;
    color: var(--accent-primary);
}

.header-icon-small.locked svg {
    color: var(--accent-warning);
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

.form-input, .form-select {
    width: 100%;
    padding: 1rem 1.5rem;
    background: rgba(0, 0, 0, 0.3);
    border: 2px solid var(--border-color);
    border-radius: 12px;
    color: var(--text-primary);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-input:focus, .form-select:focus {
    outline: none;
    border-color: var(--accent-primary);
    background: rgba(0, 0, 0, 0.4);
    box-shadow: 0 0 20px rgba(0, 212, 255, 0.2);
}

.form-input.disabled {
    background: rgba(0, 0, 0, 0.2);
    border-color: rgba(255, 255, 255, 0.05);
    color: var(--text-tertiary);
    cursor: not-allowed;
}

.form-input.error, .form-select.error {
    border-color: var(--accent-danger);
}

.input-hint {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.75rem;
    font-size: 0.75rem;
    color: var(--text-tertiary);
}

.input-hint.locked {
    color: var(--accent-warning);
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
}

/* Vehicle Info Display */
.vehicle-info-display {
    padding: 2rem;
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.05), rgba(0, 255, 170, 0.05));
    border: 1px solid rgba(0, 212, 255, 0.2);
    border-radius: 16px;
    margin-bottom: 2.5rem;
}

.info-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(0, 255, 136, 0.1);
    border: 1px solid rgba(0, 255, 136, 0.3);
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--accent-secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 1rem;
}

.info-badge svg {
    width: 16px;
    height: 16px;
}

.vehicle-name {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.vehicle-specs {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.spec-chip {
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 0.875rem;
}

.spec-label {
    color: var(--text-tertiary);
    margin-right: 0.5rem;
}

.spec-value {
    color: var(--text-primary);
    font-weight: 600;
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
}

/* Select Dropdown */
.select-wrapper {
    position: relative;
}

.form-select {
    appearance: none;
    cursor: pointer;
    padding-right: 3rem;
}

.select-icon {
    position: absolute;
    right: 1.5rem;
    top: 50%;
    transform: translateY(-50%);
    width: 20px;
    height: 20px;
    color: var(--text-tertiary);
    pointer-events: none;
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

.btn-cancel svg, .btn-submit svg {
    width: 20px;
    height: 20px;
}
</style>
@endsection