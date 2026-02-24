<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Odovin') }} Provider — @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300;400;500;600;700&family=Orbitron:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg-primary: #0a0e1a; --bg-secondary: #121827;
            --card-bg: rgba(26,32,48,.85); --border-color: rgba(0,212,255,.12);
            --accent-cyan: #00d4ff; --accent-green: #00ffaa;
            --accent-warning: #ffaa00; --accent-danger: #ff3366; --accent-purple: #a855f7;
            --text-primary: #fff; --text-secondary: rgba(255,255,255,.68);
            --text-tertiary: rgba(255,255,255,.42);
            --sidebar-w: 260px; --input-bg: rgba(0,212,255,.05);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Chakra Petch', sans-serif;
            background: var(--bg-primary); color: var(--text-primary);
            display: flex; min-height: 100vh;
        }

        /* Sidebar */
        .sp-sidebar {
            width: var(--sidebar-w); background: rgba(10,14,26,.98);
            border-right: 1px solid var(--border-color);
            display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0; bottom: 0; z-index: 100;
            transition: transform .3s;
        }
        .sp-logo { padding: 1.5rem; border-bottom: 1px solid var(--border-color); }
        .sp-logo-text {
            font-family: 'Orbitron', sans-serif; font-size: 1.1rem; font-weight: 800;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .sp-logo-sub { font-size: .7rem; color: var(--text-tertiary); margin-top: 2px; }
        .sp-nav { flex: 1; overflow-y: auto; padding: 1rem 0; }
        .sp-nav-section { padding: .5rem 1rem; margin-bottom: .25rem; }
        .sp-nav-label {
            font-size: .65rem; text-transform: uppercase; letter-spacing: .1em;
            color: var(--text-tertiary); font-weight: 700; padding: .5rem .75rem;
        }
        .sp-nav-link {
            display: flex; align-items: center; gap: .75rem;
            padding: .75rem 1rem; border-radius: 10px;
            color: var(--text-secondary); text-decoration: none;
            font-size: .875rem; font-weight: 500; transition: all .25s;
        }
        .sp-nav-link:hover { background: rgba(0,212,255,.08); color: var(--text-primary); }
        .sp-nav-link.active {
            background: linear-gradient(135deg, rgba(0,212,255,.18), rgba(0,255,170,.1));
            color: var(--accent-cyan); border-left: 3px solid var(--accent-cyan);
        }
        .sp-nav-link svg { width: 18px; height: 18px; flex-shrink: 0; }
        .sp-badge {
            margin-left: auto; background: var(--accent-danger);
            color: #fff; border-radius: 20px; font-size: .65rem;
            padding: 2px 7px; font-weight: 700;
        }
        .sp-footer { padding: 1rem; border-top: 1px solid var(--border-color); }
        .sp-provider-info { display: flex; align-items: center; gap: .75rem; margin-bottom: .75rem; }
        .sp-avatar {
            width: 40px; height: 40px; border-radius: 10px;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
            display: flex; align-items: center; justify-content: center;
            font-family: 'Orbitron', sans-serif; font-weight: 800; font-size: 1rem; color: #000;
        }
        .sp-provider-name { font-size: .8rem; font-weight: 600; color: var(--text-primary); }
        .sp-provider-role { font-size: .7rem; color: var(--text-tertiary); }
        .sp-logout {
            display: flex; align-items: center; gap: .5rem;
            padding: .6rem .75rem; border-radius: 8px; width: 100%;
            background: rgba(255,51,102,.1); border: 1px solid rgba(255,51,102,.25);
            color: var(--accent-danger); font-size: .8rem; font-weight: 600;
            cursor: pointer; transition: all .25s; font-family: 'Chakra Petch', sans-serif;
        }
        .sp-logout:hover { background: rgba(255,51,102,.2); }

        /* Main */
        .sp-main { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .sp-topbar {
            background: rgba(10,14,26,.9); backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem; display: flex; align-items: center;
            justify-content: space-between; position: sticky; top: 0; z-index: 50;
        }
        .sp-topbar-title { font-family: 'Orbitron', sans-serif; font-size: 1.1rem; font-weight: 700; }
        .sp-topbar-right { display: flex; align-items: center; gap: 1rem; }
        .sp-status-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--accent-green); box-shadow: 0 0 8px var(--accent-green);
        }
        .sp-content { padding: 2rem; flex: 1; max-width: 1400px; width: 100%; margin: 0 auto; }

        /* Flash messages */
        .flash-success, .flash-error {
            padding: .875rem 1.25rem; border-radius: 12px;
            font-size: .875rem; font-weight: 600; margin-bottom: 1.5rem;
            display: flex; align-items: center; gap: .75rem;
        }
        .flash-success { background: rgba(0,255,170,.12); border: 1px solid rgba(0,255,170,.3); color: var(--accent-green); }
        .flash-error   { background: rgba(255,51,102,.12); border: 1px solid rgba(255,51,102,.3); color: var(--accent-danger); }

        @media(max-width:768px) {
            .sp-sidebar { transform: translateX(-100%); }
            .sp-sidebar.open { transform: translateX(0); }
            .sp-main { margin-left: 0; }
            .sp-content { padding: 1.25rem; }
        }
    </style>
    @stack('styles')
