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
        Schema::create('quotation_offer_leader_accommodations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quotation_offer_id');
            $table->uuid('accommodation_id');
            $table->uuid('room_category_id');
            $table->uuid('city_id');
            $table->integer('nights')->default(0);
            $table->decimal('night_price', 20, 2)->default(0);
            $table->string('tenant_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('quotation_offer_id')
                  ->references('id')
                  ->on('quotation_offers')
                  ->onDelete('cascade');
            
            $table->foreign('accommodation_id')
                  ->references('id')
                  ->on('accommodations')
                  ->onDelete('restrict');
            
            $table->foreign('room_category_id')
                  ->references('id')
                  ->on('room_categories')
                  ->onDelete('restrict');
            
            $table->foreign('city_id')
                  ->references('id')
                  ->on('cities')
                  ->onDelete('restrict');
            
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->onDelete('cascade');
            
            // Indexes
            $table->index(['quotation_offer_id']);
            $table->index(['accommodation_id']);
            $table->index(['city_id']);
            $table->index(['tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_offer_leader_accommodations');
    }
};
