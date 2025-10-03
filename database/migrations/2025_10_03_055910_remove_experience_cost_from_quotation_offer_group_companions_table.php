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
        Schema::table('quotation_offer_group_companions', function (Blueprint $table) {
            $table->dropColumn('experience_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_offer_group_companions', function (Blueprint $table) {
            $table->decimal('experience_cost', 10, 2)->default(0)->after('ticket_cost');
        });
    }
};
