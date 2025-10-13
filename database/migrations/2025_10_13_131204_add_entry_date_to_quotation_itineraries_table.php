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
        Schema::table('quotation_itineraries', function (Blueprint $table) {
            $table->date('entry_date')->nullable()->after('room_category_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_itineraries', function (Blueprint $table) {
            $table->dropColumn('entry_date');
        });
    }
};
