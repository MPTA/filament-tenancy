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
            // Check if column exists before attempting to drop
            if (Schema::hasColumn('contacts', 'type')) {
                $table->dropColumn('type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('type')->default('lead')->after('company');
        });
    }
};
