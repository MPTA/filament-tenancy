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
        Schema::table('contacts', function (Blueprint $table) {
            // Add tenant_id field
            $table->string('tenant_id')->nullable()->after('user_id');
            
            // Add foreign key constraint
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
            
            // Add index for performance
            $table->index('tenant_id');
            
            // Add composite index for tenant + user queries
            $table->index(['tenant_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['tenant_id']);
            $table->dropIndex(['tenant_id', 'user_id']);
            
            // Drop foreign key constraint
            $table->dropForeign(['tenant_id']);
            
            // Drop column
            $table->dropColumn('tenant_id');
        });
    }
};
