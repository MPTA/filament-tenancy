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
        Schema::create('quotation_offer_group_companion_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quotation_offer_group_companion_id');
            $table->uuid('from_city_id');
            $table->uuid('to_city_id');
            $table->string('class');
            $table->decimal('price', 20, 2)->default(0);
            $table->string('tenant_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('quotation_offer_group_companion_id')
                  ->references('id')
                  ->on('quotation_offer_group_companions')
                  ->onDelete('cascade');
            
            $table->foreign('from_city_id')
                  ->references('id')
                  ->on('cities')
                  ->onDelete('restrict');
            
            $table->foreign('to_city_id')
                  ->references('id')
                  ->on('cities')
                  ->onDelete('restrict');
            
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->onDelete('cascade');
            
            // Indexes
            $table->index(['quotation_offer_group_companion_id']);
            $table->index(['from_city_id']);
            $table->index(['to_city_id']);
            $table->index(['tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_offer_group_companion_tickets');
    }
};
