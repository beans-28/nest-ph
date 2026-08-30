<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Use Case Report — Apply for Occupancy, step 7.3: the bedspace is
        // tagged Reserved the moment an application is submitted, not left as
        // "vacant" until approval (which is what the schema did before this).
        // Raw ALTER used instead of Laravel's enum ->change() so this doesn't
        // require the doctrine/dbal package.
        DB::statement("ALTER TABLE beds MODIFY status ENUM('vacant','reserved','occupied','maintenance') NOT NULL DEFAULT 'vacant'");

        Schema::table('applications', function (Blueprint $table) {
            // Steps 12–13: reject requires a reason, shown back to the applicant.
            $table->text('rejection_reason')->nullable()->after('status');

            // Steps 14–15: a third outcome distinct from reject — the use case
            // treats "ask them to reapply" as its own path, not a rejection.
            $table->text('re_application_note')->nullable()->after('rejection_reason');
        });

        DB::statement("ALTER TABLE applications MODIFY status ENUM('pending','approved','rejected','re_application_requested','cancelled') NOT NULL DEFAULT 'pending'");

        Schema::table('lease_contracts', function (Blueprint $table) {
            // Week 4 timeline: "Apply Discount button for returning tenants."
            // No discount mechanism existed anywhere in the schema — this is
            // the minimum needed to actually apply one at approval time.
            $table->decimal('discount_amount', 10, 2)->nullable()->after('monthly_rate');
        });
    }

    public function down(): void
    {
        Schema::table('lease_contracts', function (Blueprint $table) {
            $table->dropColumn('discount_amount');
        });

        DB::statement("ALTER TABLE applications MODIFY status ENUM('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending'");

        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['rejection_reason', 're_application_note']);
        });

        DB::statement("ALTER TABLE beds MODIFY status ENUM('vacant','occupied','maintenance') NOT NULL DEFAULT 'vacant'");
    }
};
