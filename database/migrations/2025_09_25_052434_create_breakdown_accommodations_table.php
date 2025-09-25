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
        Schema::create('breakdown_accommodations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('breakdown_id');
            $table->string('tenant_id');
            $table->uuid('accommodation_id');
            $table->uuid('city_id');
            $table->integer('nights_qty')->default(0);
            $table->timestamps();

            // Unique constraint for breakdown_id, accommodation_id, and tenant_id
            $table->unique(['breakdown_id', 'accommodation_id', 'tenant_id'], 'breakdown_accommodation_tenant_unique');

            // Indexes for important fields
            $table->index('breakdown_id');
            $table->index('tenant_id');
            $table->index('accommodation_id');
            $table->index(['tenant_id', 'breakdown_id']); // Composite index for tenant-based queries

            // Foreign key constraints
            $table->foreign('breakdown_id')->references('id')->on('breakdowns')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('accommodation_id')->references('id')->on('accommodations')->onDelete('restrict');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breakdown_accommodations');
    }
};
