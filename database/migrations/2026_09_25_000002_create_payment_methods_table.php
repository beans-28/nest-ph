<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Admin-managed ways a tenant can pay (GCash, a bank account, cash
        // at reception...). Replaces the hardcoded GCash/BDO/Cash options
        // and the gcash_number/bdo_account_number profile fields, which no
        // screen ever let an admin edit.
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20); // ewallet | bank | cash
            $table->string('name', 60);
            $table->string('account_name', 120)->nullable();
            $table->string('account_number', 60)->nullable();
            $table->string('qr_path')->nullable();
            $table->string('instructions', 500)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Which configured method a tenant paid through, kept as a label
        // snapshot so the record still reads right if the method is later
        // edited or deleted. payment_method stays the coarse enum that
        // reports and filters already group by.
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_method_label', 60)->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('payment_method_label');
        });
        Schema::dropIfExists('payment_methods');
    }
};
