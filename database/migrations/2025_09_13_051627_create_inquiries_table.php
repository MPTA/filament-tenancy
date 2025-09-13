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
        Schema::create('inquiries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->jsonb('title');
            $table->jsonb('description');
            $table->string('reference')->nullable();
            $table->string('number')->unique();
            $table->string('tenant_id');
            $table->uuid('creator_user_id');
            $table->uuid('contact_id')->nullable();
            $table->jsonb('attachments')->nullable();
            $table->uuid('requested_currency_id')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('creator_user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('set null');
            $table->foreign('requested_currency_id')->references('id')->on('currencies')->onDelete('set null');

            // Indexes
            $table->index('tenant_id');
            $table->index('type');
            $table->index('number');
            $table->index('creator_user_id');
            $table->index('contact_id');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
