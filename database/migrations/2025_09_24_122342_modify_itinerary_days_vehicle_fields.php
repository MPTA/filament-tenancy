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
        Schema::table('itinerary_days', function (Blueprint $table) {
            // Remove the has_vehicle column
            $table->dropColumn('has_vehicle');
            
            // Add new vehicle fields
            $table->string('vehicle_usage_mode')->nullable()->after('accommodation_star_rating');
            $table->integer('vehicle_hours')->nullable()->after('vehicle_usage_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('itinerary_days', function (Blueprint $table) {
            // Remove the new vehicle fields
            $table->dropColumn(['vehicle_usage_mode', 'vehicle_hours']);
            
            // Restore the has_vehicle column
            $table->boolean('has_vehicle')->default(false)->after('accommodation_star_rating');
        });
    }
};
