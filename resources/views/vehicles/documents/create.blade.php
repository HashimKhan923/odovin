@extends('layouts.app')

@section('title', 'Upload Document')

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
            <span class="text-gray-900 font-medium">Upload</span>
        </nav>

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Upload Document
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
        <!-- Info Alert -->
        <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-blue-800 mb-1">Smart Document Processing</h4>
                    <p class="text-sm text-blue-700">Upload your document first and we'll automatically extract information like document type, dates, and numbers to fill the form for you.</p>
                </div>
            </div>
        </div>

        <form action="{{ route('vehicles.documents.store', $vehicle) }}" method="POST" enctype="multipart/form-data" id="documentForm">
            @csrf

            <!-- File Upload Section -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8 mb-6">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Step 1: Upload Your Document</h2>
                    <p class="text-sm text-gray-600">Start by uploading your document file</p>
                </div>

                <div>
                    <label for="file" class="block text-sm font-semibold text-gray-700 mb-3">
                        Document File <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <label for="file" class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gradient-to-b from-gray-50 to-white hover:from-blue-50 hover:to-white hover:border-blue-400 transition-all duration-200" id="fileDropZone">
                            <div class="flex flex-col items-center justify-center py-6" id="uploadPrompt">
                                <div class="p-4 bg-blue-100 rounded-full mb-4">
                                    <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                </div>
                                <p class="mb-2 text-base font-semibold text-gray-700">
                                    <span class="text-blue-600 cursor-pointer">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-sm text-gray-500">PDF, JPG, PNG up to 10MB</p>
                            </div>
                            <div class="hidden items-center space-x-4 p-6" id="fileInfo">
                                <div class="flex-shrink-0">
                                    <div class="p-3 bg-green-100 rounded-lg">
                                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-base font-semibold text-gray-900 truncate" id="fileName"></p>
                                    <p class="text-sm text-gray-500" id="fileSize"></p>
                                    <button type="button" onclick="clearFile()" class="mt-2 text-sm text-red-600 hover:text-red-800 font-medium">
                                        Remove file
                                    </button>
                                </div>
                            </div>
                            <input type="file" id="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required class="hidden">
                        </label>
                    </div>
                    @error('file')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Processing Indicator -->
                <div id="processingIndicator" class="hidden mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="animate-spin h-6 w-6 text-blue-600 mr-3" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-blue-800">Processing your document...</p>
                            <p class="text-xs text-blue-600 mt-1">Extracting information using OCR technology</p>
                        </div>
                    </div>
                </div>

                <!-- Auto-filled notification -->
                <div id="autoFillNotification" class="hidden mt-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-green-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-green-800">Information extracted successfully!</p>
                            <p class="text-xs text-green-600 mt-1">Please review the auto-filled fields below and make any necessary adjustments.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Details Section -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Step 2: Document Details</h2>
                    <p class="text-sm text-gray-600">Verify and complete the document information</p>
                </div>

                <div class="space-y-6">
                    <!-- Document Type -->
                    <div>
                        <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">
                            Document Type <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="type" name="type" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition">
                                <option value="">Select document type</option>
                                <optgroup label="Personal Documents">
                                    <option value="driver_license" {{ old('type') == 'driver_license' ? 'selected' : '' }}>Driver License</option>
                                    <option value="passport" {{ old('type') == 'passport' ? 'selected' : '' }}>Passport</option>
                                    <option value="id_card" {{ old('type') == 'id_card' ? 'selected' : '' }}>ID Card</option>
                                </optgroup>
                                <optgroup label="Vehicle Documents">
                                    <option value="registration" {{ old('type') == 'registration' ? 'selected' : '' }}>Registration</option>
                                    <option value="insurance" {{ old('type') == 'insurance' ? 'selected' : '' }}>Insurance</option>
                                    <option value="warranty" {{ old('type') == 'warranty' ? 'selected' : '' }}>Warranty</option>
                                    <option value="inspection" {{ old('type') == 'inspection' ? 'selected' : '' }}>Inspection</option>
                                </optgroup>
                                <optgroup label="Other">
                                    <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
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
                               value="{{ old('title') }}"
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
                                   value="{{ old('issue_date') }}"
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
                                   value="{{ old('expiry_date') }}"
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
                                  placeholder="Add any additional information about this document...">{{ old('notes') }}</textarea>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Upload Document
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file');
    const fileDropZone = document.getElementById('fileDropZone');
    const uploadPrompt = document.getElementById('uploadPrompt');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const processingIndicator = document.getElementById('processingIndicator');
    const autoFillNotification = document.getElementById('autoFillNotification');

    // Handle file selection
    fileInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            handleFileUpload(this.files[0]);
        }
    });

    // Drag and drop functionality
    fileDropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('border-blue-500', 'bg-blue-50', 'scale-105');
    });

    fileDropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('border-blue-500', 'bg-blue-50', 'scale-105');
    });

    fileDropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('border-blue-500', 'bg-blue-50', 'scale-105');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileUpload(files[0]);
        }
    });

    window.clearFile = function() {
        fileInput.value = '';
        uploadPrompt.classList.remove('hidden');
        fileInfo.classList.add('hidden');
        fileInfo.classList.remove('flex');
        processingIndicator.classList.add('hidden');
        autoFillNotification.classList.add('hidden');
    }

    function handleFileUpload(file) {
        // Show file info
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        uploadPrompt.classList.add('hidden');
        fileInfo.classList.remove('hidden');
        fileInfo.classList.add('flex');

        // Process the file
        processDocument(file);
    }

    function processDocument(file) {
        // Show processing indicator
        processingIndicator.classList.remove('hidden');
        autoFillNotification.classList.add('hidden');

        const formData = new FormData();
        formData.append('file', file);
        formData.append('vehicle_id', '{{ $vehicle->id }}');

        fetch('{{ route("vehicles.documents.extract", $vehicle) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            processingIndicator.classList.add('hidden');
            
            if (data.success) {
                // Auto-fill the form fields
                if (data.data.type) {
                    document.getElementById('type').value = data.data.type;
                    highlightField('type');
                }
                if (data.data.title) {
                    document.getElementById('title').value = data.data.title;
                    highlightField('title');
                }
                if (data.data.issue_date) {
                    document.getElementById('issue_date').value = data.data.issue_date;
                    highlightField('issue_date');
                }
                if (data.data.expiry_date) {
                    document.getElementById('expiry_date').value = data.data.expiry_date;
                    highlightField('expiry_date');
                }
                if (data.data.notes) {
                    document.getElementById('notes').value = data.data.notes;
                    highlightField('notes');
                }

                // Show success notification
                autoFillNotification.classList.remove('hidden');
                
                // Scroll to show the filled fields
                setTimeout(() => {
                    document.getElementById('type').scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 500);
            } else {
                console.error('Failed to extract data:', data.message);
                showError('Could not extract data from document. Please fill the form manually.');
            }
        })
        .catch(error => {
            processingIndicator.classList.add('hidden');
            console.error('Error processing document:', error);
            showError('Error processing document. Please fill the form manually.');
        });
    }

    function highlightField(fieldId) {
        const field = document.getElementById(fieldId);
        field.classList.add('ring-2', 'ring-green-500', 'border-green-500');
        setTimeout(() => {
            field.classList.remove('ring-2', 'ring-green-500', 'border-green-500');
        }, 2000);
    }

    function showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'mt-6 bg-red-50 border border-red-200 rounded-lg p-4';
        errorDiv.innerHTML = `
            <div class="flex items-center">
                <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-red-800">${message}</p>
            </div>
        `;
        processingIndicator.parentNode.insertBefore(errorDiv, processingIndicator.nextSibling);
        setTimeout(() => errorDiv.remove(), 5000);
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }
});
</script>
@endpush
@endsection