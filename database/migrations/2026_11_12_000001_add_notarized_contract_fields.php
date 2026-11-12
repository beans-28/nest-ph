<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Optional notarized contract (adviser recommendation).
 *
 * Notarization is not needed for a lease to be valid (Civil Code Arts.
 * 1318 and 1356), so it is OFF by default. A dorm that requires it turns
 * `notarization_required` on in its Rental Policy, and from then on every
 * contract must have its notarized copy uploaded.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->boolean('notarization_required')->default(false);
        });

        Schema::table('lease_contracts', function (Blueprint $table) {
            // Stored on the PRIVATE disk: a notarized contract carries
            // personal data (Data Privacy Act), so it is never a public URL.
            $table->string('notarized_document_path', 255)->nullable()->after('signed_at');
            $table->timestamp('notarized_at')->nullable()->after('notarized_document_path');
        });
    }

    public function down(): void
    {
        Schema::table('lease_contracts', function (Blueprint $table) {
            $table->dropColumn(['notarized_document_path', 'notarized_at']);
        });

        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->dropColumn('notarization_required');
        });
    }
};
