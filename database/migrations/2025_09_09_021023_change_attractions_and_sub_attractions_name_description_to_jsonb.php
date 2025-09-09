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
        // Change attractions table
        Schema::table('attractions', function (Blueprint $table) {
            $table->jsonb('name')->change();
            $table->jsonb('description')->change();
        });

        // Change sub_attractions table
        Schema::table('sub_attractions', function (Blueprint $table) {
            $table->jsonb('name')->change();
            $table->jsonb('description')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert attractions table
        Schema::table('attractions', function (Blueprint $table) {
            $table->json('name')->change();
            $table->json('description')->change();
        });

        // Revert sub_attractions table
        Schema::table('sub_attractions', function (Blueprint $table) {
            $table->json('name')->change();
            $table->json('description')->change();
        });
    }
};
