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
        Schema::create('itinerary_activity_sub_attractions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('itinerary_day_activity_attraction_id');
            $table->uuid('sub_attraction_id');
            $table->string('tenant_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('itinerary_day_activity_attraction_id')->references('id')->on('itinerary_day_activity_attractions')->onDelete('cascade');
            $table->foreign('sub_attraction_id')->references('id')->on('sub_attractions')->onDelete('restrict');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Indexes
            $table->index('itinerary_day_activity_attraction_id');
            $table->index('sub_attraction_id');
            $table->index('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_activity_sub_attractions');
    }
};
