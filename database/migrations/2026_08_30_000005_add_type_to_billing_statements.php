<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_statements', function (Blueprint $table) {
            // Nothing distinguished a one-time move-in fee from a recurring
            // monthly bill before this — inferring it from matching dates
            // would have been fragile. This makes it explicit.
            $table->enum('type', ['move_in', 'monthly'])->default('monthly')->after('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::table('billing_statements', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
