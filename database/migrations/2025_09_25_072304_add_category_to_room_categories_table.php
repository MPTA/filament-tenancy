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
        Schema::table('room_categories', function (Blueprint $table) {
            $table->string('category')->nullable()->after('slug');
        });

        // Update existing records based on their slug
        \DB::table('room_categories')->update([
            'category' => \DB::raw("
                CASE 
                    WHEN slug = 'single' THEN 'single'
                    WHEN slug = 'double-one' THEN 'double_for_one'
                    WHEN slug = 'double-two' THEN 'double_for_two'
                    WHEN slug = 'suite-one' THEN 'suite_for_one'
                    WHEN slug = 'suite-two' THEN 'suite_for_two'
                    WHEN slug = 'twin' THEN 'twin'
                    WHEN slug = 'triple' THEN 'triple'
                    ELSE 'single'
                END
            ")
        ]);

        // Now make the column NOT NULL and add unique constraint
        Schema::table('room_categories', function (Blueprint $table) {
            $table->string('category')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_categories', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
