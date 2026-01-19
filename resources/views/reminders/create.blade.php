@extends('layouts.app')

@section('title', 'Create Reminder')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create Reminder</h1>
        <p class="mt-1 text-sm text-gray-600">Set a reminder for important dates</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ route('reminders.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <div>
                    <label for="vehicle_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Vehicle <span class="text-red-500">*</span>
                    </label>
                    <select id="vehicle_id" name="vehicle_id" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select a vehicle</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Type <span class="text-red-500">*</span>
                    </label>
                    <select id="type" name="type" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" required
                           value="{{ old('title') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="e.g., Renew Registration">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Additional details...">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Due Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="due_date" name="due_date" required
                               value="{{ old('due_date') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="reminder_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Remind Me On <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="reminder_date" name="reminder_date" required
                               value="{{ old('reminder_date') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Must be before due date</p>
                    </div>
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                        Priority <span class="text-red-500">*</span>
                    </label>
                    <select id="priority" name="priority" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('reminders.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Create Reminder
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
