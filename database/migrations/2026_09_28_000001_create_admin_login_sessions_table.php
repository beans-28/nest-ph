<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Login tracker on the Admin Privileges page (Owner only). One row per
 * admin sign-in: logged_out_at is filled when they press Log Out, and
 * stays null if the session simply timed out or the browser was closed.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_login_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('session_id', 255)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->dateTime('logged_in_at');
            $table->dateTime('logged_out_at')->nullable();

            $table->index(['user_id', 'logged_in_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_login_sessions');
    }
};
