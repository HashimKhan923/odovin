<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_escrows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->unique()->constrained('service_job_posts')->onDelete('cascade');

            // Stripe references
            $table->string('stripe_payment_intent_id')->unique();
            $table->string('stripe_transfer_id')->nullable();

            // Money stored in CENTS to avoid float precision bugs
            $table->unsignedBigInteger('amount');        // total charged to consumer
            $table->unsignedBigInteger('platform_fee'); // your cut
            $table->string('currency', 3)->default('usd');

            // State machine
            $table->enum('status', ['pending', 'held', 'released', 'refunded', 'disputed'])
                  ->default('pending');

            // Timestamps for each state transition
            $table->timestamp('held_at')->nullable();
            $table->timestamp('release_at')->nullable(); // auto-release deadline
            $table->timestamp('released_at')->nullable();
            $table->timestamp('refunded_at')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('release_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_escrows');
    }
};