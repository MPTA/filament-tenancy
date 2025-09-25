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
        Schema::create('breakdown_accommodation_rooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('breakdown_accommodation_id');
            $table->string('tenant_id');
            $table->uuid('room_category_id');
            $table->decimal('price', 20, 2)->default(0);
            $table->timestamps();

            // Unique constraint for breakdown_accommodation_id, room_category_id, and tenant_id
            $table->unique(['breakdown_accommodation_id', 'room_category_id', 'tenant_id'], 'breakdown_accommodation_room_tenant_unique');

            // Indexes for important fields
            $table->index('breakdown_accommodation_id');
            $table->index('tenant_id');
            $table->index('room_category_id');
            $table->index(['tenant_id', 'breakdown_accommodation_id']); // Composite index for tenant-based queries

            // Foreign key constraints
            $table->foreign('breakdown_accommodation_id')->references('id')->on('breakdown_accommodations')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('room_category_id')->references('id')->on('room_categories')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breakdown_accommodation_rooms');
    }
};
