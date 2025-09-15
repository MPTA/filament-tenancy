<?php

use App\Enums\CompanionCategoryEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('companion_categories', function (Blueprint $table) {
            // First add the column as nullable
            $table->string('category_type')->nullable()->after('slug');
        });
        
        // Update existing records with appropriate category types based on their names
        DB::table('companion_categories')->where('slug', 'tour-guide')->update(['category_type' => 'tour_guide']);
        DB::table('companion_categories')->where('slug', 'tour-leader')->update(['category_type' => 'driver']);
        DB::table('companion_categories')->where('slug', 'translator')->update(['category_type' => 'translator']);
        DB::table('companion_categories')->where('slug', 'staff')->update(['category_type' => 'staff']);
        
        // Now make the column NOT NULL
        Schema::table('companion_categories', function (Blueprint $table) {
            $table->string('category_type')->nullable(false)->change();
            
            // Add unique constraint for category_type
            $table->unique('category_type', 'companion_categories_category_type_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companion_categories', function (Blueprint $table) {
            $table->dropUnique('companion_categories_category_type_unique');
            $table->dropColumn('category_type');
        });
    }
};
