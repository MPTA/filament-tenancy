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
        Schema::create('breakdown_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('breakdown_id');
            $table->string('tenant_id');
            $table->string('transport_mode');
            $table->uuid('from_city_id');
            $table->uuid('to_city_id');
            $table->string('class');
            $table->decimal('price', 20, 2)->default(0);
            $table->timestamps();

            // Unique constraint for from_city_id, to_city_id, tenant_id, and breakdown_id
            $table->unique(['from_city_id', 'to_city_id', 'tenant_id', 'breakdown_id'], 'breakdown_ticket_cities_tenant_unique');

            // Indexes for important fields
            $table->index('breakdown_id');
            $table->index('tenant_id');
            $table->index('transport_mode');
            $table->index('from_city_id');
            $table->index('to_city_id');
            $table->index(['tenant_id', 'breakdown_id']); // Composite index for tenant-based queries

            // Foreign key constraints
            $table->foreign('breakdown_id')->references('id')->on('breakdowns')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('from_city_id')->references('id')->on('cities')->onDelete('restrict');
            $table->foreign('to_city_id')->references('id')->on('cities')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breakdown_tickets');
    }
};
