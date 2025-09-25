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
        Schema::table('quotation_offer_companions', function (Blueprint $table) {
            // Add is_same_meal field
            $table->boolean('is_same_meal')->default(false)->after('is_stay_same_hotel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_offer_companions', function (Blueprint $table) {
            // Remove the is_same_meal field
            $table->dropColumn('is_same_meal');
        });
    }
};
