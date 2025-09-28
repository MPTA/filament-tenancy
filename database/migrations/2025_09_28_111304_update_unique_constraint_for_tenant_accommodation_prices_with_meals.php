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
        Schema::table('tenant_accommodation_prices', function (Blueprint $table) {
            // Drop the old unique constraint
            $table->dropUnique('unique_tenant_accommodation_room_date');
            
            // Add new unique constraint including meal inclusion fields
            $table->unique([
                'tenant_id', 
                'accommodation_id', 
                'room_category_id', 
                'valid_from',
                'is_include_breakfast',
                'is_include_lunch', 
                'is_include_dinner'
            ], 'unique_tenant_accommodation_room_date_meals');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_accommodation_prices', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique('unique_tenant_accommodation_room_date_meals');
            
            // Restore the old unique constraint
            $table->unique(['tenant_id', 'accommodation_id', 'room_category_id', 'valid_from'], 'unique_tenant_accommodation_room_date');
        });
    }
};