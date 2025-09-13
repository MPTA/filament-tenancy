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
        Schema::table('itineraries', function (Blueprint $table) {
            // Drop the old polymorphic index
            $table->dropIndex('itineraries_polymorphic_index');
            
            // Drop the old columns
            $table->dropColumn(['itineraryable_id', 'itineraryable_type']);
        });

        Schema::table('itineraries', function (Blueprint $table) {
            // Add the new uuidMorphs columns
            $table->uuidMorphs('itineraryable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('itineraries', function (Blueprint $table) {
            // Drop the uuidMorphs columns
            $table->dropMorphs('itineraryable');
        });

        Schema::table('itineraries', function (Blueprint $table) {
            // Add back the old columns
            $table->uuid('itineraryable_id');
            $table->string('itineraryable_type');
            
            // Add back the old index
            $table->index(['itineraryable_id', 'itineraryable_type'], 'itineraries_polymorphic_index');
        });
    }
};