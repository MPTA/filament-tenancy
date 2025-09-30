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
            $table->integer('hours_qty')->default(0)->after('full_days_qty');
            $table->decimal('day_price', 20, 2)->default(0)->after('hours_qty');
            $table->decimal('half_day_price', 20, 2)->default(0)->after('day_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_offer_companions', function (Blueprint $table) {
            $table->dropColumn(['hours_qty', 'day_price', 'half_day_price']);
        });
    }
};
