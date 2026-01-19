@extends('layouts.app')

@section('title', 'Recalls')

@section('content')
<div class="max-w-5xl mx-auto px-4">
    <h1 class="text-3xl font-bold mb-6">Vehicle Recalls</h1>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Vehicle</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Open Recalls</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($vehicles as $vehicle)
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $vehicle->full_name }}
                        </td>
                        <td class="px-6 py-4">
                            @if($vehicle->open_recalls_count > 0)
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                    {{ $vehicle->open_recalls_count }} Open
                                </span>
                            @else
                                <span class="text-sm text-gray-500">None</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('recalls.check', $vehicle) }}"
                               class="text-blue-600 hover:text-blue-800">
                                View Recalls
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
