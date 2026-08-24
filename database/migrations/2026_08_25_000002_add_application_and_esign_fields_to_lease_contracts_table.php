<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lease_contracts', function (Blueprint $table) {
            $table->foreignId('application_id')->nullable()->after('id')
                ->constrained('applications')->restrictOnDelete();

            $table->enum('esign_status', ['pending', 'signed', 'not_applicable'])
                ->default('pending')->after('monthly_rate');
            $table->string('signed_document_url', 255)->nullable()->after('esign_status');
            $table->timestamp('signed_at')->nullable()->after('signed_document_url');

            $table->foreignId('created_by')->nullable()->after('status')
                ->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->after('created_by')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lease_contracts', function (Blueprint $table) {
            $table->dropForeign(['application_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'application_id',
                'esign_status',
                'signed_document_url',
                'signed_at',
                'created_by',
                'approved_by',
            ]);
        });
    }
};
