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
        Schema::create('quotation_offer_driver_meals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quotation_offer_id');
            $table->uuid('meal_type_id')->nullable();
            $table->integer('qty')->default(0);
            $table->decimal('price', 20, 2)->default(0);
            $table->boolean('is_base_budget')->default(false);
            $table->string('tenant_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('quotation_offer_id')
                  ->references('id')
                  ->on('quotation_offers')
                  ->onDelete('cascade');
            
            $table->foreign('meal_type_id')
                  ->references('id')
                  ->on('meal_types')
                  ->onDelete('restrict');
            
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->onDelete('cascade');
            
            // Indexes
            $table->index(['quotation_offer_id']);
            $table->index(['meal_type_id']);
            $table->index(['tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_offer_driver_meals');
    }
};
