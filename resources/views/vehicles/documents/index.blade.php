@extends('layouts.app')

@section('title', 'Documents - ' . $vehicle->full_name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('vehicles.show', $vehicle) }}" class="text-blue-600 hover:text-blue-800 flex items-center mb-2">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Vehicle
        </a>
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Documents</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $vehicle->full_name }}</p>
            </div>
            <a href="{{ route('vehicles.documents.create', $vehicle) }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Upload Document
            </a>
        </div>
    </div>

    @if($vehicle->documents->isEmpty())
        <div class="bg-white rounded-lg shadow-lg p-12 text-center">
            <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No documents yet</h3>
            <p class="text-gray-600 mb-6">Upload your vehicle documents for easy access</p>
            <a href="{{ route('vehicles.documents.create', $vehicle) }}" 
               class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                Upload First Document
            </a>
        </div>
    @else
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($vehicle->documents as $document)
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900 mb-1">{{ $document->title }}</h3>
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                            {{ ucfirst($document->type) }}
                        </span>
                    </div>
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>

                <div class="space-y-2 mb-4">
                    @if($document->issue_date)
                    <p class="text-sm text-gray-600">
                        <strong>Issued:</strong> {{ $document->issue_date->format('M d, Y') }}
                    </p>
                    @endif
                    @if($document->expiry_date)
                    <p class="text-sm text-gray-600">
                        <strong>Expires:</strong> {{ $document->expiry_date->format('M d, Y') }}
                        @if($document->isExpiringSoon())
                            <span class="text-yellow-600 font-medium">(Soon)</span>
                        @elseif($document->isExpired())
                            <span class="text-red-600 font-medium">(Expired)</span>
                        @endif
                    </p>
                    @endif
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('vehicles.documents.download', [$vehicle, $document]) }}" 
                       class="flex-1 text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                        Download
                    </a>
                    <a href="{{ route('vehicles.documents.edit', [$vehicle, $document]) }}" 
                       class="flex-1 text-center bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-sm">
                        Edit
                    </a>
                    <form action="{{ route('vehicles.documents.destroy', [$vehicle, $document]) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this document?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 p-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection