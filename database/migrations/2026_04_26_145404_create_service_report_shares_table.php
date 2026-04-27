<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_report_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');

            // Unique public token — used in the shareable URL
            $table->string('token', 64)->unique();

            // What to include
            $table->boolean('include_costs')->default(true);
            $table->boolean('include_diagnostics')->default(true);
            $table->boolean('include_provider_details')->default(true);
            $table->boolean('include_photos')->default(true);

            // Optional date filter applied to this share link
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();

            // Optional label for the owner's reference
            $table->string('label')->nullable(); // e.g. "For insurance", "For buyer"

            // Expiry
            $table->timestamp('expires_at')->nullable(); // null = never expires
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamp('last_viewed_at')->nullable();

            $table->timestamps();

            $table->index('token');
            $table->index('vehicle_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_report_shares');
    }
};