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
            // Add new fields
            $table->integer('pickups_qty')->default(0)->after('companion_type_id');
            $table->integer('half_days_qty')->default(0)->after('pickups_qty');
            $table->integer('full_days_qty')->default(0)->after('half_days_qty');
            $table->boolean('is_stay_same_hotel')->default(false)->after('full_days_qty');
            $table->uuid('room_category_id')->nullable()->after('is_stay_same_hotel');
            
            // Add foreign key constraint for room_category_id
            $table->foreign('room_category_id')->references('id')->on('room_categories')->onDelete('restrict');
            
            // Add index for room_category_id
            $table->index(['room_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_offer_companions', function (Blueprint $table) {
            // Remove foreign key constraint and index first
            $table->dropForeign(['room_category_id']);
            $table->dropIndex(['room_category_id']);
            
            // Remove the added fields
            $table->dropColumn([
                'pickups_qty',
                'half_days_qty', 
                'full_days_qty',
                'is_stay_same_hotel',
                'room_category_id'
            ]);
        });
    }
};
