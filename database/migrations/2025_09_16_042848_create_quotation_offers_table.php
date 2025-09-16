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
        Schema::create('quotation_offers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quotation_offer_group_id');
            $table->uuid('vehicle_type_id');
            $table->integer('leaders_qty')->default(0);
            $table->uuid('leader_room_category_id')->nullable();
            $table->integer('pax_qty')->default(1);
            $table->integer('drivers_qty')->default(1);
            $table->decimal('markup', 5, 2)->default(0.00);
            $table->string('tenant_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('quotation_offer_group_id')->references('id')->on('quotation_offer_groups')->onDelete('cascade');
            $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types')->onDelete('restrict');
            $table->foreign('leader_room_category_id')->references('id')->on('room_categories')->onDelete('restrict');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            
            // Indexes
            $table->index(['quotation_offer_group_id']);
            $table->index(['tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_offers');
    }
};
