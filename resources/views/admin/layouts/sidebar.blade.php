<aside class="w-64 bg-white border-r border-gray-200 min-h-screen">
    <div class="h-16 flex items-center px-6 border-b">
        <span class="text-xl font-bold text-blue-600">🚗 OdoVin Admin</span>
    </div>

    <nav class="p-4 space-y-1">

        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center px-4 py-2 rounded-lg text-sm font-medium
           {{ request()->routeIs('admin.dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
            Dashboard
        </a>

        <a href="#"
           class="flex items-center px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100">
            Users
        </a>

        <a href="#"
           class="flex items-center px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100">
            Vehicles
        </a>

        <a href="#"
           class="flex items-center px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100">
            Fuel Logs
        </a>

        <a href="#"
           class="flex items-center px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100">
            Expenses
        </a>

        <a href="#"
           class="flex items-center px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100">
            Maintenance
        </a>

        <a href="#"
           class="flex items-center px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100">
            Recalls
        </a>

    </nav>
</aside>
