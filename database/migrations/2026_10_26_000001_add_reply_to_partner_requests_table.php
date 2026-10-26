<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partner_requests', function (Blueprint $table) {
            // The last message the NEST.PH team emailed to the owner.
            $table->text('reply_message')->nullable()->after('notes');
            $table->timestamp('replied_at')->nullable()->after('reply_message');
        });
    }

    public function down(): void
    {
        Schema::table('partner_requests', function (Blueprint $table) {
            $table->dropColumn(['reply_message', 'replied_at']);
        });
    }
};
