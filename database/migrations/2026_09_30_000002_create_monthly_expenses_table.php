<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The dorm's own running costs for one month (whole-building Meralco,
 * water and WiFi bills, staff salaries, and anything else). The Financial
 * report subtracts these from the payments collected to show net profit.
 * One row per month: `month` is always the 1st day of that month.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_expenses', function (Blueprint $table) {
            $table->id();
            $table->date('month')->unique();
            $table->decimal('electricity', 10, 2)->default(0);
            $table->decimal('water', 10, 2)->default(0);
            $table->decimal('internet', 10, 2)->default(0);
            $table->decimal('salaries', 10, 2)->default(0);
            $table->decimal('other', 10, 2)->default(0);
            $table->string('other_notes', 255)->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_expenses');
    }
};
