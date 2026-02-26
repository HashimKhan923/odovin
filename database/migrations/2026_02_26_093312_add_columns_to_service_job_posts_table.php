<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_job_posts', function (Blueprint $table) {
            // Work status lifecycle (parallel to booking: confirmed → in_progress → completed/cancelled)
            $table->enum('work_status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])
                  ->default('pending')
                  ->after('status');

            $table->decimal('final_cost', 10, 2)->nullable()->after('work_status');
            $table->text('provider_notes')->nullable()->after('final_cost');
            $table->integer('rating')->nullable()->after('provider_notes');
            $table->text('review')->nullable()->after('rating');
            $table->timestamp('work_started_at')->nullable()->after('review');
            $table->timestamp('work_completed_at')->nullable()->after('work_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('service_job_posts', function (Blueprint $table) {
            $table->dropColumn([
                'work_status', 'final_cost', 'provider_notes',
                'rating', 'review', 'work_started_at', 'work_completed_at',
            ]);
        });
    }
};