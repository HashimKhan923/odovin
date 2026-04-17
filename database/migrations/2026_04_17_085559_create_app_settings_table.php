<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('group');        // job_board | escrow | quotes | providers | platform
            $table->string('key')->unique();
            $table->string('label');        // Human readable label for admin UI
            $table->text('value');
            $table->string('type')->default('number'); // number | boolean | text | select
            $table->text('description')->nullable();
            $table->string('unit')->nullable(); // miles, hours, days, %
            $table->timestamps();

            $table->index('group');
        });

        // ── Seed all default values ────────────────────────────────────────
        $settings = [

            // ── Job Board ──────────────────────────────────────────────────
            ['group' => 'job_board', 'key' => 'default_job_radius_miles',    'label' => 'Default Job Radius',           'value' => '25',   'type' => 'number',  'unit' => 'miles',  'description' => 'Default search radius when a consumer posts a job'],
            ['group' => 'job_board', 'key' => 'default_browse_radius_miles', 'label' => 'Default Browse Radius',        'value' => '50',   'type' => 'number',  'unit' => 'miles',  'description' => 'Default radius shown in provider Browse Open Jobs filter'],
            ['group' => 'job_board', 'key' => 'max_radius_miles',            'label' => 'Maximum Allowed Radius',       'value' => '100',  'type' => 'number',  'unit' => 'miles',  'description' => 'Hard cap on any radius input across the platform'],
            ['group' => 'job_board', 'key' => 'job_post_expiry_hours',       'label' => 'Job Post Expiry',              'value' => '24',   'type' => 'number',  'unit' => 'hours',  'description' => 'How long a job post stays open before auto-expiring'],

            // ── Escrow & Payments ──────────────────────────────────────────
            ['group' => 'escrow',    'key' => 'escrow_auto_release_hours',   'label' => 'Escrow Auto-Release Window',   'value' => '72',   'type' => 'number',  'unit' => 'hours',  'description' => 'Hours after provider marks work complete before payment auto-releases if consumer takes no action'],

            // ── Quotes ─────────────────────────────────────────────────────
            ['group' => 'quotes',    'key' => 'quote_expiry_days',           'label' => 'Quote Request Expiry',         'value' => '7',    'type' => 'number',  'unit' => 'days',   'description' => 'Days before an unanswered quote request expires'],
            ['group' => 'quotes',    'key' => 'quote_duplicate_block_days',  'label' => 'Duplicate Quote Block Period', 'value' => '3',    'type' => 'number',  'unit' => 'days',   'description' => 'Block consumer from sending duplicate quote to same provider within this many days'],

            // ── Provider Directory ─────────────────────────────────────────
            ['group' => 'providers', 'key' => 'default_nearby_radius_miles', 'label' => 'Default Nearby Search Radius','value' => '25',   'type' => 'number',  'unit' => 'miles',  'description' => 'Default radius in provider directory and nearby API'],
            ['group' => 'providers', 'key' => 'providers_per_page',          'label' => 'Providers Per Page',           'value' => '12',   'type' => 'number',  'unit' => null,     'description' => 'How many provider cards to show per page in the directory'],

            // ── Platform ───────────────────────────────────────────────────
            ['group' => 'platform',  'key' => 'platform_name',               'label' => 'Platform Name',                'value' => 'Odovin','type' => 'text',   'unit' => null,     'description' => 'Displayed in emails, notifications, and page titles'],
            ['group' => 'platform',  'key' => 'support_email',               'label' => 'Support Email',                'value' => 'support@odovin.com', 'type' => 'text', 'unit' => null, 'description' => 'Email shown to users for support'],
            ['group' => 'platform',  'key' => 'jobs_pagination_size',        'label' => 'Jobs Per Page',                'value' => '15',   'type' => 'number',  'unit' => null,     'description' => 'How many jobs to show per page in lists'],
            ['group' => 'platform',  'key' => 'new_provider_notifications',  'label' => 'Notify Providers of New Jobs', 'value' => '1',    'type' => 'boolean', 'unit' => null,     'description' => 'Send notifications to nearby providers when a new job is posted'],
        ];

        foreach ($settings as $setting) {
            DB::table('app_settings')->insert([
                ...$setting,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};