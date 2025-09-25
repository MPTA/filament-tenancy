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
            // Remove the specified fields
            $table->dropColumn(['is_companion_stay_same_hotel', 'companion_room_category_id']);
            
            // Add the new field
            $table->boolean('is_driver_same_meal')->default(false)->after('is_driver_stay_same_hotel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_offer_groups', function (Blueprint $table) {
            // Remove the new field
            $table->dropColumn('is_driver_same_meal');
            
            // Restore the removed fields
            $table->boolean('is_companion_stay_same_hotel')->default(false)->after('quotation_itinerary_id');
            $table->uuid('companion_room_category_id')->nullable()->after('is_companion_stay_same_hotel');
        });
    }
};
