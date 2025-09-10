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
        Schema::create('border_points', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->jsonb('name'); // Border point name (translatable)
            $table->string('code', 3)->nullable(); // IATA code (optional)
            $table->uuid('city_id'); // Foreign key to cities table
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');

            // Indexes for performance
            $table->index('code', 'border_points_iata_code_index');
            $table->index('city_id', 'border_points_city_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('border_points');
    }
};
