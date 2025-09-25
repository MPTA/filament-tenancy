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
            // Add living_city_id field
            $table->uuid('living_city_id')->nullable()->after('room_category_id');
            
            // Add foreign key constraint for living_city_id
            $table->foreign('living_city_id')->references('id')->on('cities')->onDelete('set null');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_offer_companions', function (Blueprint $table) {
            // Remove foreign key constraint and index first
            $table->dropForeign(['living_city_id']);
            $table->dropIndex(['living_city_id']);
            
            // Remove the living_city_id field
            $table->dropColumn('living_city_id');
        });
    }
};
