<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Requests from other dormitory owners who want NEST.PH for their own dorm
// (sent from the platform landing page at "/").
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_requests', function (Blueprint $table) {
            $table->id();
            $table->string('owner_name');
            $table->string('dorm_name');
            $table->string('email');
            $table->string('contact_number', 30);
            $table->string('city')->nullable();
            $table->unsignedInteger('bed_count')->nullable();
            $table->text('message')->nullable();
            $table->boolean('dpa_consent')->default(false);
            $table->string('status')->default('new');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_requests');
    }
};
