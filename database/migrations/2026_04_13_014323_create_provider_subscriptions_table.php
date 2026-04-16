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
        Schema::create('provider_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_provider_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('subscription_plans');
 
            // Stripe references
            $table->string('stripe_subscription_id')->unique()->nullable(); // sub_xxx
            $table->string('stripe_customer_id')->nullable();               // cus_xxx
            $table->string('billing_interval')->default('monthly');         // monthly | yearly
 
            // State machine
            $table->enum('status', [
                'trialing', 'active', 'past_due', 'canceled', 'incomplete'
            ])->default('active');
 
            // Timestamps for billing lifecycle
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('ends_at')->nullable(); // when access actually stops
 
            // Usage tracking (reset monthly)
            $table->integer('bids_used_this_month')->default(0);
            $table->timestamp('bids_reset_at')->nullable();
 
            $table->timestamps();
 
            $table->index('service_provider_id');
            $table->index('status');
            $table->index('current_period_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_subscriptions');
    }
};
