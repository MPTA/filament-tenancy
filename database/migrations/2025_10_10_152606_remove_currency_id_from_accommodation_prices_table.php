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
        Schema::table('accommodation_prices', function (Blueprint $table) {
            $table->dropIndex(['currency_id']);
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accommodation_prices', function (Blueprint $table) {
            $table->uuid('currency_id')->after('price');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('restrict');
            $table->index('currency_id');
        });
    }
};
