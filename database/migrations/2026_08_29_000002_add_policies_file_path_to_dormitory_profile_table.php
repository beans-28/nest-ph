<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            // The actual uploaded PDF (contract/policies/house rules combined
            // into one document, per the "Manage Dormitory Profile" use case).
            // The existing payments_and_fees / house_rules / checkout_procedures
            // text columns stay as-is and now serve as a fallback for the
            // public page when no PDF has been uploaded yet.
            $table->string('policies_file_path', 255)->nullable()->after('logo_path');
        });
    }

    public function down(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->dropColumn('policies_file_path');
        });
    }
};
