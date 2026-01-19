@extends('layouts.app')

@section('title', 'Add Vehicle')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Add New Vehicle</h1>
        <p class="mt-1 text-sm text-gray-600">Enter your vehicle's VIN to automatically retrieve its details</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ route('vehicles.store') }}" method="POST" x-data="vinDecoder()">
            @csrf

            <!-- VIN Number -->
            <div class="mb-6">
                <label for="vin" class="block text-sm font-medium text-gray-700 mb-2">
                    VIN Number <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2">
                    <input type="text" 
                           id="vin" 
                           name="vin" 
                           x-model="vin"
                           maxlength="17"
                           required
                           class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('vin') border-red-500 @enderror"
                           placeholder="Enter 17-character VIN">
                    <button type="button" 
                            @click="decodeVin()"
                            :disabled="loading || vin.length !== 17"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition">
                        <span x-show="!loading">Decode VIN</span>
                        <span x-show="loading">Decoding...</span>
                    </button>
                </div>
                @error('vin')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">The VIN can be found on your vehicle registration or driver's side door</p>
            </div>

            <!-- Decoded Vehicle Info -->
            <div x-show="decoded" x-cloak class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm font-medium text-green-800 mb-2">✓ Vehicle Decoded Successfully</p>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div><span class="text-gray-600">Make:</span> <span class="font-medium" x-text="vehicleData.make"></span></div>
                    <div><span class="text-gray-600">Model:</span> <span class="font-medium" x-text="vehicleData.model"></span></div>
                    <div><span class="text-gray-600">Year:</span> <span class="font-medium" x-text="vehicleData.year"></span></div>
                    <div><span class="text-gray-600">Trim:</span> <span class="font-medium" x-text="vehicleData.trim || 'N/A'"></span></div>
                </div>
            </div>

            <!-- Additional Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="license_plate" class="block text-sm font-medium text-gray-700 mb-2">
                        License Plate
                    </label>
                    <input type="text" 
                           id="license_plate" 
                           name="license_plate" 
                           value="{{ old('license_plate') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('license_plate') border-red-500 @enderror">
                    @error('license_plate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                        Color
                    </label>
                    <input type="text" 
                           id="color" 
                           name="color" 
                           value="{{ old('color') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('color') border-red-500 @enderror">
                    @error('color')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="purchase_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Purchase Date
                    </label>
                    <input type="date" 
                           id="purchase_date" 
                           name="purchase_date" 
                           value="{{ old('purchase_date') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('purchase_date') border-red-500 @enderror">
                    @error('purchase_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-2">
                        Purchase Price
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                        <input type="number" 
                               id="purchase_price" 
                               name="purchase_price" 
                               step="0.01"
                               value="{{ old('purchase_price') }}"
                               class="w-full pl-7 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('purchase_price') border-red-500 @enderror">
                    </div>
                    @error('purchase_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="current_mileage" class="block text-sm font-medium text-gray-700 mb-2">
                        Current Mileage
                    </label>
                    <input type="number" 
                           id="current_mileage" 
                           name="current_mileage" 
                           value="{{ old('current_mileage', 0) }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('current_mileage') border-red-500 @enderror">
                    @error('current_mileage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Primary Vehicle Checkbox -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="is_primary" 
                           value="1"
                           {{ old('is_primary') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Set as primary vehicle</span>
                </label>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('vehicles.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Add Vehicle
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function vinDecoder() {
    return {
        vin: '',
        loading: false,
        decoded: false,
        vehicleData: {},
        
        async decodeVin() {
            if (this.vin.length !== 17) return;
            
            this.loading = true;
            
            try {
                const response = await fetch(`{{ route('vehicles.decode-vin') }}?vin=${this.vin}`, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.vehicleData = data.data;
                    this.decoded = true;
                } else {
                    alert('Failed to decode VIN: ' + data.message);
                }
            } catch (error) {
                alert('Error decoding VIN. Please try again.');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection