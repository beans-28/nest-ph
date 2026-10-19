<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// "platform_admin" = the NEST.PH team itself (not a dormitory's admin).
// They review requests from dormitory owners who want NEST.PH.
return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('roles')->where('role_name', 'platform_admin')->exists()) {
            DB::table('roles')->insert(['role_name' => 'platform_admin']);
        }

        Schema::table('partner_requests', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('partner_requests', function (Blueprint $table) {
            $table->dropColumn('notes');
        });

        DB::table('roles')->where('role_name', 'platform_admin')->delete();
    }
};
