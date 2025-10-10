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
            if (!Schema::hasColumn('contacts', 'phone')) {
                $table->string('phone', 20)->nullable()->after('email');
            }
            if (!Schema::hasColumn('contacts', 'mobile')) {
                $table->string('mobile', 20)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('contacts', 'country_code')) {
                $table->string('country_code', 10)->nullable()->after('mobile');
            }
            if (!Schema::hasColumn('contacts', 'postal_address')) {
                $table->text('postal_address')->nullable()->after('country_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['phone', 'mobile', 'country_code', 'postal_address']);
        });
    }
};
