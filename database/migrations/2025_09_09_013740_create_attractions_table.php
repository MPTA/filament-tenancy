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
        Schema::create('attractions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->json('name'); // Multi-translatable
            $table->json('description')->nullable(); // Multi-translatable
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->uuid('country_id');
            $table->uuid('city_id');
            $table->uuid('district_id')->nullable();
            $table->string('external_id')->nullable();
            $table->string('link')->nullable();
            $table->jsonb('opening_hours')->nullable();
            $table->string('image_url')->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->jsonb('suggested_duration')->nullable();
            $table->jsonb('suggested_season')->nullable();
            $table->jsonb('ticket_info')->nullable();
            $table->jsonb('tips')->nullable();
            $table->jsonb('duration_hours')->nullable();
            $table->decimal('local_price', 10, 2)->nullable();
            $table->decimal('foreigner_price', 10, 2)->nullable();
            $table->boolean('season_spring')->default(false);
            $table->boolean('season_summer')->default(false);
            $table->boolean('season_autumn')->default(false);
            $table->boolean('season_winter')->default(false);
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
            $table->index('rating');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attractions');
    }
};
