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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_record_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('category', ['fuel', 'maintenance', 'insurance', 'registration', 'parking', 'toll', 'loan', 'other']);
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->integer('odometer_reading')->nullable();
            $table->string('receipt_file')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('vehicle_id');
            $table->index('category');
            $table->index('expense_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
