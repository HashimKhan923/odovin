@extends('admin.layouts.app')
@section('title', 'Settings')
@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Settings</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">System Information</h3>
            <dl class="space-y-2">
                <div><dt class="text-sm text-gray-500">PHP Version</dt><dd class="font-medium">{{ phpversion() }}</dd></div>
                <div><dt class="text-sm text-gray-500">Laravel Version</dt><dd class="font-medium">{{ app()->version() }}</dd></div>
                <div><dt class="text-sm text-gray-500">Environment</dt><dd class="font-medium">{{ app()->environment() }}</dd></div>
            </dl>
            <a href="{{ route('admin.settings.system-info') }}" class="mt-4 inline-block text-blue-600">View Full System Info →</a>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Cache Management</h3>
            <p class="text-sm text-gray-600 mb-4">Clear application caches to improve performance</p>
            <form method="POST" action="{{ route('admin.settings.clear-cache') }}">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">Clear All Caches</button>
            </form>
        </div>
    </div>
</div>
@endsection
