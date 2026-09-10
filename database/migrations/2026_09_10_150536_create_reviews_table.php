<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Reviews & Ratings module backend (Tables 42, 43).
     *
     * Scope simplification (flagged for BAGUI): Table 43's "Moved Out"
     * trigger and admin approval flow are not wired up yet. For the MVP,
     * any tenant with status 'inactive' can submit one review, and
     * is_approved defaults to true so no moderation queue is needed to
     * show reviews on the homepage. Revisit once the moved-out vs.
     * evicted distinction (see open items) is resolved.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};