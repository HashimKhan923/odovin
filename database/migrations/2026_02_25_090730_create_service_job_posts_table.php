<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_job_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->string('job_number')->unique();
            $table->string('service_type');
            $table->text('description');
            $table->decimal('budget_min', 10, 2)->nullable();
            $table->decimal('budget_max', 10, 2)->nullable();
            $table->string('preferred_date')->nullable();       // e.g. "2026-03-10"
            $table->string('preferred_time')->nullable();       // e.g. "morning / afternoon / any"
            $table->decimal('latitude',  10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('location_address')->nullable();
            $table->integer('radius')->default(25);             // km – how far providers are notified
            $table->enum('status', ['open', 'accepted', 'completed', 'cancelled', 'expired'])->default('open');
            $table->foreignId('accepted_offer_id')->nullable(); // links to winning service_job_offers row
            $table->timestamp('expires_at')->nullable();        // auto-expire after 24 h
            $table->text('customer_notes')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('expires_at');
        });

        Schema::create('service_job_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->constrained('service_job_posts')->onDelete('cascade');
            $table->foreignId('service_provider_id')->constrained()->onDelete('cascade');
            $table->decimal('offered_price', 10, 2);
            $table->string('available_date');                   // "2026-03-10"
            $table->string('available_time');                   // "09:00"
            $table->integer('estimated_duration')->nullable();  // minutes
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();

            $table->index('job_post_id');
            $table->index('service_provider_id');
            $table->index('status');
            $table->unique(['job_post_id', 'service_provider_id']); // one offer per provider per job
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_job_offers');
        Schema::dropIfExists('service_job_posts');
    }
};