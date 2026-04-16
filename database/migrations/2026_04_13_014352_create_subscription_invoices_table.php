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
        Schema::create('subscription_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_provider_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('subscription_plans');
            $table->string('stripe_invoice_id')->unique();
            $table->unsignedInteger('amount');          // cents
            $table->string('currency', 3)->default('usd');
            $table->enum('status', ['paid', 'open', 'void', 'uncollectible'])->default('paid');
            $table->string('hosted_invoice_url')->nullable(); // Stripe-hosted PDF link
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->index('service_provider_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_invoices');
    }
};
