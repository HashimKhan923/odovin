<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <div class="md:col-span-2">
        <label class="block text-sm font-medium">Vehicle *</label>
        <select name="vehicle_id" required class="w-full rounded-lg border-gray-300">
            @foreach($vehicles as $vehicle)
                <option value="{{ $vehicle->id }}"
                    {{ old('vehicle_id', $fuelLog->vehicle_id ?? null) == $vehicle->id ? 'selected' : '' }}>
                    {{ $vehicle->full_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium">Fill Date *</label>
        <input type="date" name="fill_date"
               value="{{ old('fill_date', optional($fuelLog->fill_date ?? null)->toDateString()) }}"
               class="w-full rounded-lg border-gray-300">
    </div>

    <div>
        <label class="block text-sm font-medium">Odometer *</label>
        <input type="number" name="odometer"
               value="{{ old('odometer', $fuelLog->odometer ?? '') }}"
               class="w-full rounded-lg border-gray-300">
    </div>

    <div>
        <label class="block text-sm font-medium">Gallons *</label>
        <input type="number" step="0.01" name="gallons"
               value="{{ old('gallons', $fuelLog->gallons ?? '') }}"
               class="w-full rounded-lg border-gray-300">
    </div>

    <div>
        <label class="block text-sm font-medium">Price / Gallon *</label>
        <input type="number" step="0.01" name="price_per_gallon"
               value="{{ old('price_per_gallon', $fuelLog->price_per_gallon ?? '') }}"
               class="w-full rounded-lg border-gray-300">
    </div>

    <div>
        <label class="block text-sm font-medium">Total Cost *</label>
        <input type="number" step="0.01" name="total_cost"
               value="{{ old('total_cost', $fuelLog->total_cost ?? '') }}"
               class="w-full rounded-lg border-gray-300">
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_full_tank" value="1"
            {{ old('is_full_tank', $fuelLog->is_full_tank ?? true) ? 'checked' : '' }}>
        <span class="text-sm">Full tank</span>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium">Gas Station</label>
        <input type="text" name="gas_station"
               value="{{ old('gas_station', $fuelLog->gas_station ?? '') }}"
               class="w-full rounded-lg border-gray-300">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium">Notes</label>
        <textarea name="notes" rows="3"
                  class="w-full rounded-lg border-gray-300">{{ old('notes', $fuelLog->notes ?? '') }}</textarea>
    </div>
</div>
