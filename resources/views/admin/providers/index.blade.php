@extends('admin.layouts.app')
@section('title', 'Service Providers')
@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Service Providers</h1>
        <a href="{{ route('admin.providers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">+ Add Provider</a>
    </div>
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form method="GET" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search providers..." class="flex-1 border rounded-lg px-4 py-2">
            <select name="status" class="border rounded-lg px-4 py-2">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg">Filter</button>
        </form>
    </div>
    <div class="bg-white rounded-lg shadow">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bookings</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($providers as $provider)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ $provider->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $provider->email }}<br>{{ $provider->phone }}</td>
                    <td class="px-6 py-4">{{ $provider->bookings_count }}</td>
                    <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full {{ $provider->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ ucfirst($provider->status) }}</span></td>
                    <td class="px-6 py-4 text-sm">
                        <a href="{{ route('admin.providers.show', $provider) }}" class="text-blue-600 mr-3">View</a>
                        <a href="{{ route('admin.providers.edit', $provider) }}" class="text-green-600 mr-3">Edit</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">No providers found</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($providers->hasPages())<div class="px-6 py-4">{{ $providers->links() }}</div>@endif
    </div>
</div>
@endsection
