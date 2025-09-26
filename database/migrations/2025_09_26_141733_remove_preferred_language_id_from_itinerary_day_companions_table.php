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
        Schema::table('itinerary_day_companions', function (Blueprint $table) {
            $table->dropForeign(['preferred_language_id']);
            $table->dropIndex(['preferred_language_id']);
            $table->dropColumn('preferred_language_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('itinerary_day_companions', function (Blueprint $table) {
            $table->uuid('preferred_language_id')->nullable()->after('companion_category_id');
            $table->foreign('preferred_language_id')->references('id')->on('languages')->onDelete('set null');
            $table->index('preferred_language_id');
        });
    }
};
