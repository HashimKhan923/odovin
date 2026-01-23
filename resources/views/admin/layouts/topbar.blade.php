<header class="h-16 bg-white border-b flex items-center justify-between px-6">
    <h1 class="text-lg font-semibold text-gray-800">
        @yield('title', 'Dashboard')
    </h1>

    <div class="flex items-center space-x-4" x-data="{ open: false }">
        <button @click="open = !open"
                class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
            {{ auth()->user()->name }}
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-show="open" @click.away="open=false" x-cloak
             class="absolute right-6 top-16 w-48 bg-white border rounded-lg shadow-lg">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit"
                        class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>
