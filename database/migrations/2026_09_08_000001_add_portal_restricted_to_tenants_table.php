<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Use Case Report Table 25 (Stage 3: Portal Restriction), step 3:
            // "System restricts access to all portal features except the
            // direct payment link." No column existed anywhere to represent
            // this state before Week 6.
            $table->boolean('portal_restricted')->default(false)->after('is_blacklisted');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('portal_restricted');
        });
    }
};
