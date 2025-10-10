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
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_accommodation_prices', function (Blueprint $table) {
            $table->uuid('currency_id')->nullable()->after('price');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null');
        });
    }
};
