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
            $table->integer('number')->after('quotation_itinerary_id');
            
            // Unique constraint: number must be unique per quotation_itinerary and tenant
            $table->unique(['quotation_itinerary_id', 'number', 'tenant_id'], 'unique_offer_group_number_per_quotation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_offer_groups', function (Blueprint $table) {
            $table->dropUnique('unique_offer_group_number_per_quotation');
            $table->dropColumn('number');
        });
    }
};
