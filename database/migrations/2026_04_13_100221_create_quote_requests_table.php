<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();

            // Who sent it and to whom
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_provider_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('set null');

            // Quote details
            $table->string('reference')->unique();       // QR-XXXXXXXX
            $table->string('service_type');
            $table->text('description');
            $table->string('preferred_date')->nullable();
            $table->string('preferred_time')->nullable();
            $table->decimal('budget_min', 10, 2)->nullable();
            $table->decimal('budget_max', 10, 2)->nullable();
            $table->string('urgency')->default('flexible'); // flexible | this_week | today

            // State machine
            // pending → quoted | declined | expired
            $table->string('status')->default('pending');

            // Provider's response
            $table->decimal('quoted_price', 10, 2)->nullable();
            $table->text('provider_message')->nullable();
            $table->string('estimated_duration')->nullable();  // e.g. "2-3 hours"
            $table->timestamp('responded_at')->nullable();

            // Consumer's follow-up action
            // null | accepted | declined
            $table->string('consumer_action')->nullable();
            $table->timestamp('consumer_action_at')->nullable();

            // If consumer converts to a full job post
            $table->foreignId('converted_job_id')
                ->nullable()
                ->constrained('service_job_posts')
                ->onDelete('set null');

            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('service_provider_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_requests');
    }
};