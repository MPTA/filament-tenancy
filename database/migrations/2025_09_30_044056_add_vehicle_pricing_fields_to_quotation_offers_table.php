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
            $table->integer('vehicle_days_qty')->default(0)->after('markup');
            $table->integer('vehicle_half_days_qty')->default(0)->after('vehicle_days_qty');
            $table->integer('vehicle_airport_transfers_qty')->default(0)->after('vehicle_half_days_qty');
            $table->decimal('vehicle_day_price', 20, 2)->default(0)->after('vehicle_airport_transfers_qty');
            $table->decimal('vehicle_half_day_price', 20, 2)->default(0)->after('vehicle_day_price');
            $table->decimal('vehicle_airport_transfer_price', 20, 2)->default(0)->after('vehicle_half_day_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_offers', function (Blueprint $table) {
            $table->dropColumn([
                'vehicle_days_qty',
                'vehicle_half_days_qty',
                'vehicle_airport_transfers_qty',
                'vehicle_day_price',
                'vehicle_half_day_price',
                'vehicle_airport_transfer_price',
            ]);
        });
    }
};
