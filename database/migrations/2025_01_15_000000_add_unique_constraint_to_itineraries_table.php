<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to handle the index drop safely
        DB::statement('DROP INDEX IF EXISTS itineraries_polymorphic_index');
        
        // Remove duplicate records before adding unique constraint
        DB::statement('
            DELETE FROM itineraries 
            WHERE id NOT IN (
                SELECT DISTINCT ON (itineraryable_id, itineraryable_type, tenant_id) id
                FROM itineraries 
                ORDER BY itineraryable_id, itineraryable_type, tenant_id, created_at ASC
            )
        ');
        
        Schema::table('itineraries', function (Blueprint $table) {
            // Add unique constraint for itineraryable_id, itineraryable_type, and tenant_id
            $table->unique(['itineraryable_id', 'itineraryable_type', 'tenant_id'], 'itineraries_unique_polymorphic_tenant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('itineraries', function (Blueprint $table) {
            // Drop the unique constraint
            $table->dropUnique('itineraries_unique_polymorphic_tenant');
            
            // Add back the original index
            $table->index(['itineraryable_id', 'itineraryable_type'], 'itineraries_polymorphic_index');
        });
    }
};
