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
        Schema::table('provinces', function (Blueprint $table) {
            $table->string('iso3166_2', 10)->nullable()->after('code');
            $table->string('fips_code', 10)->nullable()->after('iso3166_2');
            $table->string('level', 10)->nullable()->after('fips_code');
            $table->decimal('latitude', 10, 8)->nullable()->after('level');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('timezone', 50)->nullable()->after('longitude');
            $table->string('wiki_data_id', 20)->nullable()->after('timezone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provinces', function (Blueprint $table) {
            $table->dropColumn([
                'iso3166_2',
                'fips_code',
                'level',
                'latitude',
                'longitude',
                'timezone',
                'wiki_data_id',
            ]);
        });
    }
};
