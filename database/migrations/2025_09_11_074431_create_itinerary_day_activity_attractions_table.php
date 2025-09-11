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
        Schema::create('itinerary_day_activity_attractions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('itinerary_day_activity_id');
            $table->uuid('attraction_id');
            $table->boolean('is_outview')->default(false);
            $table->string('tenant_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('itinerary_day_activity_id')->references('id')->on('itinerary_day_activities')->onDelete('cascade');
            $table->foreign('attraction_id')->references('id')->on('attractions')->onDelete('restrict');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Indexes
            $table->index('itinerary_day_activity_id');
            $table->index('attraction_id');
            $table->index('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_day_activity_attractions');
    }
};
