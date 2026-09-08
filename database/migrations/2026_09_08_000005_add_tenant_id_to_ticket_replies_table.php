<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The approved Figma frame for the tenant tracking page (node
     * 524-6001) gives each ticket its own "Add a reply" box on the
     * tenant's side too, not just the admin's -- so a reply needs to be
     * authored by either a user (admin) or a tenant.
     */
    public function up(): void
    {
        Schema::table('ticket_replies', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('user_id')->constrained('tenants')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_replies', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
        });
    }
};