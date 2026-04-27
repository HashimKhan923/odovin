<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();

            // What's being disputed
            $table->foreignId('job_post_id')->constrained('service_job_posts')->onDelete('cascade');
            $table->foreignId('job_escrow_id')->nullable()->constrained('job_escrows')->onDelete('set null');

            // Who raised it
            $table->foreignId('raised_by_user_id')->constrained('users')->onDelete('cascade');
            $table->enum('raised_by_role', ['consumer', 'provider']);

            // Reference number
            $table->string('reference')->unique(); // DSP-XXXXXXXX

            // Reason
            $table->string('reason_code'); // work_not_done|poor_quality|no_show|overcharged|other
            $table->text('description');

            // State machine
            // open → under_review → resolved_consumer | resolved_provider | resolved_split | closed
            $table->string('status')->default('open');

            // Admin resolution
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->string('resolution')->nullable(); // full_refund|partial_refund|release_to_provider|no_action
            $table->text('resolution_notes')->nullable();
            $table->unsignedBigInteger('resolution_amount')->nullable(); // cents — for partial refund
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();

            // Evidence from both parties (JSON arrays of file paths)
            $table->json('consumer_evidence')->nullable();
            $table->json('provider_evidence')->nullable();

            // Messaging thread count (denormalised for badge)
            $table->unsignedInteger('message_count')->default(0);
            $table->timestamp('last_message_at')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('job_post_id');
            $table->index('raised_by_user_id');
        });

        Schema::create('dispute_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispute_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('sender_role', ['consumer', 'provider', 'admin']);
            $table->text('message');
            $table->json('attachments')->nullable(); // file paths
            $table->boolean('is_internal')->default(false); // admin-only notes
            $table->timestamps();

            $table->index('dispute_id');
        });

        // Add disputed_at to job_escrows
        Schema::table('job_escrows', function (Blueprint $table) {
            $table->timestamp('disputed_at')->nullable()->after('refunded_at');
        });
    }

    public function down(): void
    {
        Schema::table('job_escrows', function (Blueprint $table) {
            $table->dropColumn('disputed_at');
        });
        Schema::dropIfExists('dispute_messages');
        Schema::dropIfExists('disputes');
    }
};