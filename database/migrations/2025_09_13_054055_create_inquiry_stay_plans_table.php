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
        Schema::create('inquiry_stay_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('inquiry_itinerary_id');
            $table->uuid('stay_city_id');
            $table->integer('nights');
            $table->integer('accommodation_stars');
            $table->uuid('accommodation_id');
            $table->string('tenant_id');
            $table->timestamps();

            $table->foreign('inquiry_itinerary_id')->references('id')->on('inquiry_itineraries')->onDelete('cascade');
            $table->foreign('stay_city_id')->references('id')->on('cities')->onDelete('restrict');
            $table->foreign('accommodation_id')->references('id')->on('accommodations')->onDelete('restrict');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            $table->index('inquiry_itinerary_id');
            $table->index('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiry_stay_plans');
    }
};