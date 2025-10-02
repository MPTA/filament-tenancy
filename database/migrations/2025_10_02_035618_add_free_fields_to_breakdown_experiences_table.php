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
        Schema::table('breakdown_experiences', function (Blueprint $table) {
            $table->boolean('is_free_for_guide')->default(false)->after('price');
            $table->boolean('is_free_for_other_companions')->default(false)->after('is_free_for_guide');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('breakdown_experiences', function (Blueprint $table) {
            $table->dropColumn(['is_free_for_guide', 'is_free_for_other_companions']);
        });
    }
};
