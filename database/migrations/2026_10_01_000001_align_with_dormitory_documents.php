<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Brings the system in line with the dorm's real signed documents (Tenant
 * Agreement, Rules and Regulations, Payments and Fees Schedule):
 *
 * - room_types: each dorm lists its own room types and prices in the
 *   Dormitory Profile (not every dorm has the same kinds of rooms).
 * - dormitory_charges: the "Other Charges" table (lost key, hazardous
 *   item, damage...), used as presets when an admin adds a penalty.
 * - dormitory_profile: the rental policy numbers the documents promise
 *   (due day, grace period, late penalty %, minimum stay, notice periods)
 *   and the Lessor details printed on the agreement. Billing reads these,
 *   so the documents and the system can never disagree.
 * - applications / tenants: the emergency contact's own signature and their
 *   separate consent to billing reminders (Agreement Section 9.3).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('location', 80)->nullable();      // e.g. "Ground floor", "2nd to 5th floor"
            $table->string('description', 255)->nullable();
            $table->string('pricing_mode', 10)->default('per_bed'); // per_bed | per_room
            $table->decimal('monthly_rate', 10, 2)->default(0);
            $table->unsignedSmallInteger('min_capacity')->nullable();
            $table->unsignedSmallInteger('max_capacity')->nullable();
            $table->boolean('has_aircon')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->foreignId('room_type_id')->nullable()->after('floor_id')->constrained('room_types')->nullOnDelete();
        });

        Schema::create('dormitory_charges', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->decimal('amount', 10, 2)->nullable();      // null = no fixed amount
            $table->string('amount_note', 100)->nullable();    // e.g. "Reasonable repair or replacement cost", "per month"
            $table->string('when_applies', 100)->nullable();   // e.g. "Per violation"
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('dormitory_house_rules', function (Blueprint $table) {
            $table->string('section', 80)->nullable()->after('id');
        });

        Schema::table('dormitory_profile', function (Blueprint $table) {
            // Lessor details printed on the Tenant Agreement and letterhead.
            $table->string('representative_name', 150)->nullable();
            $table->string('representative_position', 100)->nullable();
            $table->string('facebook_page_name', 150)->nullable();
            $table->string('facebook_url', 255)->nullable();
            $table->string('website_url', 255)->nullable();

            // Rental policy (Payments and Fees Schedule / Agreement).
            $table->unsignedTinyInteger('rent_due_day')->default(1);
            $table->unsignedTinyInteger('grace_period_days')->default(3);
            $table->decimal('late_penalty_percent', 5, 2)->default(10);
            $table->unsignedTinyInteger('minimum_stay_months')->default(3);
            $table->unsignedSmallInteger('move_out_notice_days')->default(14);
            $table->unsignedSmallInteger('extension_notice_days')->default(30);
            $table->unsignedSmallInteger('deposit_refund_days')->default(21);
            $table->unsignedSmallInteger('reservation_validity_days')->default(30);
            $table->string('mid_month_move_in', 10)->default('full'); // full | prorated
            $table->boolean('water_included')->default(false);
            $table->boolean('electricity_included')->default(false);
            $table->boolean('wifi_included')->default(false);
            $table->decimal('short_term_rate', 10, 2)->nullable();
            $table->decimal('transient_rate', 10, 2)->nullable();

            // Document versions shown on the signed copies.
            $table->string('rules_version', 20)->nullable();
            $table->string('fees_version', 20)->nullable();
            $table->date('documents_effective_date')->nullable();
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->boolean('emergency_contact_signed')->default(false)->after('emergency_contact_relation');
            $table->boolean('emergency_billing_consent')->default(false)->after('emergency_contact_signed');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->boolean('emergency_billing_reminders')->default(false)->after('emergency_contact_number');
        });

        // Penalties gain a 'late_payment' type (automatic 10% late fee). A
        // plain string is used so new types never need another enum change.
        Schema::table('penalties', function (Blueprint $table) {
            $table->string('type', 30)->default('manual')->change();
        });
    }

    public function down(): void
    {
        Schema::table('penalties', function (Blueprint $table) {
            $table->enum('type', ['damage', 'manual', 'other'])->default('manual')->change();
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('emergency_billing_reminders');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['emergency_contact_signed', 'emergency_billing_consent']);
        });

        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->dropColumn([
                'representative_name', 'representative_position', 'facebook_page_name', 'facebook_url', 'website_url',
                'rent_due_day', 'grace_period_days', 'late_penalty_percent', 'minimum_stay_months',
                'move_out_notice_days', 'extension_notice_days', 'deposit_refund_days', 'reservation_validity_days',
                'mid_month_move_in', 'water_included', 'electricity_included', 'wifi_included',
                'short_term_rate', 'transient_rate', 'rules_version', 'fees_version', 'documents_effective_date',
            ]);
        });

        Schema::table('dormitory_house_rules', function (Blueprint $table) {
            $table->dropColumn('section');
        });

        Schema::dropIfExists('dormitory_charges');

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropConstrainedForeignId('room_type_id');
        });

        Schema::dropIfExists('room_types');
    }
};
