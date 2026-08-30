<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            // Use Case Report — Inquiry Form, steps 6/6.1–6.4: the admin enters
            // a single reply message, it's associated with the inquiry, status
            // moves to "replied", and the reply is emailed to the visitor.
            $table->text('reply_message')->nullable()->after('message');
            $table->timestamp('replied_at')->nullable()->after('reply_message');
            $table->foreignId('replied_by')->nullable()->after('replied_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropForeign(['replied_by']);
            $table->dropColumn(['reply_message', 'replied_at', 'replied_by']);
        });
    }
};
