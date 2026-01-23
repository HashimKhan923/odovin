@extends('layouts.app')

@section('title', 'Edit Fuel Log')

@section('content')
<div class="max-w-3xl mx-auto px-4">
    <h1 class="text-3xl font-bold mb-6">Edit Fuel Log</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('fuel.update', $fuelLog) }}">
            @csrf
            @method('PUT')

            @include('fuel._form', ['fuelLog' => $fuelLog])

            <div class="flex justify-end mt-6 gap-3">
                <a href="{{ route('fuel.index') }}" class="px-4 py-2 border rounded-lg">
                    Cancel
                </a>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
