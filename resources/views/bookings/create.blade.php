@extends('layouts.app')

@section('title', 'New Booking')

@section('content')
<style>
.booking-form-container {
    max-width: 900px;
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
    margin-bottom: 2rem;
}

.page-header h1 {
    font-family: 'Orbitron', sans-serif;
    font-size: 2rem;
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

.form-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 18px;
    padding: 2rem;
    backdrop-filter: blur(20px);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.form-group label .required {
    color: var(--accent-danger);
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    background: var(--input-bg);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    color: var(--text-primary);
    font-family: 'Chakra Petch', sans-serif;
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--accent-cyan);
    box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
}

.form-group select option {
    background: var(--card-bg);
    color: var(--text-primary);
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.form-group .hint {
    font-size: 0.75rem;
    color: var(--text-tertiary);
    margin-top: 0.5rem;
}

.form-group .hint a {
    color: var(--accent-cyan);
    text-decoration: none;
}

.form-group .hint a:hover {
    text-decoration: underline;
}

.form-error {
    color: var(--accent-danger);
    font-size: 0.75rem;
    margin-top: 0.5rem;
}

.form-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.input-with-icon {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--accent-cyan);
    font-weight: 700;
}

.input-with-icon input {
    padding-left: 2.5rem;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color);
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
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
    .booking-form-container {
        padding: 1.5rem 1rem;
    }

    .form-card {
        padding: 1.5rem;
    }

    .form-grid-2 {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
    }
}
</style>

<div class="booking-form-container">
    <a href="{{ route('bookings.index') }}" class="back-link">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Bookings
    </a>

    <div class="page-header">
        <h1>Book a Service</h1>
        <p>Schedule your vehicle service appointment</p>
    </div>

    <div class="form-card">
        <form action="{{ route('bookings.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Vehicle <span class="required">*</span></label>
                <select name="vehicle_id" required>
                    <option value="">Select a vehicle</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $vehicleId) == $vehicle->id ? 'selected' : '' }}>
                            {{ $vehicle->full_name }} ({{ $vehicle->license_plate ?? 'No Plate' }})
                        </option>
                    @endforeach
                </select>
                @error('vehicle_id')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Service Provider <span class="required">*</span></label>
                <select name="service_provider_id" required>
                    <option value="">Select a provider</option>
                    @foreach($providers as $provider)
                        <option value="{{ $provider->id }}" {{ old('service_provider_id') == $provider->id ? 'selected' : '' }}>
                            {{ $provider->name }} – {{ $provider->city }}
                            @if($provider->rating > 0)
                                (⭐ {{ number_format($provider->rating, 1) }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('service_provider_id')
                    <div class="form-error">{{ $message }}</div>
                @enderror
                <div class="hint">
                    <a href="{{ route('providers.index') }}">Browse all providers →</a>
                </div>
            </div>

            <div class="form-group">
                <label>Service Type <span class="required">*</span></label>
                <select name="service_type" required>
                    <option value="">Select service type</option>
                    @foreach($serviceTypes as $type)
                        <option value="{{ $type }}" {{ old('service_type') == $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
                @error('service_type')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Description <span class="required">*</span></label>
                <textarea name="description" required placeholder="Describe the service needed...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label>Date <span class="required">*</span></label>
                    <input type="date" name="scheduled_date" 
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}" 
                           value="{{ old('scheduled_date') }}" 
                           required />
                    @error('scheduled_date')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Time <span class="required">*</span></label>
                    <input type="time" name="scheduled_time" 
                           value="{{ old('scheduled_time', '09:00') }}" 
                           required />
                    @error('scheduled_time')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label>Estimated Cost</label>
                <div class="input-with-icon">
                    <span class="input-icon">$</span>
                    <input type="number" name="estimated_cost" step="0.01" 
                           value="{{ old('estimated_cost') }}" />
                </div>
                @error('estimated_cost')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Additional Notes</label>
                <textarea name="customer_notes" placeholder="Any special instructions...">{{ old('customer_notes') }}</textarea>
                @error('customer_notes')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('bookings.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">CONFIRM BOOKING</button>
            </div>
        </form>
    </div>
</div>
@endsection