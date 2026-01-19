@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
            <p class="mt-1 text-sm text-gray-600">Stay updated on all activities</p>
        </div>
        @if($alerts->where('is_read', false)->count() > 0)
            <form action="{{ route('alerts.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Mark All as Read
                </button>
            </form>
        @endif
    </div>

    @if($alerts->isEmpty())
        <div class="bg-white rounded-lg shadow-lg p-12 text-center">
            <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No notifications</h3>
            <p class="text-gray-600">You're all caught up!</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow divide-y divide-gray-200">
            @foreach($alerts as $alert)
            <div class="p-4 hover:bg-gray-50 transition {{ $alert->is_read ? 'bg-white' : 'bg-blue-50' }}">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-semibold text-gray-900">{{ $alert->title }}</h3>
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $alert->priority === 'critical' ? 'bg-red-100 text-red-800' : 
                                   ($alert->priority === 'warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ ucfirst($alert->priority) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-700 mb-2">{{ $alert->message }}</p>
                        <p class="text-xs text-gray-500">{{ $alert->created_at->diffForHumans() }}</p>
                    </div>
                    
                    <div class="flex gap-2 ml-4">
                        @unless($alert->is_read)
                            <form action="{{ route('alerts.mark-read', $alert) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </form>
                        @endunless
                        <form action="{{ route('alerts.destroy', $alert) }}" method="POST"
                              onsubmit="return confirm('Delete this notification?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $alerts->links() }}
        </div>
    @endif
</div>
@endsection