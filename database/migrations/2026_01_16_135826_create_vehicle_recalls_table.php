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
Schema::create('vehicle_recalls', function (Blueprint $table) {
    $table->id();
    $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();

    $table->string('nhtsa_campaign_number')->index();
    $table->string('component')->nullable();
    $table->string('summary', 1000)->nullable();
    $table->text('consequence')->nullable();
    $table->text('remedy')->nullable();
    $table->date('report_received_date')->nullable();

    $table->boolean('is_open')->default(true);
    $table->boolean('is_read')->default(false);

    $table->timestamps();

    $table->unique(['vehicle_id', 'nhtsa_campaign_number']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_recalls');
    }
};
