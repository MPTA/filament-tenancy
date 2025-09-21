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
            // Add composite index for tenant_id and name for better performance
            $table->index(['tenant_id', 'name'], 'meal_types_tenant_name_index');
            
            // Add index for slug if it exists
            if (Schema::hasColumn('meal_types', 'slug')) {
                $table->index('slug', 'meal_types_slug_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meal_types', function (Blueprint $table) {
            $table->dropIndex('meal_types_tenant_name_index');
            
            if (Schema::hasColumn('meal_types', 'slug')) {
                $table->dropIndex('meal_types_slug_index');
            }
        });
    }
};