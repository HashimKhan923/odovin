@extends('layouts.app')

@section('title', 'Upload Document')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('vehicles.documents.index', $vehicle) }}" class="text-blue-600 hover:text-blue-800 flex items-center mb-2">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Documents
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Upload Document</h1>
        <p class="mt-1 text-sm text-gray-600">{{ $vehicle->full_name }}</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ route('vehicles.documents.store', $vehicle) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="space-y-6">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Document Type <span class="text-red-500">*</span>
                    </label>
                    <select id="type" name="type" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select type</option>
                        @foreach($documentTypes as $key => $label)
                            <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" required
                           value="{{ old('title') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="e.g., 2024 Vehicle Registration">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                        File <span class="text-red-500">*</span>
                    </label>
                    <input type="file" id="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500">PDF, JPG, or PNG. Max 10MB</p>
                    @error('file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="issue_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Issue Date
                        </label>
                        <input type="date" id="issue_date" name="issue_date"
                               value="{{ old('issue_date') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Expiry Date
                        </label>
                        <input type="date" id="expiry_date" name="expiry_date"
                               value="{{ old('expiry_date') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">We'll remind you before expiration</p>
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notes
                    </label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Additional notes about this document...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('vehicles.documents.index', $vehicle) }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Upload Document
                </button>
            </div>
        </form>
    </div>
</div>
@endsection