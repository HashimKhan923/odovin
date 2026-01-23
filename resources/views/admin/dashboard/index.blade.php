@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <div class="bg-white p-6 rounded-lg shadow">
        <p class="text-sm text-gray-500">Total Users</p>
        <p class="text-2xl font-semibold">{{ $stats['users_total'] }}</p>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <p class="text-sm text-gray-500">Total Vehicles</p>
        <p class="text-2xl font-semibold">{{ $stats['vehicles_total'] }}</p>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <p class="text-sm text-gray-500">Total Service Providers</p>
        <p class="text-2xl font-semibold">{{ $stats['providers_total'] }}</p>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <p class="text-sm text-gray-500">Total Bookings</p>
        <p class="text-2xl font-semibold">{{ $stats['bookings_total'] }}</p>
    </div>



    <div class="bg-white p-6 rounded-lg shadow">
        <p class="text-sm text-gray-500">Total Recalls</p>
        <p class="text-2xl font-semibold">{{ $stats['recalls_open'] }}</p>
    </div>



</div>
@endsection
