<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Table 29 ("Override Delinquency Escalation Stage") lets an
            // admin Pause a tenant's escalation without resetting their
            // stage or clearing history. No column existed to represent
            // "auto-advancement is paused for this tenant" before now.
            // EscalationService::processAll() skips paused tenants.
            $table->boolean('escalation_paused')->default(false)->after('portal_restricted');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('escalation_paused');
        });
    }
};
