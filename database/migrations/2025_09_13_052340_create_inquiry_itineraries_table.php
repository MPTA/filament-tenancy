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
        Schema::create('inquiry_itineraries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('inquiry_id');
            $table->string('date_type');
            $table->date('from_date');
            $table->date('to_date');
            $table->integer('accommodation_stars')->nullable();
            $table->string('tenant_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('inquiry_id')->references('id')->on('inquiries')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Indexes
            $table->index('inquiry_id');
            $table->index('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiry_itineraries');
    }
};
