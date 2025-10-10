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
        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('email')->nullable();
            $table->string('company')->nullable();
            $table->string('type')->default('lead');
            $table->string('tenant_id')->nullable();
            $table->uuid('user_id')->nullable();
            $table->boolean('is_customer')->default(false);
            $table->timestamps();

            // Foreign keys
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->unique(['email', 'tenant_id'], 'contacts_email_tenant_unique');
            $table->index('is_customer');
            $table->index(['first_name', 'last_name']);
            $table->index('type', 'contacts_type_index');
            $table->index(['type', 'tenant_id'], 'contacts_type_tenant_index');
            $table->index('tenant_id');
            $table->index(['tenant_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
