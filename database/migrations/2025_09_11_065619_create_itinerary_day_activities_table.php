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
        Schema::create('itinerary_day_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('itinerary_day_id');
            $table->uuid('activity_category_id');
            $table->time('start_time');
            $table->time('end_time');
            $table->uuid('city_id');
            $table->jsonb('description')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('itinerary_day_id')->references('id')->on('itinerary_days')->onDelete('cascade');
            $table->foreign('activity_category_id')->references('id')->on('activity_categories')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');

            // Indexes
            $table->index('itinerary_day_id');
            $table->index('activity_category_id');
            $table->index('city_id');
            $table->index(['itinerary_day_id', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_day_activities');
    }
};
