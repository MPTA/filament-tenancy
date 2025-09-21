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
        Schema::table('meal_types', function (Blueprint $table) {
            // Drop the existing unique constraint on slug
            $table->dropUnique(['slug']);
            
            // Add unique constraint on slug + tenant_id combination
            $table->unique(['slug', 'tenant_id'], 'meal_types_slug_tenant_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meal_types', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('meal_types_slug_tenant_unique');
            
            // Restore the original unique constraint on slug only
            $table->unique('slug');
        });
    }
};