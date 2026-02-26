<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_records', function (Blueprint $table) {
            // Link a service record back to the job post that generated it
            $table->foreignId('job_post_id')
                  ->nullable()
                  ->after('service_provider_id')
                  ->constrained('service_job_posts')
                  ->onDelete('set null');

            // mileage_at_service is required in original schema — make nullable for job-based records
            $table->integer('mileage_at_service')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('service_records', function (Blueprint $table) {
            $table->dropForeign(['job_post_id']);
            $table->dropColumn('job_post_id');
        });
    }
};