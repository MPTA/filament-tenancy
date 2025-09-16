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
        Schema::create('quotation_offer_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quotation_offer_id');
            $table->uuid('room_category_id');
            $table->decimal('per_person_price', 20, 2)->default(0.00);
            $table->string('tenant_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('quotation_offer_id')->references('id')->on('quotation_offers')->onDelete('cascade');
            $table->foreign('room_category_id')->references('id')->on('room_categories')->onDelete('restrict');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            
            // Indexes
            $table->index(['quotation_offer_id']);
            $table->index(['room_category_id']);
            $table->index(['tenant_id']);
            
            // Unique constraint to prevent duplicate room category for same offer
            $table->unique(['quotation_offer_id', 'room_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_offer_prices');
    }
};
