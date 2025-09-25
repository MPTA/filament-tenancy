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
        Schema::create('breakdowns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quotation_itinerary_id');
            $table->string('tenant_id');
            $table->uuid('creator_user_id');
            $table->uuid('currency_id');
            $table->integer('vehicle_days_qty')->default(0);
            $table->integer('vehicle_half_days_qty')->default(0);
            $table->integer('vehicle_airport_transfers_qty')->default(0);
            $table->integer('vehicle_hours_qty')->default(0);
            $table->integer('vehicle_empty_backs_qty')->default(0);
            $table->decimal('driver_base_meal_budget', 20, 2)->default(0);
            $table->decimal('driver_base_accommodation_budget', 20, 2)->default(0);
            $table->decimal('companion_base_meal_budget', 20, 2)->default(0);
            $table->decimal('companion_base_accommodation_budget', 20, 2)->default(0);
            $table->timestamps();

            // Indexes for important fields
            $table->index('quotation_itinerary_id');
            $table->index('tenant_id');
            $table->index('creator_user_id');
            $table->index(['tenant_id', 'quotation_itinerary_id']); // Composite index for tenant-based queries

            // Foreign key constraints
            $table->foreign('quotation_itinerary_id')->references('id')->on('quotation_itineraries')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('creator_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breakdowns');
    }
};
