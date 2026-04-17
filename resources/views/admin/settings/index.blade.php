@extends('admin.layouts.app')
@section('title', 'Platform Settings')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Platform Settings</div>
        <div class="page-sub">Control all dynamic values across the platform — no code changes needed</div>
    </div>
    <div style="display:flex;gap:.75rem;">
        <a href="{{ route('admin.settings.system-info') }}" class="btn btn-secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
            System Info
        </a>
        <form method="POST" action="{{ route('admin.settings.clear-cache') }}" style="margin:0;">
            @csrf
            <button type="submit" class="btn btn-secondary" onclick="return confirm('Clear all caches?')">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Clear Cache
            </button>
        </form>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:1.5rem;">
    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif

<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf

    @php
        $groupLabels = [
            'job_board' => ['label' => '🗺 Job Board',          'desc' => 'Radius defaults, job post expiry'],
            'escrow'    => ['label' => '🔒 Escrow & Payments',   'desc' => 'Auto-release window and payment timing'],
            'quotes'    => ['label' => '💬 Quote Requests',      'desc' => 'Quote expiry and duplicate protection'],
            'providers' => ['label' => '🔧 Provider Directory',  'desc' => 'Directory search defaults and pagination'],
            'platform'  => ['label' => '⚙ Platform',            'desc' => 'General platform configuration'],
        ];
    @endphp

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

        @foreach($groupLabels as $groupKey => $meta)
        @if(isset($groups[$groupKey]))
        <div class="card">
            <div class="card-title" style="display:flex;align-items:center;justify-content:space-between;">
                <span>{{ $meta['label'] }}</span>
                <span style="font-size:.72rem;color:var(--text-tertiary);font-weight:400;font-family:'Chakra Petch',sans-serif;">{{ $meta['desc'] }}</span>
            </div>

            @foreach($groups[$groupKey] as $setting)
            <div style="margin-bottom:1.25rem;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.4rem;">
                    <label for="s_{{ $setting->key }}" style="font-size:.78rem;font-weight:700;color:var(--text-secondary);">
                        {{ $setting->label }}
                    </label>
                    @if($setting->unit)
                    <span style="font-size:.7rem;color:var(--text-tertiary);background:rgba(0,212,255,.08);border:1px solid rgba(0,212,255,.15);padding:.1rem .5rem;border-radius:6px;">
                        {{ $setting->unit }}
                    </span>
                    @endif
                </div>

                @if($setting->type === 'boolean')
                <label style="display:flex;align-items:center;gap:.75rem;cursor:pointer;padding:.75rem 1rem;background:rgba(0,212,255,.03);border:1px solid var(--border-color);border-radius:10px;">
                    <input type="hidden" name="{{ $setting->key }}" value="0">
                    <input type="checkbox"
                        id="s_{{ $setting->key }}"
                        name="{{ $setting->key }}"
                        value="1"
                        {{ $setting->value == '1' ? 'checked' : '' }}
                        style="width:18px;height:18px;accent-color:var(--accent-cyan);cursor:pointer;">
                    <span style="font-size:.82rem;color:var(--text-secondary);">
                        {{ $setting->value == '1' ? 'Enabled' : 'Disabled' }}
                    </span>
                </label>

                @elseif($setting->type === 'text')
                <input type="text"
                    id="s_{{ $setting->key }}"
                    name="{{ $setting->key }}"
                    value="{{ $setting->value }}"
                    style="width:100%;padding:.7rem 1rem;background:rgba(0,212,255,.04);border:1px solid var(--border-color);border-radius:10px;color:var(--text-primary);font-family:'Chakra Petch',sans-serif;font-size:.875rem;outline:none;transition:border-color .2s;"
                    onfocus="this.style.borderColor='var(--accent-cyan)'"
                    onblur="this.style.borderColor='var(--border-color)'">

                @else
                {{-- number --}}
                <div style="position:relative;">
                    <input type="number"
                        id="s_{{ $setting->key }}"
                        name="{{ $setting->key }}"
                        value="{{ $setting->value }}"
                        min="0"
                        step="{{ in_array($setting->unit, ['%']) ? '0.01' : '1' }}"
                        style="width:100%;padding:.7rem 1rem;background:rgba(0,212,255,.04);border:1px solid var(--border-color);border-radius:10px;color:var(--text-primary);font-family:'Orbitron',sans-serif;font-size:.9rem;font-weight:700;outline:none;transition:border-color .2s;"
                        onfocus="this.style.borderColor='var(--accent-cyan)'"
                        onblur="this.style.borderColor='var(--border-color)'">
                </div>
                @endif

                @if($setting->description)
                <p style="font-size:.72rem;color:var(--text-tertiary);margin-top:.35rem;line-height:1.5;">
                    {{ $setting->description }}
                </p>
                @endif
            </div>
            @endforeach
        </div>
        @endif
        @endforeach

    </div>

    {{-- System info card (read only) --}}
    <div class="card" style="margin-top:1.5rem;">
        <div class="card-title">🖥 System Information</div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;">
            @foreach([
                ['PHP Version',     $systemSettings['php_version'],     '#00d4ff'],
                ['Laravel',         $systemSettings['laravel_version'],  '#a855f7'],
                ['Environment',     $systemSettings['environment'],      $systemSettings['environment']==='production' ? '#00ffaa' : '#ffaa00'],
                ['Debug Mode',      $systemSettings['debug'] ? 'ON' : 'OFF', $systemSettings['debug'] ? '#ff3366' : '#00ffaa'],
                ['Cache Driver',    $systemSettings['cache_driver'],     'var(--text-secondary)'],
                ['Queue Driver',    $systemSettings['queue_driver'],     'var(--text-secondary)'],
                ['Timezone',        $systemSettings['timezone'],         'var(--text-secondary)'],
                ['App URL',         $systemSettings['app_url'],          'var(--text-secondary)'],
                ['App Name',        $systemSettings['app_name'],         'var(--text-secondary)'],
            ] as [$label, $value, $color])
            <div style="background:rgba(0,212,255,.03);border:1px solid var(--border-color);border-radius:10px;padding:.875rem 1rem;">
                <div style="font-size:.7rem;color:var(--text-tertiary);text-transform:uppercase;letter-spacing:.06em;margin-bottom:.35rem;">{{ $label }}</div>
                <div style="font-size:.82rem;font-weight:700;color:{{ $color }};font-family:'Orbitron',sans-serif;word-break:break-all;">{{ $value }}</div>
            </div>
            @endforeach
        </div>
        <div style="margin-top:1.25rem;text-align:right;">
            <a href="{{ route('admin.settings.system-info') }}" class="btn btn-secondary btn-sm">Full System Info →</a>
        </div>
    </div>

    {{-- Save button (sticky) --}}
    <div style="position:sticky;bottom:1.5rem;z-index:10;display:flex;justify-content:flex-end;margin-top:1.5rem;">
        <button type="submit" class="btn btn-primary" style="padding:.875rem 2.5rem;font-size:.875rem;">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Save All Settings
        </button>
    </div>
</form>

@endsection