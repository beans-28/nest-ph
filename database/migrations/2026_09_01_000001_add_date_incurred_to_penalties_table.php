<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penalties', function (Blueprint $table) {
            // Use Case Report Table 50 ("Add Tenant Penalty"): the manual-add
            // form needs its own date field, separate from created_at, so an
            // admin can record a penalty for an incident that happened a few
            // days before they got around to entering it. Nullable because
            // damage-linked penalties (type='damage') use their Damage
            // record's own date_incurred instead -- this column is only
            // ever set for manually-added penalties.
            $table->date('date_incurred')->nullable()->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('penalties', function (Blueprint $table) {
            $table->dropColumn('date_incurred');
        });
    }
};
