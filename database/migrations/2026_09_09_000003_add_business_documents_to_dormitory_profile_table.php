<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            // Use Case Report — Manage Dormitory Profile (Table 39), extend
            // use cases "Upload Business Permit" / "Upload BIR Registration
            // Credentials". Both optional. Uploading the BIR file is what
            // triggers the "BIR Registration Seal/Badge" on the public
            // profile per RMC No. 038-2026 -- no separate boolean flag is
            // needed, presence of bir_registration_path IS the flag.
            $table->string('business_permit_path', 255)->nullable()->after('contract_template_path');
            $table->string('bir_registration_path', 255)->nullable()->after('business_permit_path');
        });
    }

    public function down(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->dropColumn(['business_permit_path', 'bir_registration_path']);
        });
    }
};
