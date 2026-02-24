@extends('admin.layouts.app')
@section('title', 'System Information')
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6"><a href="{{ route('admin.settings.index') }}" class="text-blue-600">← Back to Settings</a></div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-6">System Information</h1>
        <dl class="space-y-4">
            <div><dt class="text-sm text-gray-500">PHP Version</dt><dd class="font-medium">{{ $info['php_version'] }}</dd></div>
            <div><dt class="text-sm text-gray-500">Laravel Version</dt><dd class="font-medium">{{ $info['laravel_version'] }}</dd></div>
            <div><dt class="text-sm text-gray-500">Database Driver</dt><dd class="font-medium">{{ $info['database_driver'] }}</dd></div>
            <div><dt class="text-sm text-gray-500">Cache Driver</dt><dd class="font-medium">{{ $info['cache_driver'] }}</dd></div>
            <div><dt class="text-sm text-gray-500">Session Driver</dt><dd class="font-medium">{{ $info['session_driver'] }}</dd></div>
            <div><dt class="text-sm text-gray-500">Queue Driver</dt><dd class="font-medium">{{ $info['queue_driver'] }}</dd></div>
        </dl>
    </div>
</div>
@endsection
