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
            --bg-primary: #0a0e1a;
            --bg-secondary: #121827;
            --card-bg: rgba(26,32,48,0.85);
            --border-color: rgba(0,212,255,0.12);
            --accent-cyan: #00d4ff;
            --accent-green: #00ffaa;
            --accent-warning: #ffaa00;
            --accent-danger: #ff3366;
            --accent-purple: #a855f7;
            --text-primary: #fff;
            --text-secondary: rgba(255,255,255,0.68);
            --text-tertiary: rgba(255,255,255,0.42);
            --sidebar-w: 260px;
            --input-bg: rgba(0,212,255,0.05);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Chakra Petch', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
        }
        /* ── Sidebar ── */
        .sp-sidebar {
            width: var(--sidebar-w);
            background: rgba(10,14,26,0.98);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: transform .3s;
        }
        .sp-logo {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }
        .sp-logo-text {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.1rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .sp-logo-sub { font-size: 0.7rem; color: var(--text-tertiary); margin-top: 2px; }
        .sp-nav { flex: 1; overflow-y: auto; padding: 1rem 0; }
        .sp-nav-section { padding: 0.5rem 1rem; margin-bottom: 0.25rem; }
        .sp-nav-label { font-size: 0.65rem; text-transform: uppercase; letter-spacing: .1em; color: var(--text-tertiary); font-weight: 700; padding: 0.5rem 0.75rem; }
        .sp-nav-link {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.75rem 1rem; border-radius: 10px;
            color: var(--text-secondary); text-decoration: none;
            font-size: 0.875rem; font-weight: 500;
            transition: all .25s;
        }
        .sp-nav-link:hover { background: rgba(0,212,255,.08); color: var(--text-primary); }
        .sp-nav-link.active {
            background: linear-gradient(135deg, rgba(0,212,255,.18), rgba(0,255,170,.1));
            color: var(--accent-cyan);
            border-left: 3px solid var(--accent-cyan);
        }
        .sp-nav-link svg { width: 18px; height: 18px; flex-shrink: 0; }
        .sp-badge {
            margin-left: auto; background: var(--accent-danger);
            color: #fff; border-radius: 20px; font-size: 0.65rem;
            padding: 2px 7px; font-weight: 700;
        }
        .sp-footer { padding: 1rem; border-top: 1px solid var(--border-color); }
        .sp-provider-info { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; }
        .sp-avatar {
            width: 40px; height: 40px; border-radius: 10px;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
            display: flex; align-items: center; justify-content: center;
            font-family: 'Orbitron', sans-serif; font-weight: 800; font-size: 1rem; color: #000;
        }
        .sp-provider-name { font-size: 0.8rem; font-weight: 600; color: var(--text-primary); }
        .sp-provider-role { font-size: 0.7rem; color: var(--text-tertiary); }
        .sp-logout {
            display: flex; align-items: center; gap: 0.5rem;
            padding: 0.6rem 0.75rem; border-radius: 8px; width: 100%;
            background: rgba(255,51,102,.1); border: 1px solid rgba(255,51,102,.25);
            color: var(--accent-danger); font-size: 0.8rem; font-weight: 600;
            cursor: pointer; transition: all .25s; font-family: 'Chakra Petch', sans-serif;
        }
        .sp-logout:hover { background: rgba(255,51,102,.2); }
        /* ── Main ── */
        .sp-main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .sp-topbar {
            background: rgba(10,14,26,.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .sp-topbar-title { font-family: 'Orbitron', sans-serif; font-size: 1.1rem; font-weight: 700; }
        .sp-topbar-right { display: flex; align-items: center; gap: 1rem; }
        .sp-status-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--accent-green);
            box-shadow: 0 0 8px var(--accent-green);
        }
        .sp-content { padding: 2rem; flex: 1; max-width: 1400px; width: 100%; margin: 0 auto; }
        /* ── Flash Messages ── */
        .flash-success, .flash-error {
            padding: 0.875rem 1.25rem; border-radius: 12px;
            font-size: 0.875rem; font-weight: 600; margin-bottom: 1.5rem;
            display: flex; align-items: center; gap: 0.75rem;
        }
        .flash-success { background: rgba(0,255,170,.12); border: 1px solid rgba(0,255,170,.3); color: var(--accent-green); }
        .flash-error   { background: rgba(255,51,102,.12); border: 1px solid rgba(255,51,102,.3); color: var(--accent-danger); }
        @media(max-width:768px) {
            .sp-sidebar { transform: translateX(-100%); }
            .sp-sidebar.open { transform: translateX(0); }
            .sp-main { margin-left: 0; }
            .sp-content { padding: 1.25rem; }
        }

        /* ── Notification Bell ─────────────────────────────────────────────── */
        .notif-wrap { position: relative; }
        .notif-btn {
            position: relative; background: rgba(0,212,255,.08);
            border: 1px solid rgba(0,212,255,.15); border-radius: 10px;
            width: 40px; height: 40px; display: flex; align-items: center;
            justify-content: center; color: var(--text-secondary);
            cursor: pointer; transition: all .25s;
        }
        .notif-btn:hover { background: rgba(0,212,255,.18); color: var(--accent-cyan); border-color: rgba(0,212,255,.4); }
        .notif-btn.has-unread { color: var(--accent-cyan); border-color: rgba(0,212,255,.4); }
        .notif-btn.has-unread svg { filter: drop-shadow(0 0 6px rgba(0,212,255,.6)); }
        .notif-badge {
            position: absolute; top: -5px; right: -5px;
            background: var(--accent-danger); color: #fff;
            border-radius: 20px; font-size: .6rem; font-weight: 800;
            min-width: 18px; height: 18px; display: flex;
            align-items: center; justify-content: center;
            padding: 0 4px; border: 2px solid var(--bg-primary);
            animation: badgePulse 2s infinite;
        }
        @keyframes badgePulse {
            0%,100% { transform: scale(1); }
            50%      { transform: scale(1.15); }
        }
        .notif-dropdown {
            position: absolute; top: calc(100% + 10px); right: 0;
            width: 360px; background: rgba(18,24,39,.98);
            backdrop-filter: blur(24px); border: 1px solid var(--border-color);
            border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,.5);
            z-index: 200; display: none;
            animation: dropIn .2s ease;
        }
        .notif-dropdown.open { display: block; }
        @keyframes dropIn {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .notif-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color);
        }
        .notif-header-title { font-family: 'Orbitron', sans-serif; font-size: .875rem; font-weight: 700; }
        .notif-mark-all {
            background: none; border: none; font-size: .75rem; font-weight: 600;
            color: var(--accent-cyan); cursor: pointer; font-family: 'Chakra Petch', sans-serif;
            padding: .25rem .5rem; border-radius: 6px; transition: all .2s;
        }
        .notif-mark-all:hover { background: rgba(0,212,255,.1); }
        .notif-list { max-height: 380px; overflow-y: auto; }
        .notif-list::-webkit-scrollbar { width: 4px; }
        .notif-list::-webkit-scrollbar-track { background: transparent; }
        .notif-list::-webkit-scrollbar-thumb { background: rgba(0,212,255,.2); border-radius: 2px; }
        .notif-item {
            display: flex; gap: .875rem; padding: .875rem 1.25rem;
            border-bottom: 1px solid rgba(0,212,255,.04);
            cursor: pointer; transition: background .2s; text-decoration: none;
        }
        .notif-item:hover { background: rgba(0,212,255,.05); }
        .notif-item.unread { background: rgba(0,212,255,.04); }
        .notif-item.unread::before {
            content: ''; display: block; position: absolute;
            left: 0; top: 50%; transform: translateY(-50%);
            width: 3px; height: 60%; background: var(--accent-cyan);
            border-radius: 0 2px 2px 0;
        }
        .notif-item { position: relative; }
        .notif-icon {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .notif-icon svg { width: 16px; height: 16px; }
        .notif-title { font-size: .8rem; font-weight: 700; color: var(--text-primary); margin-bottom: .2rem; }
        .notif-msg   { font-size: .75rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: .25rem; }
        .notif-time  { font-size: .7rem; color: var(--text-tertiary); }
        .notif-unread-dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: var(--accent-cyan); flex-shrink: 0; margin-top: 4px;
            box-shadow: 0 0 6px var(--accent-cyan);
        }
        .notif-empty {
            padding: 3rem 1.5rem; text-align: center; color: var(--text-tertiary);
            font-size: .875rem;
        }
        .notif-empty svg { width: 48px; height: 48px; margin: 0 auto .75rem; display: block; opacity: .25; }
        .notif-loading {
            padding: 2rem; text-align: center; color: var(--text-tertiary);
            font-size: .8rem; display: flex; align-items: center; justify-content: center; gap: .5rem;
        }
        .notif-footer { padding: .75rem 1.25rem; border-top: 1px solid var(--border-color); text-align: center; }
        .spin { animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sp-sidebar" id="spSidebar">
        <div class="sp-logo">
            <div class="sp-logo-text">odovin</div>
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
                    @php $pending = Auth::user()->serviceProvider ? \App\Models\ServiceBooking::where('service_provider_id', Auth::user()->serviceProvider->id)->where('status','pending')->count() : 0 @endphp
                    @if($pending > 0)<span class="sp-badge">{{ $pending }}</span>@endif
                </a>
                <a href="{{ route('provider.bookings.calendar') }}" class="sp-nav-link {{ request()->routeIs('provider.bookings.calendar') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Calendar View
                </a>
            </div>

            <div class="sp-nav-section">
                <div class="sp-nav-label">Job Board</div>
                <a href="{{ route('provider.jobs.index') }}" class="sp-nav-link {{ request()->routeIs('provider.jobs.index') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Browse Open Jobs
                    @php $openJobs = \App\Models\ServiceJobPost::open()->count(); @endphp
                    @if($openJobs > 0)<span class="sp-badge" style="background:var(--accent-warning);color:#000;">{{ $openJobs }}</span>@endif
                </a>
                <a href="{{ route('provider.jobs.my-offers') }}" class="sp-nav-link {{ request()->routeIs('provider.jobs.my-offers') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    My Offers
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

    <!-- Main Content -->
    <div class="sp-main">
        <header class="sp-topbar">
            <div style="display:flex;align-items:center;gap:1rem;">
                <button onclick="document.getElementById('spSidebar').classList.toggle('open')" style="display:none;background:none;border:none;color:var(--text-primary);cursor:pointer;" id="menuBtn">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="sp-topbar-title">@yield('title', 'Dashboard')</div>
            </div>
            <div class="sp-topbar-right">
                <div class="sp-status-dot"></div>
                <span style="font-size:0.8rem;color:var(--text-secondary);">Online</span>
                <span style="font-size:0.8rem;color:var(--text-tertiary);">{{ now()->format('D, M d') }}</span>

                {{-- Notification Bell --}}
                <div class="notif-wrap" id="notifWrap">
                    <button class="notif-btn" id="notifBtn" onclick="toggleNotifDropdown()" aria-label="Notifications">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="notif-badge" id="notifBadge" style="display:none;">0</span>
                    </button>

                    <div class="notif-dropdown" id="notifDropdown">
                        <div class="notif-header">
                            <span class="notif-header-title">Notifications</span>
                            <button class="notif-mark-all" id="markAllBtn" onclick="markAllRead()">Mark all read</button>
                        </div>
                        <div class="notif-list" id="notifList">
                            <div class="notif-loading">
                                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="spin"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Loading…
                            </div>
                        </div>
                        <div class="notif-footer">
                            <span id="notifFooterCount" style="font-size:.75rem;color:var(--text-tertiary);"></span>
                        </div>
                    </div>
                </div>
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
        if(window.innerWidth <= 768) {
            document.getElementById('menuBtn').style.display = 'block';
        }
    </script>

    <script>
    // ── Provider Notification Bell ───────────────────────────────────────────
    const FETCH_URL      = '{{ route("alerts.fetch") }}';
    const MARK_ALL_URL   = '{{ route("alerts.mark-all-read") }}';
    const MARK_READ_BASE = '{{ url("/alerts") }}';
    const CSRF          = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    let notifOpen = false;
    let notifData = [];

    function toggleNotifDropdown() {
        notifOpen = !notifOpen;
        document.getElementById('notifDropdown').classList.toggle('open', notifOpen);
        if (notifOpen) fetchNotifications();
    }

    // Close on outside click
    document.addEventListener('click', function(e) {
        if (notifOpen && !document.getElementById('notifWrap').contains(e.target)) {
            notifOpen = false;
            document.getElementById('notifDropdown').classList.remove('open');
        }
    });

    function iconSvg(type) {
        const icons = {
            calendar : `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`,
            wrench   : `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`,
            currency : `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
            warning  : `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`,
            bell     : `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>`,
        };
        return icons[type] ?? icons.bell;
    }

    function renderNotifications(data) {
        const list = document.getElementById('notifList');
        const badge = document.getElementById('notifBadge');
        const btn   = document.getElementById('notifBtn');
        const footer = document.getElementById('notifFooterCount');
        const unread = data.unread_count;
        notifData = data.notifications;

        // Update badge
        if (unread > 0) {
            badge.textContent = unread > 99 ? '99+' : unread;
            badge.style.display = 'flex';
            btn.classList.add('has-unread');
        } else {
            badge.style.display = 'none';
            btn.classList.remove('has-unread');
        }

        footer.textContent = unread > 0 ? `${unread} unread notification${unread > 1 ? 's' : ''}` : 'All caught up!';

        if (!notifData.length) {
            list.innerHTML = `<div class="notif-empty">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                No notifications yet
            </div>`;
            return;
        }

        list.innerHTML = notifData.map(n => {
            const url = n.action_url ?? '#';
            const colorHex = n.color ?? '#00d4ff';
            const alpha18  = colorHex + '1e';
            return `<a href="${url}" class="notif-item ${n.is_read ? '' : 'unread'}"
                       onclick="handleNotifClick(event, ${n.id}, '${url}')">
                <div class="notif-icon" style="background:${alpha18};color:${colorHex};">
                    ${iconSvg(n.icon)}
                </div>
                <div style="flex:1;min-width:0;">
                    <div class="notif-title">${n.title}</div>
                    <div class="notif-msg">${n.message}</div>
                    <div class="notif-time">${n.time}</div>
                </div>
                ${!n.is_read ? '<div class="notif-unread-dot"></div>' : ''}
            </a>`;
        }).join('');
    }

    async function fetchNotifications() {
        document.getElementById('notifList').innerHTML =
            `<div class="notif-loading"><svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="spin"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Loading…</div>`;
        try {
            const res  = await fetch(FETCH_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            renderNotifications(data);
        } catch (e) {
            document.getElementById('notifList').innerHTML =
                `<div class="notif-empty">Failed to load. <a href="#" onclick="fetchNotifications();return false;" style="color:var(--accent-cyan);">Retry</a></div>`;
        }
    }

    async function handleNotifClick(e, id, url) {
        e.preventDefault();
        // Mark as read via POST
        await fetch(`${MARK_READ_BASE}/${id}/read`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        });
        if (url && url !== '#') window.location.href = url;
        else fetchNotifications();
    }

    async function markAllRead() {
        await fetch(MARK_ALL_URL, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        });
        fetchNotifications();
    }

    // Poll for new notifications every 45 seconds
    function pollNotifications() {
        fetch(FETCH_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                const badge = document.getElementById('notifBadge');
                const btn   = document.getElementById('notifBtn');
                const unread = data.unread_count;
                if (unread > 0) {
                    badge.textContent = unread > 99 ? '99+' : unread;
                    badge.style.display = 'flex';
                    btn.classList.add('has-unread');
                } else {
                    badge.style.display = 'none';
                    btn.classList.remove('has-unread');
                }
                // If dropdown is open, refresh list too
                if (notifOpen) renderNotifications(data);
            })
            .catch(() => {});
    }

    // Initial badge load on page ready
    document.addEventListener('DOMContentLoaded', function() {
        pollNotifications();
        setInterval(pollNotifications, 45000);
    });
    </script>

    @stack('scripts')
</body>
</html>