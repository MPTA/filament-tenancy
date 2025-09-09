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
        Schema::create('sub_attractions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->json('name'); // Multi-translatable
            $table->json('description')->nullable(); // Multi-translatable
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->uuid('attraction_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('attraction_id')->references('id')->on('attractions')->onUpdate('cascade')->onDelete('cascade');

            // Indexes
            $table->index('attraction_id');
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_attractions');
    }
};
