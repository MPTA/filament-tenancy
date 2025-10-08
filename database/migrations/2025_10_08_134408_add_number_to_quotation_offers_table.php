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
        Schema::table('quotation_offers', function (Blueprint $table) {
            $table->integer('number')->after('quotation_offer_group_id');
            
            // Unique constraint: number must be unique per offer_group and tenant
            $table->unique(['quotation_offer_group_id', 'number', 'tenant_id'], 'unique_offer_number_per_group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_offers', function (Blueprint $table) {
            $table->dropUnique('unique_offer_number_per_group');
            $table->dropColumn('number');
        });
    }
};
