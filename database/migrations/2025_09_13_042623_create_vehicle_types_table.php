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
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vehicle_category_id');
            $table->jsonb('name');
            $table->string('slug')->unique();
            $table->string('cover')->nullable();
            $table->integer('capacity_from');
            $table->integer('capacity_to');
            $table->decimal('per_day_price', 20, 2)->nullable();
            $table->decimal('half_day_price', 20, 2)->nullable();
            $table->integer('max_hour_per_day')->nullable();
            $table->integer('max_hour_half_day')->nullable();
            $table->decimal('extra_hour_price', 20, 2)->nullable();
            $table->boolean('is_vip')->default(false);
            $table->decimal('airport_transfer_price', 20, 2)->nullable();
            $table->string('tenant_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('vehicle_category_id')->references('id')->on('vehicle_categories')->onDelete('restrict');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Indexes
            $table->index('vehicle_category_id');
            $table->index('slug');
            $table->index('tenant_id');
            
            // Unique constraints
            $table->unique(['tenant_id', 'vehicle_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_types');
    }
};
