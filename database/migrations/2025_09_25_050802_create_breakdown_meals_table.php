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
        Schema::create('breakdown_meals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('breakdown_id');
            $table->string('tenant_id');
            $table->uuid('meal_type_id');
            $table->integer('qty')->default(1);
            $table->decimal('price', 20, 2)->default(0);
            $table->timestamps();

            // Unique constraint for breakdown_id, meal_type_id, and tenant_id
            $table->unique(['breakdown_id', 'meal_type_id', 'tenant_id'], 'breakdown_meal_type_tenant_unique');

            // Indexes for important fields
            $table->index('breakdown_id');
            $table->index('tenant_id');
            $table->index('meal_type_id');
            $table->index(['tenant_id', 'breakdown_id']); // Composite index for tenant-based queries

            // Foreign key constraints
            $table->foreign('breakdown_id')->references('id')->on('breakdowns')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('meal_type_id')->references('id')->on('meal_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breakdown_meals');
    }
};
