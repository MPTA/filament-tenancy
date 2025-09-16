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
            $table->renameColumn('default_meal_budget', 'base_meal_budget');
            $table->renameColumn('default_accommodation_budget', 'base_accommodation_budget');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companion_types', function (Blueprint $table) {
            $table->renameColumn('base_meal_budget', 'default_meal_budget');
            $table->renameColumn('base_accommodation_budget', 'default_accommodation_budget');
        });
    }
};
