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
        Schema::create('experiences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->jsonb('name');
            $table->jsonb('description')->nullable();
            $table->jsonb('content')->nullable();
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2)->nullable();
            $table->uuid('currency_id')->nullable();
            $table->text('address')->nullable();
            $table->uuid('city_id');
            $table->uuid('district_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('tenant_id');
            $table->uuid('creator_user_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('set null');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('creator_user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes for essential fields only
            $table->index('slug');
            $table->index('city_id');
            $table->index('district_id');
            $table->index('currency_id');
            $table->index('tenant_id');
            $table->index('is_active');
            $table->index(['tenant_id', 'is_active']);
            $table->index(['city_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};
