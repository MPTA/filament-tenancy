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
            $table->decimal('default_meal_budget', 20, 2)->nullable()->after('currency_id');
            $table->decimal('default_accommodation_budget', 20, 2)->nullable()->after('default_meal_budget');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companion_types', function (Blueprint $table) {
            $table->dropColumn(['default_meal_budget', 'default_accommodation_budget']);
        });
    }
};
