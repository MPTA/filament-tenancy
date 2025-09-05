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
            $table->id();
            $table->unsignedBigInteger('from_currency_id');
            $table->unsignedBigInteger('to_currency_id');
            $table->foreign('from_currency_id')->references('id')->on('public.currencies')->cascadeOnDelete();
            $table->foreign('to_currency_id')->references('id')->on('public.currencies')->cascadeOnDelete();
            $table->decimal('rate', 10, 4);
            // Add unique constraint to prevent duplicate exchange rates
            $table->unique(['from_currency_id', 'to_currency_id'], 'unique_currency_pair');
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
