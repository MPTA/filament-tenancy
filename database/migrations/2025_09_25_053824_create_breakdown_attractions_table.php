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
        Schema::create('breakdown_attractions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('breakdown_id');
            $table->string('tenant_id');
            $table->uuid('city_id');
            $table->uuid('attraction_id');
            $table->boolean('is_outview')->default(false);
            $table->decimal('entry_price', 20, 2)->default(0);
            $table->timestamps();

            // Unique constraint for breakdown_id, attraction_id, and tenant_id
            $table->unique(['breakdown_id', 'attraction_id', 'tenant_id'], 'breakdown_attraction_tenant_unique');

            // Indexes for important fields
            $table->index('breakdown_id');
            $table->index('tenant_id');
            $table->index('attraction_id');
            $table->index(['tenant_id', 'breakdown_id']); // Composite index for tenant-based queries

            // Foreign key constraints
            $table->foreign('breakdown_id')->references('id')->on('breakdowns')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('restrict');
            $table->foreign('attraction_id')->references('id')->on('attractions')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breakdown_attractions');
    }
};
