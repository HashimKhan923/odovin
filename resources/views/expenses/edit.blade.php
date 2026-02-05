@extends('layouts.app')

@section('title', 'Edit Expense')

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

    /* Vehicle Info Banner */
    .vehicle-info {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 255, 170, 0.1));
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .vehicle-info-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-secondary);
    }

    .vehicle-info-value {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--text-primary);
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
    .form-textarea,
    .form-file {
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
    .form-textarea:focus,
    .form-file:focus {
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

    .input-with-icon {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-tertiary);
        font-size: 1rem;
        pointer-events: none;
    }

    .input-with-icon .form-input {
        padding-left: 2.5rem;
    }

    .input-helper {
        font-size: 0.75rem;
        color: var(--text-tertiary);
        margin-top: 0.25rem;
    }

    .current-file {
        font-size: 0.75rem;
        color: var(--accent-cyan);
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }

    .current-file::before {
        content: '📎';
        font-size: 0.875rem;
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
        <h1>Edit Expense</h1>
        <p>Update expense information</p>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <!-- Vehicle Info -->
        <div class="vehicle-info">
            <span class="vehicle-info-label">Vehicle:</span>
            <span class="vehicle-info-value">{{ $expense->vehicle->full_name }}</span>
        </div>

        <form action="{{ route('expenses.update', $expense) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <!-- Category -->
                <div class="form-group">
                    <label for="category" class="form-label">
                        Category <span class="required">*</span>
                    </label>
                    <select id="category" name="category" required class="form-select">
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category', $expense->category) == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date -->
                <div class="form-group">
                    <label for="expense_date" class="form-label">
                        Date <span class="required">*</span>
                    </label>
                    <input type="date" id="expense_date" name="expense_date" required
                           value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}"
                           max="{{ date('Y-m-d') }}"
                           class="form-input">
                </div>

                <!-- Description -->
                <div class="form-group full-width">
                    <label for="description" class="form-label">
                        Description <span class="required">*</span>
                    </label>
                    <input type="text" id="description" name="description" required
                           value="{{ old('description', $expense->description) }}"
                           placeholder="e.g., Gas station fill-up, Oil change at AutoShop"
                           class="form-input">
                </div>

                <!-- Amount -->
                <div class="form-group">
                    <label for="amount" class="form-label">
                        Amount <span class="required">*</span>
                    </label>
                    <div class="input-with-icon">
                        <span class="input-icon">$</span>
                        <input type="number" id="amount" name="amount" step="0.01" required
                               value="{{ old('amount', $expense->amount) }}"
                               placeholder="0.00"
                               class="form-input">
                    </div>
                </div>

                <!-- Odometer Reading -->
                <div class="form-group">
                    <label for="odometer_reading" class="form-label">Odometer Reading</label>
                    <input type="number" id="odometer_reading" name="odometer_reading"
                           value="{{ old('odometer_reading', $expense->odometer_reading) }}"
                           placeholder="Current mileage"
                           class="form-input">
                    <span class="input-helper">Optional: Record current mileage</span>
                </div>

                <!-- Receipt File -->
                <div class="form-group full-width">
                    <label for="receipt_file" class="form-label">Receipt (Optional - upload new to replace)</label>
                    <input type="file" id="receipt_file" name="receipt_file" 
                           accept=".pdf,.jpg,.jpeg,.png"
                           class="form-file">
                    @if($expense->receipt_file)
                        <span class="current-file">{{ basename($expense->receipt_file) }}</span>
                    @endif
                    <span class="input-helper">PDF, JPG, or PNG. Max 5MB</span>
                </div>

                <!-- Notes -->
                <div class="form-group full-width">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              placeholder="Any additional notes..."
                              class="form-textarea">{{ old('notes', $expense->notes) }}</textarea>
                    <span class="input-helper">Optional: Add any extra information</span>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('expenses.index') }}" class="button button-secondary">
                    Cancel
                </a>
                <button type="submit" class="button button-primary">
                    Update Expense
                </button>
            </div>
        </form>
    </div>
</div>
@endsection