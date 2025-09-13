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
        Schema::create('companion_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->jsonb('name');
            $table->string('tenant_id');
            $table->uuid('native_language_id');
            $table->uuid('speaking_language_id');
            $table->uuid('companion_category_id');
            $table->decimal('per_day_price', 20, 2)->nullable();
            $table->decimal('half_day_price', 20, 2)->nullable();
            $table->decimal('per_hour_price', 20, 2)->nullable();
            $table->integer('max_hour_per_day')->nullable();
            $table->integer('max_hour_half_day')->nullable();
            $table->decimal('extra_hour_price', 20, 2)->nullable();
            $table->uuid('currency_id')->nullable();
            $table->string('slug')->unique();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('native_language_id')->references('id')->on('languages')->onDelete('restrict');
            $table->foreign('speaking_language_id')->references('id')->on('languages')->onDelete('restrict');
            $table->foreign('companion_category_id')->references('id')->on('companion_categories')->onDelete('restrict');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('restrict');

            // Indexes
            $table->index('tenant_id');
            $table->index('slug');
            $table->index('companion_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companion_types');
    }
};
