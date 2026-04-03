<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_diagnostics', function (Blueprint $table) {
            $table->id();

            // Core relationships
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_record_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('service_provider_id')->nullable()->constrained()->onDelete('set null');

            // Issue details
            $table->string('title');                                          // short label e.g. "Worn brake pads"
            $table->text('description');                                      // detailed explanation
            $table->string('location')->nullable();                           // e.g. "Front left", "Engine bay"
            $table->enum('category', [
                'brakes', 'engine', 'transmission', 'suspension',
                'electrical', 'tires', 'body', 'fluids',
                'cooling', 'exhaust', 'safety', 'other'
            ])->default('other');

            // Severity & urgency
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->boolean('is_safety_critical')->default(false);            // ⚠️ flagged as unsafe to drive

            // Status lifecycle
            $table->enum('status', [
                'open',         // just flagged
                'acknowledged', // consumer has seen it
                'in_progress',  // being fixed
                'resolved',     // fixed
                'ignored',      // consumer chose to ignore
                'monitoring',   // keep an eye on it
            ])->default('open');

            // Cost estimate
            $table->decimal('estimated_cost_min', 10, 2)->nullable();
            $table->decimal('estimated_cost_max', 10, 2)->nullable();

            // Who resolved it and when
            $table->foreignId('resolved_by_provider_id')
                ->nullable()
                ->constrained('service_providers')
                ->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();

            // Status change audit
            $table->foreignId('status_updated_by_provider_id')
                ->nullable()
                ->constrained('service_providers')
                ->onDelete('set null');
            $table->timestamp('status_updated_at')->nullable();
            $table->text('status_notes')->nullable();                        // note when changing status

            $table->timestamps();

            $table->index(['vehicle_id', 'status']);
            $table->index(['service_record_id']);
            $table->index(['severity', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_diagnostics');
    }
};