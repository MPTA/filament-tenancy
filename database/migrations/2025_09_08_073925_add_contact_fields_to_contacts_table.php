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
        // Check if table exists before attempting to modify
        if (!Schema::hasTable('contacts')) {
            return;
        }
        
        Schema::table('contacts', function (Blueprint $table) {
            // Add missing fields only if they don't exist
            if (!Schema::hasColumn('contacts', 'first_name')) {
                $table->string('first_name')->after('id');
            }
            if (!Schema::hasColumn('contacts', 'last_name')) {
                $table->string('last_name')->after('first_name')->nullable();
            }
            if (!Schema::hasColumn('contacts', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('contacts', 'is_customer')) {
                $table->boolean('is_customer')->default(false)->after('user_id');
            }
            
            // Make user_id nullable if it's not already
            $table->uuid('user_id')->nullable()->change();
            
            // Add indexes for performance only if they don't exist
            if (!Schema::hasIndex('contacts', 'contacts_is_customer_index')) {
                $table->index('is_customer');
            }
            if (!Schema::hasIndex('contacts', 'contacts_first_name_last_name_index')) {
                $table->index(['first_name', 'last_name']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['is_customer']);
            $table->dropIndex(['first_name', 'last_name']);
            
            // Drop columns
            $table->dropColumn(['first_name', 'last_name', 'gender', 'is_customer']);
        });
    }
};
