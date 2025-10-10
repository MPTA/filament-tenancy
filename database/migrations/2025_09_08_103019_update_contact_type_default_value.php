<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        
        // Update existing records to use lowercase values
        DB::table('contacts')->where('type', 'Lead')->update(['type' => 'lead']);
        DB::table('contacts')->where('type', 'User')->update(['type' => 'user']);
        DB::table('contacts')->where('type', 'Customer')->update(['type' => 'customer']);
        
        // Update default value for the column
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('type')->default('lead')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert existing records to uppercase values
        DB::table('contacts')->where('type', 'lead')->update(['type' => 'Lead']);
        DB::table('contacts')->where('type', 'user')->update(['type' => 'User']);
        DB::table('contacts')->where('type', 'customer')->update(['type' => 'Customer']);
        
        // Revert default value for the column
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('type')->default('Lead')->change();
        });
    }
};
