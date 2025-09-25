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
        Schema::table('breakdown_sub_attractions', function (Blueprint $table) {
            $table->decimal('local_price', 20, 2)->nullable()->after('price');
            $table->decimal('foreigner_price', 20, 2)->nullable()->after('local_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('breakdown_sub_attractions', function (Blueprint $table) {
            $table->dropColumn(['local_price', 'foreigner_price']);
        });
    }
};
