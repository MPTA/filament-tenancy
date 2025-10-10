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
        Schema::table('tenant_settings', function (Blueprint $table) {
            $table->decimal('driver_meal_base_budget', 10, 2)->default(0)->after('city_id');
            $table->decimal('driver_accommodation_base_budget', 10, 2)->default(0)->after('driver_meal_base_budget');
            $table->decimal('companion_meal_base_budget', 10, 2)->default(0)->after('driver_accommodation_base_budget');
            $table->decimal('companion_accommodation_base_budget', 10, 2)->default(0)->after('companion_meal_base_budget');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_settings', function (Blueprint $table) {
            $table->dropColumn([
                'driver_meal_base_budget',
                'driver_accommodation_base_budget',
                'companion_meal_base_budget',
                'companion_accommodation_base_budget'
            ]);
        });
    }
};
