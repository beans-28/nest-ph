<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stores the 5-digit reset code for the "forgot password" flow.
     * Separate from Laravel's built-in password_reset_tokens table (which
     * stores link-based tokens) so the two flows never collide.
     */
    public function up(): void
    {
        Schema::create('password_reset_codes', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('code'); // hashed, never stored in plain text
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_codes');
    }
};