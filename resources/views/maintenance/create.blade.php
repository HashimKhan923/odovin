@extends('layouts.app')

@section('title', 'New Maintenance')

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
        margin-bottom: 0.5rem;
    }

    .page-header p {
        color: var(--text-secondary);
        font-size: 0.875rem;
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

    .form-input.error,
    .form-select.error {
        border-color: var(--accent-danger);
        background: rgba(255, 51, 102, 0.05);
    }

    .form-select option {
        background: var(--card-bg);
        color: var(--text-primary);
    }

    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }

    .error-message {
        font-size: 0.75rem;
        color: var(--accent-danger);
        margin-top: 0.25rem;
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

    /* Input Helper Text */
    .input-helper {
        font-size: 0.75rem;
        color: var(--text-tertiary);
        margin-top: 0.25rem;
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
        <h1>Add Maintenance</h1>
        <p>Schedule or record vehicle maintenance</p>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form action="{{ route('maintenance.store') }}" method="POST">
            @csrf

            <div class="form-grid">
                <!-- Vehicle -->
                <div class="form-group full-width">
                    <label for="vehicle_id" class="form-label">
                        Vehicle <span class="required">*</span>
                    </label>
                    <select id="vehicle_id" name="vehicle_id" required class="form-select @error('vehicle_id') error @enderror">
                        <option value="">Select a vehicle</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->full_name ?? ($vehicle->make.' '.$vehicle->model) }}
                            </option>
                        @endforeach
                    </select>
                    @error('vehicle_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Service Type -->
                <div class="form-group full-width">
                    <label for="service_type" class="form-label">
                        Service Type <span class="required">*</span>
                    </label>
                    <input type="text" id="service_type" name="service_type" required
                           value="{{ old('service_type') }}"
                           placeholder="e.g., Oil Change, Tire Rotation, Brake Inspection"
                           class="form-input @error('service_type') error @enderror">
                    @error('service_type')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Description -->
                <div class="form-group full-width">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" rows="3"
                              placeholder="Add any additional notes or details about this maintenance task..."
                              class="form-textarea">{{ old('description') }}</textarea>
                    <span class="input-helper">Optional: Provide more context about this maintenance</span>
                </div>

                <!-- Due Mileage -->
                <div class="form-group">
                    <label for="due_mileage" class="form-label">Due Mileage</label>
                    <input type="number" id="due_mileage" name="due_mileage"
                           value="{{ old('due_mileage') }}"
                           placeholder="e.g., 50000"
                           class="form-input">
                    <span class="input-helper">Mileage when this service is due</span>
                </div>

                <!-- Due Date -->
                <div class="form-group">
                    <label for="due_date" class="form-label">Due Date</label>
                    <input type="date" id="due_date" name="due_date"
                           value="{{ old('due_date') }}"
                           class="form-input">
                    <span class="input-helper">Date when this service is due</span>
                </div>

                <!-- Priority -->
                <div class="form-group full-width">
                    <label for="priority" class="form-label">
                        Priority <span class="required">*</span>
                    </label>
                    <select id="priority" name="priority" required class="form-select">
                        @foreach(['low','medium','high','critical'] as $level)
                            <option value="{{ $level }}" {{ old('priority') === $level ? 'selected' : '' }}>
                                {{ ucfirst($level) }}
                            </option>
                        @endforeach
                    </select>
                    <span class="input-helper">Set the urgency level for this maintenance</span>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('maintenance.index') }}" class="button button-secondary">
                    Cancel
                </a>
                <button type="submit" class="button button-primary">
                    Save Maintenance
                </button>
            </div>
        </form>
    </div>
</div>
@endsection