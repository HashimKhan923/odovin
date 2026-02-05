<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Odovin') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300;400;500;600;700&family=Orbitron:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        
        /* Dark Theme (Default) */
        :root[data-theme="dark"], :root {
            --bg-primary: #0a0e1a;
            --bg-secondary: #121827;
            --bg-tertiary: #1a2030;
            --bg-card: rgba(26, 32, 48, 0.8);
            --bg-card-hover: rgba(30, 38, 58, 0.9);
            --sidebar-bg: rgba(10, 14, 26, 0.98);
            --sidebar-border: rgba(0, 212, 255, 0.1);
            --card-bg: rgba(26, 32, 48, 0.8);
            --input-bg: rgba(0, 212, 255, 0.05);
            
            --accent-primary: #00d4ff;
            --accent-secondary: #00ffaa;
            --accent-cyan: #00d4ff;
            --accent-green: #00ffaa;
            --accent-danger: #ff3366;
            --accent-warning: #ffaa00;
            --accent-success: #00ff88;
            
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.7);
            --text-tertiary: rgba(255, 255, 255, 0.5);
            
            --border-color: rgba(0, 212, 255, 0.1);
            --border-color-hover: rgba(0, 212, 255, 0.3);
            --shadow-color: rgba(0, 212, 255, 0.1);
            --dropdown-bg: rgba(18, 24, 39, 0.98);
            
            --glow-primary: 0 0 20px rgba(0, 212, 255, 0.3);
            --glow-secondary: 0 0 30px rgba(0, 255, 170, 0.2);
        }
        
        /* Light Theme */
        :root[data-theme="light"] {
            --bg-primary: #f8fafc;
            --bg-secondary: #ffffff;
            --bg-tertiary: #f1f5f9;
            --bg-card: rgba(255, 255, 255, 0.9);
            --bg-card-hover: rgba(248, 250, 252, 1);
            --sidebar-bg: rgba(255, 255, 255, 0.98);
            --sidebar-border: rgba(0, 0, 0, 0.1);
            --card-bg: rgba(255, 255, 255, 0.9);
            --input-bg: rgba(0, 0, 0, 0.02);
            
            --accent-primary: #0066ff;
            --accent-secondary: #00cc88;
            --accent-cyan: #0066ff;
            --accent-green: #00cc88;
            --accent-danger: #ff3366;
            --accent-warning: #ff9500;
            --accent-success: #00cc66;
            
            --text-primary: #1a1f36;
            --text-secondary: rgba(26, 31, 54, 0.7);
            --text-tertiary: rgba(26, 31, 54, 0.5);
            
            --border-color: rgba(0, 0, 0, 0.1);
            --border-color-hover: rgba(0, 102, 255, 0.3);
            --shadow-color: rgba(0, 0, 0, 0.1);
            --dropdown-bg: rgba(255, 255, 255, 0.98);
            
            --glow-primary: 0 0 20px rgba(0, 102, 255, 0.15);
            --glow-secondary: 0 0 30px rgba(0, 204, 136, 0.15);
        }
        
        body {
            margin: 0;
            padding: 0;
            background: var(--bg-primary);
            font-family: 'Chakra Petch', sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        /* Smooth theme transitions for all elements */
        *,
        *::before,
        *::after {
            transition-property: background-color, color, border-color, box-shadow;
            transition-duration: 0.3s;
            transition-timing-function: ease;
        }
        
        /* Don't transition animations and transforms */
        * {
            transition-property: background-color, color, border-color, box-shadow;
        }

        .app-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--sidebar-border);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease, width 0.3s ease;
            box-shadow: 4px 0 30px rgba(0, 212, 255, 0.1);
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 3px;
        }

        .sidebar-header {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--sidebar-border);
        }

        .logo {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
            letter-spacing: 0.05em;
            white-space: nowrap;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .logo {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .sidebar-toggle {
            padding: 0.5rem;
            background: rgba(0, 212, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .sidebar-toggle:hover {
            background: rgba(0, 212, 255, 0.1);
            border-color: var(--accent-primary);
        }

        .sidebar-toggle svg {
            width: 20px;
            height: 20px;
            color: var(--text-secondary);
            transition: transform 0.3s ease;
        }

        .sidebar.collapsed .sidebar-toggle svg {
            transform: rotate(180deg);
        }

        .sidebar-nav {
            padding: 1rem;
        }

        .nav-section {
            margin-bottom: 2rem;
        }

        .nav-section-title {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-tertiary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0 1rem;
            margin-bottom: 0.5rem;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .nav-section-title {
            opacity: 0;
            height: 0;
            margin: 0;
            overflow: hidden;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.875rem 1rem;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            position: relative;
            margin-bottom: 0.25rem;
        }

        .nav-item:hover {
            background: rgba(0, 212, 255, 0.05);
            color: var(--text-primary);
        }

        .nav-item.active {
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.15), rgba(0, 255, 170, 0.15));
            color: var(--accent-primary);
            box-shadow: 0 4px 15px rgba(0, 212, 255, 0.2);
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: linear-gradient(180deg, var(--accent-primary), var(--accent-secondary));
            border-radius: 0 2px 2px 0;
        }

        .nav-icon {
            width: 22px;
            height: 22px;
            flex-shrink: 0;
            transition: transform 0.3s ease;
        }

        .nav-item:hover .nav-icon {
            transform: scale(1.1);
        }

        .nav-text {
            font-size: 0.875rem;
            font-weight: 500;
            white-space: nowrap;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .nav-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .sidebar-footer {
            position: sticky;
            bottom: 0;
            background: var(--sidebar-bg);
            border-top: 1px solid var(--sidebar-border);
            padding: 1rem;
        }

        .footer-button {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            background: rgba(0, 212, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.875rem;
            width: 100%;
            margin-bottom: 0.5rem;
        }

        .footer-button:hover {
            background: rgba(0, 212, 255, 0.1);
            border-color: var(--accent-primary);
            color: var(--text-primary);
        }

        .footer-button svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .footer-button span {
            white-space: nowrap;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .footer-button span {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .sidebar.collapsed .footer-button {
            padding: 0.75rem;
            justify-content: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            transition: margin-left 0.3s ease;
        }

        .sidebar.collapsed ~ .main-content {
            margin-left: 80px;
        }

        .top-bar {
            background: var(--sidebar-bg);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--sidebar-border);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 30px rgba(0, 212, 255, 0.1);
        }

        .top-bar-left h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        .top-bar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .theme-toggle, .alert-button, .user-button {
            padding: 0.625rem;
            background: rgba(0, 212, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .theme-toggle:hover, .alert-button:hover, .user-button:hover {
            background: rgba(0, 212, 255, 0.1);
            border-color: var(--accent-primary);
        }

        .theme-toggle svg, .alert-button svg {
            width: 20px;
            height: 20px;
            color: var(--text-secondary);
        }

        [data-theme="dark"] .sun-icon { display: block; }
        [data-theme="dark"] .moon-icon { display: none; }
        [data-theme="light"] .sun-icon { display: none; }
        [data-theme="light"] .moon-icon { display: block; }

        .alert-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            background: linear-gradient(135deg, #ff3366, #ff6699);
            color: white;
            font-size: 0.65rem;
            font-weight: 700;
            border-radius: 9px;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .alert-dropdown, .user-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 0.5rem);
            background: var(--dropdown-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            z-index: 1001;
        }

        .alert-dropdown {
            width: 360px;
            max-height: 480px;
        }

        .user-dropdown {
            width: 200px;
        }

        .dropdown-header {
            padding: 1.25rem;
            border-bottom: 1px solid var(--border-color);
        }

        .dropdown-header h3 {
            font-family: 'Orbitron', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .alert-item {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            display: block;
            color: inherit;
        }

        .alert-item:hover {
            background: rgba(0, 212, 255, 0.05);
        }

        .alert-item.unread {
            background: rgba(0, 212, 255, 0.03);
            border-left: 3px solid var(--accent-primary);
        }

        .user-dropdown a, .user-dropdown button {
            display: block;
            width: 100%;
            padding: 0.875rem 1.25rem;
            background: none;
            border: none;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.875rem;
            text-align: left;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Chakra Petch', sans-serif;
        }

        .user-dropdown a:hover, .user-dropdown button:hover {
            background: rgba(0, 212, 255, 0.1);
            color: var(--text-primary);
        }

        .flash-message {
            margin: 1.5rem 2rem;
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 1rem;
            animation: slideInDown 0.4s ease-out;
        }

        @keyframes slideInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-success {
            background: rgba(0, 255, 136, 0.1);
            border: 1px solid rgba(0, 255, 136, 0.3);
            color: #00ff88;
        }

        .alert-error {
            background: rgba(255, 51, 102, 0.1);
            border: 1px solid rgba(255, 51, 102, 0.3);
            color: #ff3366;
        }

        .relative { position: relative; }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .top-bar {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="app-layout">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}" class="logo">Odovin</a>
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </button>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Main</div>
                    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="nav-text">Dashboard</span>
                    </a>
                    <a href="{{ route('vehicles.index') }}" class="nav-item {{ request()->routeIs('vehicles.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21a2 2 0 01-2 2H7a2 2 0 01-2-2m14-7a2 2 0 00-2-2H7a2 2 0 00-2 2v4a2 2 0 002 2h10a2 2 0 002-2v-4zM9 8V6a3 3 0 013-3h0a3 3 0 013 3v2"/>
                        </svg>
                        <span class="nav-text">Vehicles</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Operations</div>
                    <a href="{{ route('bookings.index') }}" class="nav-item {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="nav-text">Bookings</span>
                    </a>
                    <a href="{{ route('maintenance.index') }}" class="nav-item {{ request()->routeIs('maintenance.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="nav-text">Maintenance</span>
                    </a>
                    <a href="{{ route('fuel.index') }}" class="nav-item {{ request()->routeIs('fuel.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span class="nav-text">Fuel</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Finance</div>
                    <a href="{{ route('expenses.index') }}" class="nav-item {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="nav-text">Expenses</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="nav-text">Reports</span>
                    </a>
                    <a href="{{ route('comparison.index') }}" class="nav-item {{ request()->routeIs('comparison.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="nav-text">Compare</span>
                    </a>
                </div>
            </nav>

            <div class="sidebar-footer">
                <a href="{{ route('profile.edit') }}" class="footer-button">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>Profile</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="footer-button">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="main-content">
            <div class="top-bar">
                <div class="top-bar-left">
                    <h1>@yield('title', 'Dashboard')</h1>
                </div>
                <div class="top-bar-right">
                    <button onclick="toggleTheme()" class="theme-toggle">
                        <svg class="sun-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <svg class="moon-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </button>

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="alert-button">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if($unreadAlerts = auth()->user()->alerts()->where('is_read', false)->count())
                                <span class="alert-badge">{{ $unreadAlerts }}</span>
                            @endif
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak class="alert-dropdown">
                            <div class="dropdown-header">
                                <h3>Notifications</h3>
                            </div>
                            <div style="max-height: 320px; overflow-y: auto;">
                                @forelse(auth()->user()->alerts()->latest()->limit(5)->get() as $alert)
                                    <a href="{{ route('alerts.mark-read', $alert) }}" class="alert-item {{ $alert->is_read ? '' : 'unread' }}">
                                        <div style="font-size: 0.8rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem;">{{ $alert->title }}</div>
                                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.5rem;">{{ $alert->message }}</div>
                                        <div style="font-size: 0.7rem; color: var(--text-tertiary);">{{ $alert->created_at->diffForHumans() }}</div>
                                    </a>
                                @empty
                                    <div style="padding: 3rem 1.5rem; text-align: center; color: var(--text-tertiary);">No notifications</div>
                                @endforelse
                            </div>
                            <div style="padding: 1rem 1.25rem; border-top: 1px solid var(--border-color); text-align: center;">
                                <a href="{{ route('alerts.index') }}" style="color: var(--accent-primary); text-decoration: none; font-size: 0.8rem;">View All</a>
                            </div>
                        </div>
                    </div>

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="user-button" :aria-expanded="open">
                            <span style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">{{ Auth::user()->name }}</span>
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak class="user-dropdown">
                            <a href="{{ route('profile.edit') }}">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="flash-message">
                    <div class="alert alert-success">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="flash-message">
                    <div class="alert alert-error">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <main>
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function initTheme() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        }

        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        }

        function initSidebar() {
            const sidebar = document.getElementById('sidebar');
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
            }
        }

        initTheme();
        initSidebar();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    @stack('scripts')
</body>
</html>