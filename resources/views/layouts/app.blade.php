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
        
        /* Global Dark Theme Variables */
        :root {
            --nav-bg: rgba(10, 14, 26, 0.95);
            --nav-border: rgba(0, 212, 255, 0.2);
            --accent-primary: #00d4ff;
            --accent-secondary: #00ffaa;
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.7);
            --text-tertiary: rgba(255, 255, 255, 0.5);
        }
        
        body {
            margin: 0;
            padding: 0;
            background: #0a0e1a;
            font-family: 'Chakra Petch', sans-serif;
        }
        
        /* Navigation Styling */
        .nav-container {
            background: var(--nav-bg);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--nav-border);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 30px rgba(0, 212, 255, 0.1);
        }
        
        .nav-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }
        
        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 3rem;
        }
        
        .logo {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.75rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
            letter-spacing: 0.05em;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .logo::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary));
            transition: width 0.3s ease;
        }
        
        .logo:hover::after {
            width: 100%;
        }
        
        .nav-links {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .nav-link {
            position: relative;
            padding: 0.75rem 1.25rem;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            letter-spacing: 0.025em;
            transition: all 0.3s ease;
            border-radius: 8px;
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary));
            transition: width 0.3s ease;
        }
        
        .nav-link:hover {
            color: var(--text-primary);
            background: rgba(0, 212, 255, 0.05);
        }
        
        .nav-link:hover::before {
            width: 80%;
        }
        
        .nav-link.active {
            color: var(--accent-primary);
            background: rgba(0, 212, 255, 0.1);
        }
        
        .nav-link.active::before {
            width: 80%;
        }
        
        .nav-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        
        /* Alert Bell */
        .alert-button {
            position: relative;
            padding: 0.75rem;
            background: rgba(0, 212, 255, 0.05);
            border: 1px solid rgba(0, 212, 255, 0.2);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .alert-button:hover {
            background: rgba(0, 212, 255, 0.1);
            border-color: var(--accent-primary);
            box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        }
        
        .alert-button svg {
            width: 24px;
            height: 24px;
            color: var(--text-secondary);
            transition: color 0.3s ease;
        }
        
        .alert-button:hover svg {
            color: var(--accent-primary);
        }
        
        .alert-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            background: linear-gradient(135deg, #ff3366, #ff6699);
            color: white;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 15px rgba(255, 51, 102, 0.5);
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .alert-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 1rem);
            width: 380px;
            max-height: 500px;
            background: rgba(18, 24, 39, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid var(--nav-border);
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            z-index: 1001;
        }
        
        .dropdown-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0, 212, 255, 0.1);
        }
        
        .dropdown-header h3 {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .dropdown-content {
            max-height: 350px;
            overflow-y: auto;
        }
        
        .alert-item {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(0, 212, 255, 0.05);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .alert-item:hover {
            background: rgba(0, 212, 255, 0.05);
        }
        
        .alert-item.unread {
            background: rgba(0, 212, 255, 0.03);
            border-left: 3px solid var(--accent-primary);
        }
        
        .alert-item-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }
        
        .alert-item-message {
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }
        
        .alert-item-time {
            font-size: 0.75rem;
            color: var(--text-tertiary);
        }
        
        .dropdown-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(0, 212, 255, 0.1);
            text-align: center;
        }
        
        .dropdown-footer a {
            color: var(--accent-primary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .dropdown-footer a:hover {
            text-shadow: 0 0 10px var(--accent-primary);
        }
        
        /* User Dropdown */
        .user-button {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 1.25rem;
            background: rgba(0, 212, 255, 0.05);
            border: 1px solid rgba(0, 212, 255, 0.2);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .user-button:hover {
            background: rgba(0, 212, 255, 0.1);
            border-color: var(--accent-primary);
            box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        }
        
        .user-name {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-primary);
        }
        
        .user-button svg {
            width: 18px;
            height: 18px;
            color: var(--text-secondary);
            transition: transform 0.3s ease;
        }
        
        .user-button[aria-expanded="true"] svg {
            transform: rotate(180deg);
        }
        
        .user-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 1rem);
            width: 220px;
            background: rgba(18, 24, 39, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid var(--nav-border);
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            z-index: 1001;
        }
        
        .user-dropdown a,
        .user-dropdown button {
            display: block;
            width: 100%;
            padding: 1rem 1.5rem;
            background: none;
            border: none;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            text-align: left;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Chakra Petch', sans-serif;
        }
        
        .user-dropdown a:hover,
        .user-dropdown button:hover {
            background: rgba(0, 212, 255, 0.1);
            color: var(--text-primary);
        }
        
        /* Flash Messages */
        .flash-message {
            margin: 2rem auto;
            max-width: 1400px;
            padding: 0 1.5rem;
        }
        
        .alert {
            padding: 1.25rem 1.5rem;
            border-radius: 12px;
            backdrop-filter: blur(20px);
            display: flex;
            align-items: center;
            gap: 1rem;
            animation: slideInDown 0.4s ease-out;
        }
        
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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

        .right-4 {
            right: 3rem;
        }
        
        /* Mobile Menu */
        @media (max-width: 1024px) {
            .nav-links {
                display: none;
            }
            
            .mobile-menu-button {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 44px;
                height: 44px;
                background: rgba(0, 212, 255, 0.05);
                border: 1px solid rgba(0, 212, 255, 0.2);
                border-radius: 10px;
                cursor: pointer;
            }
            
            .mobile-menu-button svg {
                width: 24px;
                height: 24px;
                color: var(--text-secondary);
            }
        }
        
        @media (min-width: 1025px) {
            .mobile-menu-button {
                display: none;
            }
        }
        
        /* Dropdown scrollbar */
        .dropdown-content::-webkit-scrollbar {
            width: 6px;
        }
        
        .dropdown-content::-webkit-scrollbar-track {
            background: rgba(0, 212, 255, 0.05);
        }
        
        .dropdown-content::-webkit-scrollbar-thumb {
            background: var(--accent-primary);
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="nav-container">
            <div class="nav-wrapper">
                <div class="nav-content">
                    <div class="logo-section">
                        <!-- Logo -->
                        <a href="{{ route('dashboard') }}" class="logo">
                            Odovin
                        </a>

                        <!-- Navigation Links -->
                        <div class="nav-links">
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('vehicles.index') }}" class="nav-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}">
                                Vehicles
                            </a>
                            <a href="{{ route('bookings.index') }}" class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                                Bookings
                            </a>
                            <a href="{{ route('maintenance.index') }}" class="nav-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}">
                                Maintenance
                            </a>
                            <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                                Expenses
                            </a>
                            <a href="{{ route('fuel.index') }}" class="nav-link {{ request()->routeIs('fuel.*') ? 'active' : '' }}">
                                Fuel
                            </a>
                            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                                Reports
                            </a>
                            <a href="{{ route('comparison.index') }}" class="nav-link {{ request()->routeIs('comparison.*') ? 'active' : '' }}">
                                Compare
                            </a>
                        </div>
                    </div>

                    <!-- Right Side -->
                    <div class="nav-right">
                        <!-- Alerts Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="alert-button">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                @if($unreadAlerts = auth()->user()->alerts()->where('is_read', false)->count())
                                    <span class="alert-badge">{{ $unreadAlerts }}</span>
                                @endif
                            </button>

                            <div x-show="open" @click.away="open = false" x-cloak class="alert-dropdown">
                                <div class="dropdown-header">
                                    <h3>Notifications</h3>
                                </div>
                                <div class="dropdown-content">
                                    @forelse(auth()->user()->alerts()->latest()->limit(5)->get() as $alert)
                                        <a href="{{ route('alerts.mark-read', $alert) }}" class="alert-item {{ $alert->is_read ? '' : 'unread' }}">
                                            <div class="alert-item-title">{{ $alert->title }}</div>
                                            <div class="alert-item-message">{{ $alert->message }}</div>
                                            <div class="alert-item-time">{{ $alert->created_at->diffForHumans() }}</div>
                                        </a>
                                    @empty
                                        <div style="padding: 3rem 1.5rem; text-align: center; color: var(--text-tertiary);">
                                            No notifications
                                        </div>
                                    @endforelse
                                </div>
                                <div class="dropdown-footer">
                                    <a href="{{ route('alerts.index') }}">View All Notifications</a>
                                </div>
                            </div>
                        </div>

                        <!-- User Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="user-button" :aria-expanded="open">
                                <span class="user-name">{{ Auth::user()->name }}</span>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-cloak class="user-dropdown">
                                <a href="{{ route('profile.edit') }}">Profile Settings</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit">Logout</button>
                                </form>
                            </div>
                        </div>

                        <!-- Mobile Menu Button -->
                        <button class="mobile-menu-button">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="flash-message">
                <div class="alert alert-success">
                    <svg style="width: 24px; height: 24px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="flash-message">
                <div class="alert alert-error">
                    <svg style="width: 24px; height: 24px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    @stack('scripts')
</body>
</html>