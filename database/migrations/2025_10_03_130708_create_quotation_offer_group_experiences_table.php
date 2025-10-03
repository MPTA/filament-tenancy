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
        Schema::create('quotation_offer_group_experiences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quotation_offer_group_id');
            $table->uuid('experience_id');
            $table->decimal('price', 20, 2)->default(0);
            $table->string('tenant_id');
            $table->timestamps();

            $table->foreign('quotation_offer_group_id')->references('id')->on('quotation_offer_groups')->onDelete('cascade');
            $table->foreign('experience_id')->references('id')->on('experiences')->onDelete('restrict');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_offer_group_experiences');
    }
};
