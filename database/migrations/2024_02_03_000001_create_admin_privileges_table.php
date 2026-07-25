<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_privileges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('privilege_name', [
                'manage_tenants',
                'manage_rooms',
                'manage_contracts',
                'manage_billing',
                'manage_users',
                'view_reports',
            ]);
            $table->timestamp('granted_at')->useCurrent();

            $table->unique(['user_id', 'privilege_name'], 'uq_user_privilege');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_privileges');
    }
};
