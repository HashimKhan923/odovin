<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add before/after photo arrays to service_records (permanent record)
        Schema::table('service_records', function (Blueprint $table) {
            $table->json('before_photos')->nullable()->after('notes');
            $table->json('after_photos')->nullable()->after('before_photos');
            $table->text('evidence_notes')->nullable()->after('after_photos');
        });
    }

    public function down(): void
    {
        Schema::table('service_records', function (Blueprint $table) {
            $table->dropColumn(['before_photos', 'after_photos', 'evidence_notes', 'job_post_id']);
        });
    }
};