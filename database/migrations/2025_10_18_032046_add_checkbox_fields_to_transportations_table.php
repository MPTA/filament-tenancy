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
            $table->boolean('use_custom_from_city')->default(false)->after('custom_to_city');
            $table->boolean('use_custom_to_city')->default(false)->after('use_custom_from_city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transportations', function (Blueprint $table) {
            $table->dropColumn(['use_custom_from_city', 'use_custom_to_city']);
        });
    }
};