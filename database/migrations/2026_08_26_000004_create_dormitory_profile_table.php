<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dormitory_profile', function (Blueprint $table) {
            $table->id();
            $table->string('dorm_name', 150);
            $table->text('description')->nullable();
            $table->string('address', 255)->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->string('contact_email', 150)->nullable();
            $table->string('logo_path', 255)->nullable();

            // House rules / policy documents shown on the public "Dorm Info"
            // page. Stored as long text rather than separate tables — these are
            // read-only prose the admin edits occasionally, not structured data
            // anything queries against.
            $table->longText('payments_and_fees')->nullable();
            $table->longText('house_rules')->nullable();
            $table->longText('checkout_procedures')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dormitory_profile');
    }
};
