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
            // Add unique constraint back only if it doesn't exist
            if (!Schema::hasIndex('tenant_accommodation_prices', 'unique_tenant_accommodation_room_date')) {
                $table->unique(['tenant_id', 'accommodation_id', 'room_category_id', 'valid_from'], 'unique_tenant_accommodation_room_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_accommodation_prices', function (Blueprint $table) {
            // Remove unique constraint
            $table->dropUnique('unique_tenant_accommodation_room_date');
        });
    }
};