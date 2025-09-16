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
        Schema::create('quotations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('number')->unique();
            $table->uuid('inquiry_id')->nullable();
            $table->string('type');
            $table->uuid('currency_id');
            $table->decimal('exchange_rate', 20, 4)->default(1.0000);
            $table->json('description')->nullable();
            $table->json('internal_note')->nullable();
            $table->uuid('tenant_id');
            $table->uuid('creator_user_id');
            $table->date('expire_date')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('inquiry_id')->references('id')->on('inquiries')->onDelete('set null');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
            $table->foreign('creator_user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index(['tenant_id', 'number']);
            $table->index(['inquiry_id']);
            $table->index(['type']);
            $table->index(['expire_date']);
            $table->index(['creator_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
