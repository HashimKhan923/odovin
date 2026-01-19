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
Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('vin', 17)->unique();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->integer('year')->nullable();
            $table->string('trim')->nullable();
            $table->string('engine')->nullable();
            $table->string('transmission')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('color')->nullable();
            $table->string('license_plate')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->integer('current_mileage')->default(0);
            $table->json('specifications')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->enum('status', ['active', 'sold', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('user_id');
            $table->index('vin');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
