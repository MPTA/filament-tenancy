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
            $table->string('companion_hire_mode')->nullable()->after('vehicle_hours');
            $table->integer('companion_hours')->nullable()->after('companion_hire_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('itinerary_days', function (Blueprint $table) {
            $table->dropColumn(['companion_hire_mode', 'companion_hours']);
        });
    }
};
