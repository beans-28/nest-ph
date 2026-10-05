<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Emergency contacts must submit a valid ID along with the applicant's.
 * Stored on the application, then carried over to the tenant on approval.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('emergency_contact_id_path', 255)->nullable()->after('id_document_path');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->string('emergency_contact_id_path')->nullable()->after('id_document_path');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('emergency_contact_id_path');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('emergency_contact_id_path');
        });
    }
};
