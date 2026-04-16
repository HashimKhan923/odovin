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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();           // basic | pro | premium
            $table->string('name');                     // Basic | Pro | Premium
            $table->text('description')->nullable();
            $table->unsignedInteger('price_monthly');   // cents — e.g. 999 = $9.99
            $table->unsignedInteger('price_yearly');    // cents — discounted annual price
            $table->string('stripe_monthly_price_id')->nullable(); // price_xxx from Stripe
            $table->string('stripe_yearly_price_id')->nullable();
            // Feature limits
            $table->integer('job_bids_per_month')->default(-1); // -1 = unlimited
            $table->decimal('platform_fee_pct', 5, 2)->default(10.00); // %
            $table->boolean('featured_profile')->default(false);
            $table->boolean('priority_in_job_board')->default(false);
            $table->boolean('analytics_advanced')->default(false);
            $table->boolean('badge_verified_boost')->default(false);
            $table->integer('radius_boost_km')->default(0); // extra km added to search radius
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
