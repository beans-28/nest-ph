<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['father_name', 'mother_name']);
            $table->string('emergency_contact_relation', 50)->nullable()->after('emergency_contact_landline');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['emergency_contact_relation']);
            $table->string('father_name', 150)->nullable();
            $table->string('mother_name', 150)->nullable();
        });
    }
};