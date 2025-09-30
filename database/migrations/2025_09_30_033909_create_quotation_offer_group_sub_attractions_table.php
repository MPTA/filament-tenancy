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
        Schema::create('quotation_offer_group_sub_attractions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('quotation_offer_group_attraction_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('sub_attraction_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
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
        Schema::dropIfExists('quotation_offer_group_sub_attractions');
    }
};
