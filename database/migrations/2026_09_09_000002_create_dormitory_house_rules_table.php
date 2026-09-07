<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Separate table from dormitory_profile.house_rules (which stays as
        // a long-text fallback used only when no policies PDF has been
        // uploaded -- see 2026_08_26_000004_create_dormitory_profile_table).
        // This table backs the individually addable/editable/deletable
        // rules list shown on the Dormitory Profile admin page and the
        // public listing preview.
        Schema::create('dormitory_house_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_text', 500);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dormitory_house_rules');
    }
};
