<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Announcements newsfeed (Tables 43–46). Two tables: announcements
     * (the post itself) and announcement_comments (the thread), mirroring
     * the ticket_replies pattern -- nullable user_id/tenant_id, exactly
     * one set per row, resolves author + bubble style without a lookup.
     */
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('body');
            $table->boolean('comments_restricted')->default(false);
            $table->timestamps();
        });

        Schema::create('announcement_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained('announcements')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->text('body');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_comments');
        Schema::dropIfExists('announcements');
    }
};