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
Schema::create('trip_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->date('trip_date');
            $table->integer('start_odometer');
            $table->integer('end_odometer');
            $table->integer('distance');
            $table->enum('purpose', ['business', 'personal', 'commute']);
            $table->string('destination');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('vehicle_id');
            $table->index('trip_date');
            $table->index('purpose');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_logs');
    }
};
