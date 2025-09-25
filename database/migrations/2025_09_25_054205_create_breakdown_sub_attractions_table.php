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
        Schema::create('breakdown_sub_attractions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('breakdown_attraction_id');
            $table->string('tenant_id');
            $table->uuid('sub_attraction_id');
            $table->decimal('price', 20, 2)->default(0);
            $table->timestamps();

            // Unique constraint for tenant_id, breakdown_id, subattraction_id, and attraction_id
            // Note: breakdown_id and attraction_id are derived from breakdown_attraction_id
            $table->unique(['breakdown_attraction_id', 'sub_attraction_id', 'tenant_id'], 'breakdown_sub_attraction_tenant_unique');

            // Indexes for important fields
            $table->index('breakdown_attraction_id');
            $table->index('tenant_id');
            $table->index('sub_attraction_id');
            $table->index(['tenant_id', 'breakdown_attraction_id']); // Composite index for tenant-based queries

            // Foreign key constraints
            $table->foreign('breakdown_attraction_id')->references('id')->on('breakdown_attractions')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('sub_attraction_id')->references('id')->on('sub_attractions')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breakdown_sub_attractions');
    }
};
