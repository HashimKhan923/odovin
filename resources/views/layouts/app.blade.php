<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Odovin') }} - @yield('title', 'Dashboard')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>[x-cloak]{display:none}</style>
</head>

<body class="bg-gray-100 font-sans antialiased">

<div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-30 w-64 bg-slate-900 text-white transform transition-transform duration-200 ease-in-out
               md:relative md:translate-x-0"
    >
        <div class="h-16 flex items-center px-6 text-xl font-bold border-b border-slate-700">
            Odovin
        </div>

        <nav class="px-4 py-4 space-y-1 text-sm">
            @php
                $nav = [
                    ['Dashboard','dashboard'],
                    ['My Vehicles','vehicles.index'],
                    ['Bookings','bookings.index'],
                    ['Maintenance','maintenance.index'],
                    ['Expenses','expenses.index'],
                    ['Fuel Logs','fuel.index'],
                    ['Reports','reports.index'],
                    ['Compare','comparison.index'],
                ];
            @endphp

            @foreach($nav as [$label, $route])
                <a href="{{ route($route) }}"
                   class="block px-4 py-2 rounded-lg
                   {{ request()->routeIs(Str::before($route,'*').'*')
                        ? 'bg-slate-800 text-white'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    </aside>

    <!-- OVERLAY (MOBILE) -->
    <div x-show="sidebarOpen" @click="sidebarOpen=false"
         class="fixed inset-0 bg-black bg-opacity-40 z-20 md:hidden"
         x-cloak>
    </div>

    <!-- MAIN -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- TOP BAR -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="md:hidden text-gray-600">
                    ☰
                </button>

                <h1 class="text-lg font-semibold text-gray-800">
                    @yield('title','Dashboard')
                </h1>
            </div>

            <!-- RIGHT -->
            <div class="flex items-center gap-4">

                <!-- Notifications -->
                <div class="relative" x-data="{open:false}">
                    <button @click="open=!open" class="relative">
                        🔔
                        @if($count = auth()->user()->alerts()->where('is_read',false)->count())
                            <span class="absolute -top-1 -right-1 text-xs bg-red-600 text-white px-1 rounded">
                                {{ $count }}
                            </span>
                        @endif
                    </button>

                    <div x-show="open" @click.away="open=false"
                         class="absolute right-0 mt-2 w-72 bg-white rounded-lg shadow border z-50"
                         x-cloak>
                        <div class="p-3 border-b font-semibold">Notifications</div>
                        @forelse(auth()->user()->alerts()->latest()->limit(5)->get() as $alert)
                            <a href="{{ route('alerts.mark-read',$alert) }}"
                               class="block p-3 text-sm hover:bg-gray-50">
                                {{ $alert->title }}
                            </a>
                        @empty
                            <p class="p-3 text-sm text-gray-500">No notifications</p>
                        @endforelse
                    </div>
                </div>

                <!-- User -->
                <div class="relative" x-data="{open:false}">
                    <button @click="open=!open" class="text-sm font-medium">
                        {{ auth()->user()->name }}
                    </button>

                    <div x-show="open" @click.away="open=false"
                         class="absolute right-0 mt-2 w-40 bg-white rounded shadow border z-50"
                         x-cloak>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </header>

        <!-- CONTENT -->
        <main class="flex-1 overflow-y-auto p-6">
            @if(session('success'))
                <div class="mb-4 bg-green-100 text-green-700 px-4 py-2 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 text-red-700 px-4 py-2 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@stack('scripts')

</body>
</html>
