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
        Schema::create('vehicle_ai_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();

            $table->text('summary')->nullable();
            $table->json('known_issues')->nullable();
            $table->json('maintenance_tips')->nullable();
            $table->json('owner_tips')->nullable();
            $table->json('cost_expectations')->nullable();

            $table->unsignedTinyInteger('peace_of_mind_score')->nullable();
            $table->timestamp('generated_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_a_i_insights');
    }
};
