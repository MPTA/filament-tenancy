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
            $table->dropColumn(['base_meal_budget', 'base_accommodation_budget']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companion_types', function (Blueprint $table) {
            $table->decimal('base_meal_budget', 10, 2)->default(0)->after('per_hour_price');
            $table->decimal('base_accommodation_budget', 10, 2)->default(0)->after('base_meal_budget');
        });
    }
};
