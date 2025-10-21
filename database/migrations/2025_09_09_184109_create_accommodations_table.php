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
        Schema::create('accommodations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->jsonb('name'); // Multi-translatable
            $table->jsonb('content')->nullable(); // Multi-translatable
            $table->integer('star_rating')->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->uuid('country_id');
            $table->uuid('city_id');
            $table->uuid('district_id')->nullable();
            $table->string('external_id')->nullable();
            $table->string('external_dataset')->nullable();
            $table->jsonb('attractions_data')->nullable();
            $table->jsonb('description')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('website')->nullable();
            $table->jsonb('facilities')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Foreign keys
            $table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('district_id')->references('id')->on('districts')->onUpdate('cascade')->onDelete('set null');

            // Indexes
            $table->index('country_id');
            $table->index('city_id');
            $table->index('district_id');
            $table->index(['latitude', 'longitude']);
            $table->index('star_rating');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accommodations');
    }
};
