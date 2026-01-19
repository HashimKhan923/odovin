@extends('layouts.app')

@section('title', 'Edit Expense')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Expense</h1>
        <p class="mt-1 text-sm text-gray-600">Update expense information</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ route('expenses.update', $expense) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-600"><strong>Vehicle:</strong> {{ $expense->vehicle->full_name }}</p>
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select id="category" name="category" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category', $expense->category) == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="expense_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="expense_date" name="expense_date" required
                           value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}"
                           max="{{ date('Y-m-d') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="description" name="description" required
                           value="{{ old('description', $expense->description) }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Amount <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                        <input type="number" id="amount" name="amount" step="0.01" required
                               value="{{ old('amount', $expense->amount) }}"
                               class="w-full pl-7 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label for="odometer_reading" class="block text-sm font-medium text-gray-700 mb-2">
                        Odometer Reading
                    </label>
                    <input type="number" id="odometer_reading" name="odometer_reading"
                           value="{{ old('odometer_reading', $expense->odometer_reading) }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label for="receipt_file" class="block text-sm font-medium text-gray-700 mb-2">
                        Receipt (Optional - upload new to replace)
                    </label>
                    <input type="file" id="receipt_file" name="receipt_file" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @if($expense->receipt_file)
                        <p class="mt-1 text-xs text-gray-500">Current file: {{ basename($expense->receipt_file) }}</p>
                    @endif
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notes
                    </label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $expense->notes) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('expenses.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Update Expense
                </button>
            </div>
        </form>
    </div>
</div>
@endsection