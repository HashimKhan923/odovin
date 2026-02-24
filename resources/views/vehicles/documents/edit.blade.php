@extends('layouts.app')

@section('title', 'Edit Document')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-4">
            <a href="{{ route('vehicles.index') }}" class="hover:text-blue-600 transition">Vehicles</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="{{ route('vehicles.show', $vehicle) }}" class="hover:text-blue-600 transition">{{ $vehicle->full_name }}</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="{{ route('vehicles.documents.index', $vehicle) }}" class="hover:text-blue-600 transition">Documents</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-900 font-medium">Edit</span>
        </nav>

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Document
                </h1>
                <p class="mt-2 text-gray-600">
                    <span class="font-medium">{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}</span>
                    @if($vehicle->license_plate)
                        <span class="mx-2">•</span>
                        <span class="bg-gray-100 px-3 py-1 rounded text-sm font-mono">{{ $vehicle->license_plate }}</span>
                    @endif
                </p>
            </div>
            <a href="{{ route('vehicles.documents.index', $vehicle) }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Documents
            </a>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('vehicles.documents.update', [$vehicle, $document]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Current Document Info -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-lg border border-blue-200 p-6 mb-6">
                <h3 class="text-sm font-semibold text-blue-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Current Document
                </h3>
                <div class="grid md:grid-cols-2 gap-4 text-sm">
                    <div class="bg-white/70 rounded-lg p-3">
                        <span class="text-gray-600">File:</span>
                        <span class="font-medium text-gray-900 ml-2">{{ basename($document->file_path) }}</span>
                    </div>
                    <div class="bg-white/70 rounded-lg p-3">
                        <span class="text-gray-600">Type:</span>
                        <span class="font-medium text-gray-900 ml-2">{{ ucfirst($document->type) }}</span>
                    </div>
                </div>
            </div>

            <!-- Document Details Section -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8">
                <div class="space-y-6">
                    <!-- Document Type -->
                    <div>
                        <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">
                            Document Type <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="type" name="type" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition">
                                <optgroup label="Personal Documents">
                                    <option value="driver_license" {{ old('type', $document->type) == 'driver_license' ? 'selected' : '' }}>Driver License</option>
                                    <option value="passport" {{ old('type', $document->type) == 'passport' ? 'selected' : '' }}>Passport</option>
                                    <option value="id_card" {{ old('type', $document->type) == 'id_card' ? 'selected' : '' }}>ID Card</option>
                                </optgroup>
                                <optgroup label="Vehicle Documents">
                                    <option value="registration" {{ old('type', $document->type) == 'registration' ? 'selected' : '' }}>Registration</option>
                                    <option value="insurance" {{ old('type', $document->type) == 'insurance' ? 'selected' : '' }}>Insurance</option>
                                    <option value="warranty" {{ old('type', $document->type) == 'warranty' ? 'selected' : '' }}>Warranty</option>
                                    <option value="inspection" {{ old('type', $document->type) == 'inspection' ? 'selected' : '' }}>Inspection</option>
                                </optgroup>
                                <optgroup label="Other">
                                    <option value="other" {{ old('type', $document->type) == 'other' ? 'selected' : '' }}>Other</option>
                                </optgroup>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        @error('type')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                            Document Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="title" name="title" required
                               value="{{ old('title', $document->title) }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition"
                               placeholder="e.g., 2024 Vehicle Registration Certificate">
                        @error('title')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Replace File -->
                    <div>
                        <label for="file" class="block text-sm font-semibold text-gray-700 mb-2">
                            Replace File (Optional)
                        </label>
                        <div class="relative">
                            <input type="file" id="file" name="file" accept=".pdf,.jpg,.jpeg,.png"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                        </div>
                        <p class="mt-2 text-xs text-gray-500 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            Leave empty to keep the current file. PDF, JPG, PNG up to 10MB
                        </p>
                        @error('file')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Dates Grid -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="issue_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Issue Date
                            </label>
                            <input type="date" id="issue_date" name="issue_date"
                                   value="{{ old('issue_date', $document->issue_date?->format('Y-m-d')) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition">
                        </div>

                        <div>
                            <label for="expiry_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Expiry Date
                            </label>
                            <input type="date" id="expiry_date" name="expiry_date"
                                   value="{{ old('expiry_date', $document->expiry_date?->format('Y-m-d')) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition">
                            <p class="mt-2 text-xs text-gray-500 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                We'll send you a reminder 30 days before expiration
                            </p>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                            Additional Notes
                        </label>
                        <textarea id="notes" name="notes" rows="4"
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition"
                                  placeholder="Add any additional information about this document...">{{ old('notes', $document->notes) }}</textarea>
                        <p class="mt-2 text-xs text-gray-500">Document numbers, VIN, policy details, etc.</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-end">
                <a href="{{ route('vehicles.documents.index', $vehicle) }}" 
                   class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition shadow-sm font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancel
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-lg font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Document
                </button>
            </div>
        </form>
    </div>
</div>
@endsection