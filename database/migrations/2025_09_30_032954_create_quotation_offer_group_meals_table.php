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
        Schema::create('quotation_offer_group_meals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('quotation_offer_group_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('meal_type_id')->constrained()->cascadeOnDelete();
            $table->integer('qty')->default(0);
            $table->decimal('price', 20, 2)->default(0);
            $table->string('tenant_id');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_offer_group_meals');
    }
};
