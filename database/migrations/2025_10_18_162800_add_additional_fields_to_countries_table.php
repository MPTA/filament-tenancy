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
        Schema::table('countries', function (Blueprint $table) {
            $table->string('iso3', 3)->nullable()->after('code');
            $table->string('numeric_code', 3)->nullable()->after('iso3');
            $table->string('phone_code', 10)->nullable()->after('numeric_code');
            $table->string('capital')->nullable()->after('phone_code');
            $table->string('tld', 10)->nullable()->after('capital');
            $table->string('native_name')->nullable()->after('tld');
            $table->bigInteger('population')->nullable()->after('native_name');
            $table->decimal('gdp', 20, 2)->nullable()->after('population');
            $table->string('nationality')->nullable()->after('gdp');
            $table->jsonb('timezones')->nullable()->after('nationality');
            $table->decimal('latitude', 10, 8)->nullable()->after('timezones');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('emoji', 10)->nullable()->after('longitude');
            $table->string('emoji_u', 50)->nullable()->after('emoji');
            $table->string('wiki_data_id', 20)->nullable()->after('emoji_u');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn([
                'iso3',
                'numeric_code',
                'phone_code',
                'capital',
                'tld',
                'native_name',
                'population',
                'gdp',
                'nationality',
                'timezones',
                'latitude',
                'longitude',
                'emoji',
                'emoji_u',
                'wiki_data_id',
            ]);
        });
    }
};
