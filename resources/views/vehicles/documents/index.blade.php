@extends('layouts.app')

@section('title', 'Documents - ' . $vehicle->full_name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-6">
        <a href="{{ route('vehicles.index') }}" class="hover:text-blue-600 transition">Vehicles</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route('vehicles.show', $vehicle) }}" class="hover:text-blue-600 transition">{{ $vehicle->full_name }}</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-900 font-medium">Documents</span>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Vehicle Documents
                </h1>
                <p class="mt-2 text-gray-600">
                    <span class="font-medium">{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}</span>
                    @if($vehicle->license_plate)
                        <span class="mx-2">•</span>
                        <span class="bg-gray-100 px-3 py-1 rounded text-sm font-mono">{{ $vehicle->license_plate }}</span>
                    @endif
                    <span class="mx-2">•</span>
                    <span class="text-sm">{{ $vehicle->documents->count() }} Document{{ $vehicle->documents->count() !== 1 ? 's' : '' }}</span>
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('vehicles.show', $vehicle) }}" 
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Vehicle
                </a>
                <a href="{{ route('vehicles.documents.create', $vehicle) }}" 
                   class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-lg font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Upload Document
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg animate-fade-in">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($vehicle->documents->isEmpty())
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-blue-100 rounded-full mb-6">
                    <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">No documents yet</h3>
                <p class="text-gray-600 mb-8">Keep all your vehicle documents organized in one place. Upload registration, insurance, warranty, and inspection documents.</p>
                <a href="{{ route('vehicles.documents.create', $vehicle) }}" 
                   class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-lg font-medium text-lg">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Upload First Document
                </a>
                
                <!-- Quick tips -->
                <div class="mt-10 pt-10 border-t border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">Document Types You Can Upload:</h4>
                    <div class="grid grid-cols-2 gap-4 text-left">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm text-gray-700">Registration</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm text-gray-700">Insurance</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm text-gray-700">Warranty</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm text-gray-700">Inspection</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Documents Grid -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($vehicle->documents as $document)
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-200 overflow-hidden group">
                <!-- Document Header -->
                <div class="p-6 pb-4">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <!-- Document Type Badge -->
                            @php
                                $typeColors = [
                                    'driver_license' => 'bg-indigo-100 text-indigo-800',
                                    'passport' => 'bg-pink-100 text-pink-800',
                                    'id_card' => 'bg-cyan-100 text-cyan-800',
                                    'registration' => 'bg-purple-100 text-purple-800',
                                    'insurance' => 'bg-blue-100 text-blue-800',
                                    'warranty' => 'bg-green-100 text-green-800',
                                    'inspection' => 'bg-yellow-100 text-yellow-800',
                                    'other' => 'bg-gray-100 text-gray-800',
                                ];
                                $typeLabels = [
                                    'driver_license' => 'Driver License',
                                    'passport' => 'Passport',
                                    'id_card' => 'ID Card',
                                    'registration' => 'Registration',
                                    'insurance' => 'Insurance',
                                    'warranty' => 'Warranty',
                                    'inspection' => 'Inspection',
                                    'other' => 'Other',
                                ];
                                $color = $typeColors[$document->type] ?? $typeColors['other'];
                                $label = $typeLabels[$document->type] ?? ucfirst($document->type);
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full {{ $color }}">
                                {{ $label }}
                            </span>
                        </div>
                        <!-- File Type Icon -->
                        <div class="ml-3">
                            @if(Str::contains($document->file_type, 'pdf'))
                                <svg class="w-10 h-10 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg class="w-10 h-10 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </div>
                    </div>

                    <!-- Title -->
                    <h3 class="font-bold text-gray-900 mb-3 text-lg group-hover:text-blue-600 transition line-clamp-2">
                        {{ $document->title }}
                    </h3>

                    <!-- Document Details -->
                    <div class="space-y-2 text-sm">
                        @if($document->issue_date)
                        <div class="flex items-center text-gray-600">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="font-medium text-gray-700">Issued:</span>
                            <span class="ml-1">{{ $document->issue_date->format('M d, Y') }}</span>
                        </div>
                        @endif

                        @if($document->expiry_date)
                        <div class="flex items-center text-gray-600">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-medium text-gray-700">Expires:</span>
                            <span class="ml-1">{{ $document->expiry_date->format('M d, Y') }}</span>
                            @if($document->isExpiringSoon())
                                <span class="ml-2 px-2 py-0.5 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded">Soon</span>
                            @elseif($document->isExpired())
                                <span class="ml-2 px-2 py-0.5 text-xs font-semibold bg-red-100 text-red-800 rounded">Expired</span>
                            @endif
                        </div>
                        @endif

                        @if($document->notes)
                        <div class="flex items-start text-gray-600 mt-3 pt-3 border-t border-gray-100">
                            <svg class="w-4 h-4 mr-2 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                            <p class="text-xs line-clamp-2">{{ $document->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="px-6 pb-6">
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('vehicles.documents.download', [$vehicle, $document]) }}" 
                           class="flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium shadow-sm">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download
                        </a>
                        <a href="{{ route('vehicles.documents.edit', [$vehicle, $document]) }}" 
                           class="flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm font-medium shadow-sm">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </a>
                    </div>
                    
                    <!-- Delete Button -->
                    <form action="{{ route('vehicles.documents.destroy', [$vehicle, $document]) }}" method="POST" class="mt-2"
                          onsubmit="return confirm('Are you sure you want to delete this document? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm font-medium border border-red-200">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete Document
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection