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
Schema::create('insurances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->string('provider');
            $table->string('policy_number');
            $table->string('coverage_type');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('premium_amount', 10, 2);
            $table->enum('payment_frequency', ['monthly', 'quarterly', 'semi-annual', 'annual']);
            $table->decimal('deductible', 10, 2)->nullable();
            $table->text('coverage_limits')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('vehicle_id');
            $table->index('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurances');
    }
};
