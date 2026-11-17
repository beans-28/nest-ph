<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Which admin bell items each staff member has already seen. Items are
 * built live from waiting work (AdminNotificationController), so we only
 * store "user X has read item Y" (e.g. "application:12").
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_notification_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('item_key', 80);
            $table->timestamp('read_at')->useCurrent();
            $table->unique(['user_id', 'item_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_notification_reads');
    }
};
