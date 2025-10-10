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
            $table->uuid('country_id')->nullable()->after('postal_address');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
            $table->index('country_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropIndex(['country_id']);
            $table->dropColumn('country_id');
        });
    }
};
