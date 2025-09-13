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
        Schema::create('tenant_attractions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('tenant_id');
            $table->uuid('attraction_id');
            $table->decimal('local_price', 20, 2)->nullable();
            $table->decimal('foreigner_price', 20, 2)->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('attraction_id')->references('id')->on('attractions')->onDelete('restrict');

            $table->index('tenant_id');
            
            // Unique constraint for tenant_id and attraction_id combination
            $table->unique(['tenant_id', 'attraction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_attractions');
    }
};