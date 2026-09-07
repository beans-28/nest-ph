<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ticketing module backend (Tables 33, 34, 40, 41).
     *
     * The original maintenance_tickets table (Feb 2024 scaffold) was never
     * used by any model/controller/route -- no real data exists in it --
     * so it's safe to drop and rebuild instead of altering column by
     * column. Changes from the original scaffold:
     *   - category enum replaced with the 13 categories from the actual
     *     admin type-dropdown Figma frame (node 994-5004) -- far more
     *     granular than Table 33's own "Maintenance Request / Concern /
     *     Feedback". Flagged for BAGUI to update the manuscript.
     *   - status enum replaced with the 5 values from the actual admin
     *     status-dropdown Figma frame (node 882-3246): open, seen,
     *     in_progress, resolved, rejected. Also flagged for BAGUI.
     *   - new nullable priority column (Table 40: Urgent / Non-Urgent).
     *   - attachment_url renamed to attachment_path -- it stores a
     *     relative Storage path, not a full URL, matching the _path/_url
     *     convention used everywhere else (e.g. Damage::photo_path).
     *   - submitted_at / no-timestamps setup replaced with standard
     *     created_at/updated_at, matching every other model in the app.
     *   - bed_id kept (nullable) as a snapshot of the tenant's room at the
     *     time the ticket was filed, so it still shows the original room
     *     even if the tenant is moved to a different bed later.
     */
    public function up(): void
    {
        Schema::dropIfExists('maintenance_tickets');

        Schema::create('maintenance_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('bed_id')->nullable()->constrained('beds')->nullOnDelete();
            $table->string('title', 150);
            $table->enum('category', [
                'billing_payment_concern',
                'electrical_issue',
                'plumbing_water_emergency',
                'security_concern',
                'structural_damage',
                'safety_security',
                'fire_safety_hazard',
                'maintenance_repairs',
                'facilities_amenities',
                'administrative_leasing_concern',
                'account_access_issue',
                'noise_roommate_concern',
                'suggestion_feedback',
            ]);
            $table->text('description');
            $table->string('attachment_path')->nullable();
            $table->enum('priority', ['urgent', 'non_urgent'])->nullable();
            $table->enum('status', ['open', 'seen', 'in_progress', 'resolved', 'rejected'])->default('open');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_tickets');
    }
};