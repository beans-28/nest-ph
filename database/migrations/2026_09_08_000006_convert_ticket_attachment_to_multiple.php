<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tenant Submit Ticket form now supports up to 5 photos per ticket
     * (previously one). Any existing single attachment_path value is
     * preserved by wrapping it into a one-item array before the old
     * column is dropped.
     */
    public function up(): void
    {
        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->json('attachment_paths')->nullable()->after('description');
        });

        DB::table('maintenance_tickets')->whereNotNull('attachment_path')->get()->each(function ($row) {
            DB::table('maintenance_tickets')->where('id', $row->id)->update([
                'attachment_paths' => json_encode([$row->attachment_path]),
            ]);
        });

        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->dropColumn('attachment_path');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->string('attachment_path')->nullable()->after('description');
        });

        DB::table('maintenance_tickets')->whereNotNull('attachment_paths')->get()->each(function ($row) {
            $paths = json_decode($row->attachment_paths, true) ?? [];
            DB::table('maintenance_tickets')->where('id', $row->id)->update([
                'attachment_path' => $paths[0] ?? null,
            ]);
        });

        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->dropColumn('attachment_paths');
        });
    }
};