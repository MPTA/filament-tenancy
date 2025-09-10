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
        Schema::create('itineraries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('itineraryable_id');
            $table->string('itineraryable_type');
            $table->uuid('tenant_id');
            $table->string('travel_mode');
            $table->boolean('is_advanced')->default(false);
            $table->boolean('is_complete')->default(false);
            $table->boolean('is_vip')->default(false);
            $table->uuid('creator_user_id');
            $table->timestamps();

            // Indexes for performance
            $table->index(['itineraryable_id', 'itineraryable_type'], 'itineraries_polymorphic_index');
            $table->index('tenant_id', 'itineraries_tenant_id_index');
            $table->index('travel_mode', 'itineraries_travel_mode_index');
            $table->index('is_advanced', 'itineraries_is_advanced_index');
            $table->index('is_complete', 'itineraries_is_complete_index');
            $table->index('is_vip', 'itineraries_is_vip_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itineraries');
    }
};
