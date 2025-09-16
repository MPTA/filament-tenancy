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
        Schema::create('quotation_offer_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quotation_itinerary_id');
            $table->boolean('is_companion_stay_same_hotel')->default(false);
            $table->uuid('companion_room_category_id')->nullable();
            $table->boolean('is_include_driver_cost')->default(false);
            $table->boolean('is_driver_stay_same_hotel')->default(false);
            $table->uuid('driver_room_category_id')->nullable();
            $table->string('tenant_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('quotation_itinerary_id')->references('id')->on('quotation_itineraries')->onDelete('cascade');
            $table->foreign('companion_room_category_id')->references('id')->on('room_categories')->onDelete('set null');
            $table->foreign('driver_room_category_id')->references('id')->on('room_categories')->onDelete('set null');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            
            // Indexes
            $table->index(['quotation_itinerary_id']);
            $table->index(['tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_offer_groups');
    }
};
