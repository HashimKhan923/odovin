<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_provider_id')->constrained()->onDelete('cascade');

            // Certification details
            $table->string('name');                         // e.g. "ASE Master Technician"
            $table->string('issuing_body');                 // e.g. "ASE", "EPA", "Manufacturer"
            $table->string('certificate_number')->nullable();
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();         // null = never expires

            // File
            $table->string('file_path');                    // storage path
            $table->string('file_original_name');
            $table->string('file_mime')->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); // bytes

            // Admin review state machine
            // pending → approved | rejected
            $table->string('status')->default('pending');
            $table->text('admin_notes')->nullable();        // rejection reason or note
            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();

            // Badge display
            $table->boolean('show_on_profile')->default(true);

            $table->timestamps();

            $table->index('service_provider_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_certifications');
    }
};