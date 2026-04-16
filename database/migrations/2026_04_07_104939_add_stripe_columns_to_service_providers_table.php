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
        Schema::table('service_providers', function (Blueprint $table) {
            $table->string('stripe_account_id')->nullable()->after('is_active');
            $table->timestamp('stripe_onboarded_at')->nullable()->after('stripe_account_id');
            $table->boolean('payout_enabled')->default(false)->after('stripe_onboarded_at');
        });
    }

    public function down(): void
    {
        Schema::table('service_providers', function (Blueprint $table) {
            $table->dropColumn(['stripe_account_id', 'stripe_onboarded_at', 'payout_enabled']);
        });
    }
};
