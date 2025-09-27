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
        Schema::table('breakdown_attractions', function (Blueprint $table) {
            // Drop the existing unique constraint completely
            $table->dropUnique('breakdown_attraction_tenant_unique');
            
            // No new unique constraint - allow multiple records for same attraction
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('breakdown_attractions', function (Blueprint $table) {
            // Restore the original unique constraint
            $table->unique(['breakdown_id', 'attraction_id'], 'breakdown_attraction_tenant_unique');
        });
    }
};