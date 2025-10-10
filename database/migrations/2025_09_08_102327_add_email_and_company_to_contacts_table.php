<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if table exists before attempting to modify
        if (!Schema::hasTable('contacts')) {
            return;
        }
        
        Schema::table('contacts', function (Blueprint $table) {
            // Add email and company fields
            if (!Schema::hasColumn('contacts', 'email')) {
                $table->string('email')->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('contacts', 'company')) {
                $table->string('company')->nullable()->after('email');
            }
            
            // Add unique constraint for email and tenant_id
            if (!Schema::hasIndex('contacts', 'contacts_email_tenant_unique')) {
                $table->unique(['email', 'tenant_id'], 'contacts_email_tenant_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Drop unique constraint
            if (Schema::hasIndex('contacts', 'contacts_email_tenant_unique')) {
                $table->dropUnique('contacts_email_tenant_unique');
            }
            
            // Drop columns
            if (Schema::hasColumn('contacts', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('contacts', 'company')) {
                $table->dropColumn('company');
            }
        });
    }
};
