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
        Schema::create('itinerary_day_activity_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('itinerary_day_activity_id');
            $table->uuid('to_city_id');
            $table->string('class');
            $table->string('transport_number')->nullable();
            $table->string('transport_mode');
            $table->string('tenant_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('itinerary_day_activity_id')->references('id')->on('itinerary_day_activities')->onDelete('cascade');
            $table->foreign('to_city_id')->references('id')->on('cities')->onDelete('restrict');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Indexes
            $table->index('itinerary_day_activity_id');
            $table->index('transport_mode');
            $table->index('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_day_activity_tickets');
    }
};
