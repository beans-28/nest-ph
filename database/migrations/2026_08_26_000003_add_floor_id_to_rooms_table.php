<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('rooms', 'floor_id')) {
                $table->foreignId('floor_id')->nullable()->after('id')->constrained('floors')->nullOnDelete();
            }
            if (Schema::hasColumn('rooms', 'floor') && Schema::hasColumn('rooms', 'floor_id')) {
                // keep legacy 'floor' for now; migration to remove it can be added later
            }
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (Schema::hasColumn('rooms', 'floor_id')) {
                $table->dropForeign(['floor_id']);
                $table->dropColumn('floor_id');
            }
        });
    }
};
