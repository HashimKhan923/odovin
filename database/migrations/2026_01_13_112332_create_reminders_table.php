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
 Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['maintenance', 'registration', 'insurance', 'inspection', 'payment', 'custom']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date');
            $table->date('reminder_date');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->boolean('is_sent')->default(false);
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
            
            $table->index('vehicle_id');
            $table->index('due_date');
            $table->index('is_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
