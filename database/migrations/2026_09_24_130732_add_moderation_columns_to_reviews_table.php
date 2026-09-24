<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Review moderation. `status` is the real moderation state;
     * `is_approved` is kept in sync by the Review model so every existing
     * public query (aggregate, breakdown, /dorm-info list) keeps working
     * unchanged.
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->enum('status', ['published', 'hidden', 'removed'])->default('published')->after('is_approved');
            $table->json('flag_reasons')->nullable()->after('status');
            $table->foreignId('moderated_by')->nullable()->after('flag_reasons')->constrained('users')->nullOnDelete();
            $table->timestamp('moderated_at')->nullable()->after('moderated_by');
            $table->string('moderation_note', 500)->nullable()->after('moderated_at');
            $table->index('status');
        });

        DB::table('reviews')->where('is_approved', false)->update(['status' => 'hidden']);
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropConstrainedForeignId('moderated_by');
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'flag_reasons', 'moderated_at', 'moderation_note']);
        });
    }
};