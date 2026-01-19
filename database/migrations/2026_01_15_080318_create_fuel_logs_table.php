<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fuel_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->date('fill_date');
            $table->integer('odometer');
            $table->decimal('gallons', 8, 2);
            $table->decimal('price_per_gallon', 6, 3);
            $table->decimal('total_cost', 8, 2);
            $table->decimal('mpg', 6, 2)->nullable();
            $table->boolean('is_full_tank')->default(true);
            $table->string('gas_station')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('vehicle_id');
            $table->index('fill_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_logs');
    }
};
