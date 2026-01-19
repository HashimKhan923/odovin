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
Schema::create('service_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_provider_id')->nullable()->constrained()->onDelete('set null');
            $table->string('service_type');
            $table->text('description');
            $table->date('service_date');
            $table->integer('mileage_at_service');
            $table->decimal('cost', 10, 2);
            $table->string('invoice_number')->nullable();
            $table->string('invoice_file')->nullable();
            $table->json('parts_replaced')->nullable();
            $table->text('notes')->nullable();
            $table->integer('next_service_mileage')->nullable();
            $table->date('next_service_date')->nullable();
            $table->timestamps();
            
            $table->index('vehicle_id');
            $table->index('service_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_records');
    }
};
