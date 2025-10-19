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
        Schema::table('cities', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('code');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('native')->nullable()->after('longitude');
            $table->string('timezone', 50)->nullable()->after('native');
            $table->string('wiki_data_id', 20)->nullable()->after('timezone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn([
                'latitude',
                'longitude',
                'native',
                'timezone',
                'wiki_data_id',
            ]);
        });
    }
};
