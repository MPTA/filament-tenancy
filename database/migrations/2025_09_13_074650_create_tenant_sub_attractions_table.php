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
            $table->id();
            $table->string('tenant_id');
            $table->uuid('sub_attraction_id');
            $table->decimal('local_price', 20, 2)->nullable();
            $table->decimal('foreigner_price', 20, 2)->nullable();
            $table->json('additional_content')->nullable();
            $table->uuid('creator_user_id');
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('sub_attraction_id')->references('id')->on('sub_attractions')->onDelete('cascade');
            $table->foreign('creator_user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->unique(['tenant_id', 'sub_attraction_id'], 'unique_tenant_sub_attraction');
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