</head>
<body>
<aside class="sp-sidebar" id="spSidebar">
    <div class="sp-logo">
        <div class="sp-logo-text">ODOVIN</div>
        <div class="sp-logo-sub">Provider Portal</div>
    </div>

    <nav class="sp-nav">
        <div class="sp-nav-section">
            <div class="sp-nav-label">Main</div>
            <a href="{{ route('provider.dashboard') }}" class="sp-nav-link {{ request()->routeIs('provider.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h7v7H3zM14 3h7v4h-7zM14 10h7v11h-7zM3 17h7v4H3z"/></svg>
                Dashboard
            </a>
        </div>

        <div class="sp-nav-section">
            <div class="sp-nav-label">Bookings</div>
            <a href="{{ route('provider.bookings.index') }}" class="sp-nav-link {{ request()->routeIs('provider.bookings.index') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                All Bookings
            @php
                $pending = Auth::user()->serviceProvider
                    ? \App\Models\ServiceBooking::where('service_provider_id', Auth::user()->serviceProvider->id)
                        ->where('status','pending')->count()
                    : 0;
            @endphp  
              @if($pending > 0)<span class="sp-badge">{{ $pending }}</span>@endif
            </a>
            <a href="{{ route('provider.bookings.calendar') }}" class="sp-nav-link {{ request()->routeIs('provider.bookings.calendar') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Calendar View
            </a>
        </div>

        <div class="sp-nav-section">
            <div class="sp-nav-label">Management</div>
            <a href="{{ route('provider.profile') }}" class="sp-nav-link {{ request()->routeIs('provider.profile') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Business Profile
            </a>
            <a href="{{ route('provider.hours') }}" class="sp-nav-link {{ request()->routeIs('provider.hours') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Working Hours
            </a>
            <a href="{{ route('provider.analytics') }}" class="sp-nav-link {{ request()->routeIs('provider.analytics') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Analytics
            </a>
        </div>
    </nav>

    <div class="sp-footer">
        <div class="sp-provider-info">
            <div class="sp-avatar">{{ substr(Auth::user()->name ?? 'P', 0, 1) }}</div>
            <div>
                <div class="sp-provider-name">{{ Str::limit(Auth::user()->name ?? 'Provider', 20) }}</div>
                <div class="sp-provider-role">Service Provider</div>
            </div>
        </div>
        <form action="{{ route('provider.logout') }}" method="POST">
            @csrf
            <button type="submit" class="sp-logout">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
            </button>
        </form>
    </div>
</aside>

<div class="sp-main">
    <header class="sp-topbar">
        <div style="display:flex;align-items:center;gap:1rem;">
            <button id="menuBtn" onclick="document.getElementById('spSidebar').classList.toggle('open')"
                    style="display:none;background:none;border:none;color:var(--text-primary);cursor:pointer;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="sp-topbar-title">@yield('title', 'Dashboard')</div>
        </div>
        <div class="sp-topbar-right">
            <div class="sp-status-dot"></div>
            <span style="font-size:.8rem;color:var(--text-secondary);">Online</span>
            <span style="font-size:.8rem;color:var(--text-tertiary);">{{ now()->format('D, M d') }}</span>
        </div>
    </header>

    <main class="sp-content">
        @if(session('success'))
            <div class="flash-success">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="flash-error">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </main>
</div>

<script>
    if (window.innerWidth <= 768) {
        document.getElementById('menuBtn').style.display = 'block';
    }
</script>
@stack('scripts')
</body>
</html>