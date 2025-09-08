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
            // Add type field with enum constraint
            if (!Schema::hasColumn('contacts', 'type')) {
                $table->string('type')->default('Lead')->after('company');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Drop type column
            if (Schema::hasColumn('contacts', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
