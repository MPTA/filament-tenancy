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
        Schema::table('quotation_offer_groups', function (Blueprint $table) {
            // Rename is_include_driver_cost to is_include_driver_meal
            $table->renameColumn('is_include_driver_cost', 'is_include_driver_meal');
            
            // Add new column is_include_driver_hotel
            $table->boolean('is_include_driver_hotel')->default(false)->after('is_include_driver_meal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_offer_groups', function (Blueprint $table) {
            // Drop the new column
            $table->dropColumn('is_include_driver_hotel');
            
            // Rename back to original name
            $table->renameColumn('is_include_driver_meal', 'is_include_driver_cost');
        });
    }
};
