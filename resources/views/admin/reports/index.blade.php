@extends('admin.layouts.app')
@section('title', 'Reports')
@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Reports</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="{{ route('admin.reports.overview') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
            <div class="bg-blue-100 p-3 rounded-full w-12 h-12 mb-4"><svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg></div>
            <h3 class="text-lg font-semibold">Overview Report</h3>
            <p class="text-sm text-gray-600">General statistics and trends</p>
        </a>
        <a href="{{ route('admin.reports.users') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
            <div class="bg-green-100 p-3 rounded-full w-12 h-12 mb-4"><svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg></div>
            <h3 class="text-lg font-semibold">User Report</h3>
            <p class="text-sm text-gray-600">User registrations and activity</p>
        </a>
        <a href="{{ route('admin.reports.revenue') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
            <div class="bg-yellow-100 p-3 rounded-full w-12 h-12 mb-4"><svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
            <h3 class="text-lg font-semibold">Revenue Report</h3>
            <p class="text-sm text-gray-600">Financial performance</p>
        </a>
        <a href="{{ route('admin.reports.vehicles') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
            <div class="bg-purple-100 p-3 rounded-full w-12 h-12 mb-4"><svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg></div>
            <h3 class="text-lg font-semibold">Vehicle Report</h3>
            <p class="text-sm text-gray-600">Vehicle statistics</p>
        </a>
    </div>
</div>
@endsection
