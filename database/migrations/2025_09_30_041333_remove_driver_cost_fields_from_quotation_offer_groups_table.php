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
            $table->dropColumn(['driver_meal_cost', 'driver_accommodation_cost']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_offer_groups', function (Blueprint $table) {
            $table->decimal('driver_meal_cost', 20, 2)->default(0)->after('driver_room_category_id');
            $table->decimal('driver_accommodation_cost', 20, 2)->default(0)->after('driver_meal_cost');
        });
    }
};
