<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Personal information
            $table->date('birthdate')->nullable()->after('full_name');
            $table->string('gender', 20)->nullable()->after('birthdate');
            $table->string('nationality', 60)->nullable()->after('gender');
            $table->string('medical_condition', 255)->nullable()->after('nationality');
            $table->string('occupation', 100)->nullable()->after('medical_condition');
            $table->string('school_company', 150)->nullable()->after('occupation');
            $table->string('school_company_address', 255)->nullable()->after('school_company');

            // Contact information
            $table->string('landline', 20)->nullable()->after('email');
            $table->string('home_address', 255)->nullable()->after('landline');

            // Emergency contact — existing emergency_contact_name/number stay,
            // these fill in the rest of what the design collects.
            $table->string('emergency_contact_email', 150)->nullable()->after('emergency_contact_number');
            $table->string('emergency_contact_landline', 20)->nullable()->after('emergency_contact_email');
            $table->string('father_name', 150)->nullable()->after('emergency_contact_landline');
            $table->string('mother_name', 150)->nullable()->after('father_name');

            // Room information
            $table->date('tenant_end_date')->nullable()->after('preferred_start_date');
            $table->string('type_of_tenant', 30)->nullable()->after('tenant_end_date');
            $table->string('id_document_path', 255)->nullable()->after('type_of_tenant');
            $table->string('signed_contract_path', 255)->nullable()->after('id_document_path');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'birthdate',
                'gender',
                'nationality',
                'medical_condition',
                'occupation',
                'school_company',
                'school_company_address',
                'landline',
                'home_address',
                'emergency_contact_email',
                'emergency_contact_landline',
                'father_name',
                'mother_name',
                'tenant_end_date',
                'type_of_tenant',
                'id_document_path',
                'signed_contract_path',
            ]);
        });
    }
};
