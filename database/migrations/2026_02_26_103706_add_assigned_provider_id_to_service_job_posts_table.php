<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_job_posts', function (Blueprint $table) {
            // When consumer wants to assign a job directly to a specific provider
            $table->foreignId('assigned_provider_id')
                  ->nullable()
                  ->after('accepted_offer_id')
                  ->constrained('service_providers')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('service_job_posts', function (Blueprint $table) {
            $table->dropForeign(['assigned_provider_id']);
            $table->dropColumn('assigned_provider_id');
        });
    }
};