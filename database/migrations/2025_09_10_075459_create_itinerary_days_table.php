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
        Schema::create('itinerary_days', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->jsonb('description')->nullable();
            $table->uuid('itinerary_id');
            $table->integer('day_number');
            $table->uuid('current_city_id');
            $table->uuid('accommodation_city_id')->nullable();
            $table->uuid('accommodation_id')->nullable();
            $table->integer('accommodation_star_rating')->nullable();
            $table->boolean('has_vehicle')->default(false);
            $table->boolean('has_tour_guide')->default(false);
            $table->string('tenant_id');
            $table->uuid('creator_user_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('itinerary_id')->references('id')->on('itineraries')->onDelete('cascade');
            $table->foreign('current_city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreign('accommodation_city_id')->references('id')->on('cities')->onDelete('set null');
            $table->foreign('accommodation_id')->references('id')->on('accommodations')->onDelete('set null');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('creator_user_id')->references('id')->on('users')->onDelete('set null');

            // Indexes for essential fields only
            $table->index('itinerary_id');
            $table->index('current_city_id');
            $table->index('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_days');
    }
};
