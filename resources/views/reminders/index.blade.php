@extends('layouts.app')

@section('title', 'Reminders')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Reminders</h1>
            <p class="mt-1 text-sm text-gray-600">Never miss important dates</p>
        </div>
        <a href="{{ route('reminders.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create Reminder
        </a>
    </div>

    @if($reminders->isEmpty())
        <div class="bg-white rounded-lg shadow-lg p-12 text-center">
            <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No reminders</h3>
            <p class="text-gray-600 mb-6">Create reminders for important dates</p>
            <a href="{{ route('reminders.create') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                Create First Reminder
            </a>
        </div>
    @else
        <div class="grid gap-4">
            @foreach($reminders as $reminder)
            <div class="bg-white rounded-lg shadow p-6 {{ $reminder->is_completed ? 'opacity-60' : '' }}">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-bold text-gray-900">{{ $reminder->title }}</h3>
                            <span class="px-3 py-1 text-xs font-medium rounded-full
                                {{ $reminder->priority === 'high' ? 'bg-red-100 text-red-800' : 
                                   ($reminder->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($reminder->priority) }}
                            </span>
                            @if($reminder->is_completed)
                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    Completed
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mb-2">{{ $reminder->vehicle->full_name }}</p>
                        @if($reminder->description)
                            <p class="text-sm text-gray-600 mb-2">{{ $reminder->description }}</p>
                        @endif
                        <p class="text-sm text-gray-600">
                            📅 Due: {{ $reminder->due_date->format('M d, Y') }}
                        </p>
                    </div>
                    
                    <div class="flex gap-2">
                        @unless($reminder->is_completed)
                            <form action="{{ route('reminders.complete', $reminder) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-800">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                            </form>
                        @endunless
                        <form action="{{ route('reminders.destroy', $reminder) }}" method="POST" 
                              onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $reminders->links() }}
        </div>
    @endif
</div>
@endsection
