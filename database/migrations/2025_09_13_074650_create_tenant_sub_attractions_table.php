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
        Schema::create('tenant_sub_attractions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_attraction_id');
            $table->uuid('sub_attraction_id');
            $table->decimal('local_price', 20, 2)->nullable();
            $table->decimal('foreigner_price', 20, 2)->nullable();
            $table->string('tenant_id');
            $table->timestamps();

            $table->foreign('tenant_attraction_id')->references('id')->on('tenant_attractions')->onDelete('cascade');
            $table->foreign('sub_attraction_id')->references('id')->on('sub_attractions')->onDelete('restrict');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            $table->index('tenant_attraction_id');
            $table->index('sub_attraction_id');
            $table->index('tenant_id');
            
            // Unique constraint for tenant_attraction_id, sub_attraction_id, and tenant_id combination
            $table->unique(['tenant_attraction_id', 'sub_attraction_id', 'tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_sub_attractions');
    }
};