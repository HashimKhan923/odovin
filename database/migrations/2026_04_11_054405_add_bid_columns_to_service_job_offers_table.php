<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_job_offers', function (Blueprint $table) {
            // Counter-offer price proposed by consumer
            $table->decimal('counter_price', 10, 2)->nullable()->after('offered_price');
            // Consumer's note with the counter
            $table->text('counter_message')->nullable()->after('counter_price');
            // When the counter was sent
            $table->timestamp('countered_at')->nullable()->after('counter_message');
            // Track negotiation state separately from offer status
            // pending | countered | counter_accepted | counter_rejected
            $table->string('negotiation_status')->default('pending')->after('countered_at');
        });
    }

    public function down(): void
    {
        Schema::table('service_job_offers', function (Blueprint $table) {
            $table->dropColumn(['counter_price', 'counter_message', 'countered_at', 'negotiation_status']);
        });
    }
};