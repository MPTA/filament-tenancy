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
        Schema::create('quotation_offer_companions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quotation_offer_group_id');
            $table->uuid('companion_type_id');
            $table->decimal('accommodation_cost', 20, 2)->default(0);
            $table->decimal('ticket_cost', 20, 2)->default(0);
            $table->decimal('experience_cost', 20, 2)->default(0);
            $table->decimal('meal_cost', 20, 2)->default(0);
            $table->decimal('attraction_cost', 20, 2)->default(0);
            $table->decimal('expense_cost', 20, 2)->default(0);
            $table->string('tenant_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('quotation_offer_group_id')->references('id')->on('quotation_offer_groups')->onDelete('cascade');
            $table->foreign('companion_type_id')->references('id')->on('companion_types')->onDelete('restrict');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            
            // Indexes
            $table->index(['quotation_offer_group_id']);
            $table->index(['companion_type_id']);
            $table->index(['tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_offer_companions');
    }
};
