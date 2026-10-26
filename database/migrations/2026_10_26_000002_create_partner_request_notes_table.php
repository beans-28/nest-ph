<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per note, so the team keeps a history instead of one
        // text box that gets overwritten.
        Schema::create('partner_request_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('body');
            $table->timestamps();
        });

        // Keep any note already typed into the old single box.
        if (Schema::hasColumn('partner_requests', 'notes')) {
            DB::table('partner_requests')->whereNotNull('notes')->where('notes', '!=', '')
                ->get(['id', 'notes', 'updated_at'])
                ->each(fn ($r) => DB::table('partner_request_notes')->insert([
                    'partner_request_id' => $r->id,
                    'body' => $r->notes,
                    'created_at' => $r->updated_at,
                    'updated_at' => $r->updated_at,
                ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_request_notes');
    }
};
