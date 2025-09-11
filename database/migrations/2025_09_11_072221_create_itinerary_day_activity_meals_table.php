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
        Schema::create('itinerary_day_activity_meals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('itinerary_day_activity_id');
            $table->uuid('meal_type_id');
            $table->string('meal_part');
            $table->boolean('is_included_with_hotel')->default(false);
            $table->string('tenant_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('itinerary_day_activity_id')->references('id')->on('itinerary_day_activities')->onDelete('cascade');
            $table->foreign('meal_type_id')->references('id')->on('meal_types')->onDelete('restrict');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Indexes
            $table->index('itinerary_day_activity_id');
            $table->index('meal_type_id');
            $table->index('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_day_activity_meals');
    }
};
