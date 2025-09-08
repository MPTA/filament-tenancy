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
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('tenant_id')->nullable()->index();
            $table->uuid('from_currency_id');
            $table->uuid('to_currency_id');
            $table->foreign('from_currency_id')->references('id')->on('currencies')->cascadeOnDelete();
            $table->foreign('to_currency_id')->references('id')->on('currencies')->cascadeOnDelete();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('rate', 10, 4);
            // Add unique constraint to prevent duplicate exchange rates per tenant
            $table->unique(['tenant_id', 'from_currency_id', 'to_currency_id'], 'unique_currency_pair_per_tenant');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
