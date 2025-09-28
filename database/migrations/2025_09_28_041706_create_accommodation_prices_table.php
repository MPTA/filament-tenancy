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
        Schema::create('accommodation_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('room_category_id');
            $table->uuid('accommodation_id');
            $table->decimal('price', 20, 2);
            $table->uuid('currency_id');
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('room_category_id')->references('id')->on('room_categories')->onDelete('cascade');
            $table->foreign('accommodation_id')->references('id')->on('accommodations')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('restrict');
            
            // Indexes for better performance
            $table->index(['accommodation_id', 'room_category_id']);
            $table->index(['valid_from', 'valid_to']);
            $table->index('currency_id');
            
            // Unique constraint to prevent duplicate pricing for same accommodation, room category, and date range
            $table->unique(['accommodation_id', 'room_category_id', 'valid_from'], 'unique_accommodation_room_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accommodation_prices');
    }
};