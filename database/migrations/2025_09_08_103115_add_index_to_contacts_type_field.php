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
            // Add index for type field for better query performance
            if (!Schema::hasIndex('contacts', 'contacts_type_index')) {
                $table->index('type', 'contacts_type_index');
            }
            
            // Add composite index for type and tenant_id for multi-tenant queries
            if (!Schema::hasIndex('contacts', 'contacts_type_tenant_index')) {
                $table->index(['type', 'tenant_id'], 'contacts_type_tenant_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Drop indexes
            if (Schema::hasIndex('contacts', 'contacts_type_tenant_index')) {
                $table->dropIndex('contacts_type_tenant_index');
            }
            if (Schema::hasIndex('contacts', 'contacts_type_index')) {
                $table->dropIndex('contacts_type_index');
            }
        });
    }
};
