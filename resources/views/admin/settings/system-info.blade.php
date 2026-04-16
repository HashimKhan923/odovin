@extends('admin.layouts.app')
@section('title', 'System Information')
@section('content')

<a href="{{ route('admin.settings.index') }}" class="back-link">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    Back to Settings
</a>

<div class="page-header">
    <div>
        <div class="page-title">System Information</div>
        <div class="page-sub">Full environment and server configuration</div>
    </div>
</div>

<div class="grid-2">

    <div class="card">
        <div class="card-title">Application</div>
        @php
            $appInfo = [
                ['PHP Version',      $info['php_version'] ?? phpversion()],
                ['Laravel Version',  $info['laravel_version'] ?? app()->version()],
                ['Environment',      $info['environment'] ?? app()->environment()],
                ['Debug Mode',       config('app.debug') ? 'Enabled' : 'Disabled'],
                ['App Name',         config('app.name')],
                ['App URL',          config('app.url')],
                ['Timezone',         config('app.timezone')],
                ['Locale',           config('app.locale')],
            ];
        @endphp
        @foreach($appInfo as [$label, $value])
        <div style="display:flex;justify-content:space-between;align-items:center;padding:.825rem 0;border-bottom:1px solid var(--border-color);">
            <span style="font-size:.8rem;color:var(--text-tertiary);">{{ $label }}</span>
            <span style="font-size:.82rem;font-weight:600;color:var(--text-primary);font-family:monospace;max-width:55%;text-align:right;word-break:break-all;">{{ $value }}</span>
        </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-title">Infrastructure</div>
        @php
            $infraInfo = [
                ['Database Driver',  $info['database_driver'] ?? config('database.default')],
                ['Cache Driver',     $info['cache_driver'] ?? config('cache.default')],
                ['Session Driver',   $info['session_driver'] ?? config('session.driver')],
                ['Queue Driver',     $info['queue_driver'] ?? config('queue.default')],
                ['Mail Driver',      config('mail.default')],
                ['Filesystem',       config('filesystems.default')],
                ['Server',           $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'],
                ['OS',               PHP_OS_FAMILY],
            ];
        @endphp
        @foreach($infraInfo as [$label, $value])
        <div style="display:flex;justify-content:space-between;align-items:center;padding:.825rem 0;border-bottom:1px solid var(--border-color);">
            <span style="font-size:.8rem;color:var(--text-tertiary);">{{ $label }}</span>
            <span style="font-size:.82rem;font-weight:600;color:var(--accent-cyan);font-family:monospace;text-transform:uppercase;">{{ $value }}</span>
        </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-title">PHP Extensions</div>
        @php
            $extensions = ['PDO', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo', 'curl', 'zip', 'gd'];
        @endphp
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;">
            @foreach($extensions as $ext)
            @php $loaded = extension_loaded(strtolower($ext)); @endphp
            <div style="display:flex;align-items:center;gap:.625rem;padding:.625rem .875rem;background:rgba(255,255,255,.02);border:1px solid var(--border-color);border-radius:8px;">
                <div style="width:7px;height:7px;border-radius:50%;background:{{ $loaded ? '#00ffaa' : '#ff3366' }};flex-shrink:0;box-shadow:0 0 6px {{ $loaded ? '#00ffaa' : '#ff3366' }};"></div>
                <span style="font-size:.8rem;font-family:monospace;color:{{ $loaded ? 'var(--text-primary)' : 'var(--text-tertiary)' }};">{{ $ext }}</span>
                <span style="margin-left:auto;font-size:.7rem;font-weight:700;color:{{ $loaded ? '#00ffaa' : '#ff3366' }};">{{ $loaded ? 'OK' : 'MISSING' }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <div class="card-title">Storage & Permissions</div>
        @php
            $paths = [
                ['Storage',         storage_path()],
                ['Storage/Logs',    storage_path('logs')],
                ['Storage/App',     storage_path('app')],
                ['Bootstrap/Cache', base_path('bootstrap/cache')],
                ['Public',          public_path()],
            ];
        @endphp
        @foreach($paths as [$label, $path])
        @php $writable = is_writable($path); @endphp
        <div style="display:flex;align-items:center;justify-content:space-between;padding:.825rem 0;border-bottom:1px solid var(--border-color);">
            <div>
                <div style="font-size:.82rem;font-weight:600;">{{ $label }}</div>
                <div style="font-size:.7rem;color:var(--text-tertiary);font-family:monospace;margin-top:.15rem;">{{ $path }}</div>
            </div>
            <span style="font-size:.72rem;font-weight:700;color:{{ $writable ? '#00ffaa' : '#ff3366' }};background:{{ $writable ? 'rgba(0,255,170,.1)' : 'rgba(255,51,102,.1)' }};border:1px solid {{ $writable ? 'rgba(0,255,170,.25)' : 'rgba(255,51,102,.25)' }};padding:.2rem .6rem;border-radius:6px;white-space:nowrap;">
                {{ $writable ? 'Writable' : 'Not Writable' }}
            </span>
        </div>
        @endforeach
    </div>

</div>

@endsection