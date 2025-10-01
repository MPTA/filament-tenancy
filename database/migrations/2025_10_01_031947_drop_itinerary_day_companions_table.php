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
        Schema::dropIfExists('itinerary_day_companions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('itinerary_day_companions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('itinerary_day_id');
            $table->uuid('companion_category_id');
            $table->string('hire_mode');
            $table->time('from_time')->nullable();
            $table->time('to_time')->nullable();
            $table->string('tenant_id');
            $table->timestamps();

            $table->foreign('itinerary_day_id')->references('id')->on('itinerary_days')->onDelete('cascade');
            $table->foreign('companion_category_id')->references('id')->on('companion_categories')->onDelete('restrict');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            $table->index('itinerary_day_id');
            $table->index('companion_category_id');
            $table->index('hire_mode');
            $table->index('tenant_id');
        });
    }
};
