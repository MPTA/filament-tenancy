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
        Schema::create('airports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // Airport name
            $table->string('iata_code', 3)->unique(); // IATA 3-letter code (e.g., LAX, JFK)
            $table->uuid('city_id'); // Foreign key to cities table
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');

            // Indexes for performance
            $table->index('iata_code', 'airports_iata_code_index');
            $table->index('city_id', 'airports_city_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airports');
    }
};
