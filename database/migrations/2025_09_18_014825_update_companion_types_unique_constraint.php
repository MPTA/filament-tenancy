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
        Schema::table('companion_types', function (Blueprint $table) {
            // Drop the existing unique constraint
            $table->dropUnique(['tenant_id', 'speaking_language_id', 'native_language_id']);
            
            // Add the new unique constraint including companion_category_id
            $table->unique(['tenant_id', 'companion_category_id', 'native_language_id', 'speaking_language_id'], 'companion_types_tenant_category_languages_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companion_types', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique('companion_types_tenant_category_languages_unique');
            
            // Restore the original unique constraint
            $table->unique(['tenant_id', 'speaking_language_id', 'native_language_id']);
        });
    }
};
