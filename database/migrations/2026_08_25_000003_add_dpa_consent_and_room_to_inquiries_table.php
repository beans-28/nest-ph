<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            // Data Privacy Act (RA 10173) consent — required on submission,
            // enforced server-side, not just by the frontend checkbox.
            $table->boolean('dpa_consent')->default(false)->after('preferred_room_type');

            // Optional: inquiry may not target a specific room yet.
            $table->foreignId('room_id')->nullable()->after('email')
                ->constrained('rooms')->nullOnDelete();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropForeign(['room_id']);
            $table->dropColumn(['room_id', 'dpa_consent']);
        });
    }
};
