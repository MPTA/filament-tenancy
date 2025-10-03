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
        Schema::rename('quotation_offer_group_companion_attraction_costs', 'quotation_offer_group_companion_attractions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('quotation_offer_group_companion_attractions', 'quotation_offer_group_companion_attraction_costs');
    }
};
