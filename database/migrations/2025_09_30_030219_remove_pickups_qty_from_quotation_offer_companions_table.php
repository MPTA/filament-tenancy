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
            $table->dropColumn('pickups_qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_offer_companions', function (Blueprint $table) {
            $table->integer('pickups_qty')->default(0)->after('companion_type_id');
        });
    }
};
