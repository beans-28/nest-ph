<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Pureza revision (2026-11-14): expenses are now recorded one by one
 * (e.g. "Electric fan for Room 204, P1,850" with a photo of the receipt)
 * instead of one row per month with fixed columns.
 *
 *   expenses            one row per expense, with an optional receipt file.
 *   recurring_expenses  expenses that repeat (e.g. staff salaries every
 *                       payday). A daily job (expenses:post-recurring)
 *                       records each one in `expenses` when it is due.
 *
 * The old monthly_expenses rows are copied over (one expense per non-zero
 * column, dated the 1st of that month), then the old table is dropped.
 */
return new class extends Migration
{
    private const OLD_COLUMNS = [
        'electricity' => 'Electricity bill (Meralco)',
        'water' => 'Water bill',
        'internet' => 'Internet / WiFi bill',
        'salaries' => 'Staff salaries',
        'other' => 'Other expenses',
    ];

    public function up(): void
    {
        Schema::create('recurring_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('category', 30);
            $table->string('description', 255);
            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 20)->default('cash');
            // monthly = same day every month; payday = every 15th and last day of the month
            $table->string('frequency', 20);
            // Monthly only: the day it falls on (31 = last day in shorter months)
            $table->unsignedTinyInteger('day_of_month')->nullable();
            $table->date('next_date');
            $table->boolean('active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('category', 30);
            $table->string('description', 255);
            $table->decimal('amount', 10, 2);
            $table->date('date_paid');
            $table->string('payment_method', 20)->default('cash');
            $table->string('receipt_path')->nullable();
            $table->foreignId('recurring_expense_id')->nullable()->constrained('recurring_expenses')->nullOnDelete();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('date_paid');
        });

        if (Schema::hasTable('monthly_expenses')) {
            foreach (DB::table('monthly_expenses')->orderBy('month')->get() as $old) {
                foreach (self::OLD_COLUMNS as $column => $label) {
                    if ((float) $old->$column <= 0) {
                        continue;
                    }
                    DB::table('expenses')->insert([
                        'category' => $column,
                        'description' => $column === 'other' && $old->other_notes ? $old->other_notes : $label,
                        'amount' => $old->$column,
                        'date_paid' => $old->month,
                        'payment_method' => 'cash',
                        'recorded_by' => $old->recorded_by,
                        'created_at' => $old->created_at,
                        'updated_at' => $old->updated_at,
                    ]);
                }
            }
            Schema::drop('monthly_expenses');
        }
    }

    public function down(): void
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

        // Squash the itemized rows back into one row per month. Categories
        // the old table didn't have go into "other".
        $months = [];
        foreach (DB::table('expenses')->get() as $e) {
            $month = substr($e->date_paid, 0, 7) . '-01';
            $col = array_key_exists($e->category, self::OLD_COLUMNS) ? $e->category : 'other';
            $months[$month] ??= ['month' => $month, 'electricity' => 0, 'water' => 0, 'internet' => 0, 'salaries' => 0, 'other' => 0];
            $months[$month][$col] += (float) $e->amount;
        }
        foreach ($months as $row) {
            DB::table('monthly_expenses')->insert($row + ['created_at' => now(), 'updated_at' => now()]);
        }

        Schema::dropIfExists('expenses');
        Schema::dropIfExists('recurring_expenses');
    }
};
