<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_job_posts', function (Blueprint $table) {
            // IDs of ServiceDiagnostic records the consumer attaches to the job
            $table->json('attached_diagnostic_ids')->nullable()->after('customer_notes');
            // Provider the consumer prefers based on prior diagnosis
            $table->foreignId('preferred_provider_id')
                  ->nullable()
                  ->constrained('service_providers')
                  ->nullOnDelete()
                  ->after('attached_diagnostic_ids');
        });
    }

    public function down(): void
    {
        Schema::table('service_job_posts', function (Blueprint $table) {
            $table->dropForeign(['preferred_provider_id']);
            $table->dropColumn(['attached_diagnostic_ids', 'preferred_provider_id']);
        });
    }
};