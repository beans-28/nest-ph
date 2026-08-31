<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Use Case Report — Pay Move-In Fees, precondition: "account
            // status is Pending Move-In Payment and access is restricted to
            // the billing module only." This field didn't exist at all
            // before — there was no way to represent this state.
            $table->enum('status', ['pending_move_in_payment', 'active', 'archived'])
                ->default('pending_move_in_payment')
                ->after('emergency_contact_number');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
