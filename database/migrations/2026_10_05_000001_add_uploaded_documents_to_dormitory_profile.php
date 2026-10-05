<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            // The owner's own PDF for each tenancy document. When set, it is
            // shown and signed instead of the one NEST.PH generates from the
            // policy fields. Keyed like TenancyDocuments::DOCUMENTS.
            $table->string('agreement_file_path', 255)->nullable();
            $table->string('rules_file_path', 255)->nullable();
            $table->string('fees_file_path', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->dropColumn(['agreement_file_path', 'rules_file_path', 'fees_file_path']);
        });
    }
};
