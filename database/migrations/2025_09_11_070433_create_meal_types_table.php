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
        Schema::create('meal_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('meal_category_id');
            $table->jsonb('name');
            $table->jsonb('description')->nullable();
            $table->decimal('budget', 20, 2)->nullable();
            $table->string('tenant_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('meal_category_id')->references('id')->on('meal_categories')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Indexes
            $table->index('meal_category_id');
            $table->index('tenant_id');
            $table->index('budget');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_types');
    }
};
