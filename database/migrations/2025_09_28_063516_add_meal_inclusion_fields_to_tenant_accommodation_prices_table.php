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
        Schema::table('tenant_accommodation_prices', function (Blueprint $table) {
            $table->boolean('is_include_breakfast')->default(false);
            $table->boolean('is_include_lunch')->default(false);
            $table->boolean('is_include_dinner')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_accommodation_prices', function (Blueprint $table) {
            $table->dropColumn(['is_include_breakfast', 'is_include_lunch', 'is_include_dinner']);
        });
    }
};
