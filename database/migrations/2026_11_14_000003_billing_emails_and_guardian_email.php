<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Pureza revisions (2026-11-14):
 * - Billing due-date and overdue notices now go out by EMAIL too, not just
 *   SMS. `billing_email_logs` remembers every email sent (one row per bill +
 *   notice + recipient) so the daily job never sends the same one twice.
 * - The guardian (emergency contact) gets the same delinquency notices as
 *   the tenant, so the tenant record now keeps the guardian's email. The
 *   application form already asked for it; it just wasn't copied over.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('emergency_contact_email', 150)->nullable()->after('emergency_contact_number');
        });

        Schema::create('billing_email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('billing_id')->nullable()->constrained('billing_statements')->cascadeOnDelete();
            $table->string('kind', 60);          // e.g. due_soon, grace, overdue_day2, demand_letter
            $table->string('recipient_type', 20); // tenant | guardian
            $table->string('email', 150);
            $table->string('status', 20);         // sent | failed
            $table->text('error')->nullable();
            $table->timestamps();
            $table->index(['billing_id', 'kind', 'recipient_type']);
        });

        // Back-fill guardians' emails from the applications tenants came from.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('UPDATE tenants t JOIN applications a ON a.tenant_id = t.id
                SET t.emergency_contact_email = a.emergency_contact_email
                WHERE t.emergency_contact_email IS NULL AND a.emergency_contact_email IS NOT NULL');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_email_logs');
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('emergency_contact_email');
        });
    }
};
