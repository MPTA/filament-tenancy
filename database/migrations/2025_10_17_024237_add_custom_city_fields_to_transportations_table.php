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
        Schema::table('transportations', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['from_city_id']);
            $table->dropForeign(['to_city_id']);
        });

        Schema::table('transportations', function (Blueprint $table) {
            // Make city_id fields nullable
            $table->uuid('from_city_id')->nullable()->change();
            $table->uuid('to_city_id')->nullable()->change();
            
            // Add custom city fields
            $table->string('custom_from_city')->nullable()->after('from_city_id');
            $table->string('custom_to_city')->nullable()->after('to_city_id');
        });

        Schema::table('transportations', function (Blueprint $table) {
            // Re-add foreign keys with nullable support
            $table->foreign('from_city_id')->references('id')->on('cities')->onDelete('restrict');
            $table->foreign('to_city_id')->references('id')->on('cities')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transportations', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['from_city_id']);
            $table->dropForeign(['to_city_id']);
        });

        Schema::table('transportations', function (Blueprint $table) {
            // Remove custom fields
            $table->dropColumn(['custom_from_city', 'custom_to_city']);
            
            // Make city_id fields NOT nullable again
            $table->uuid('from_city_id')->nullable(false)->change();
            $table->uuid('to_city_id')->nullable(false)->change();
        });

        Schema::table('transportations', function (Blueprint $table) {
            // Re-add foreign keys
            $table->foreign('from_city_id')->references('id')->on('cities')->onDelete('restrict');
            $table->foreign('to_city_id')->references('id')->on('cities')->onDelete('restrict');
        });
    }
};
