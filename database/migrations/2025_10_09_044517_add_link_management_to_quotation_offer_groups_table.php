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
        Schema::table('quotation_offer_groups', function (Blueprint $table) {
            // Lock status - prevents editing when breakdown is incomplete
            $table->boolean('is_locked')->default(false)->after('driver_room_category_id');
            
            // Link status - tracks relationship with breakdown
            // Phase 1: only 'linked' is used
            // Phase 2: 'linked', 'decoupled', 'outdated'
            $table->string('link_status')->default('linked')->after('is_locked');
            
            // Breakdown snapshot - stores breakdown data when decoupled
            // Phase 1: remains null
            // Phase 2: stores breakdown data
            // Using jsonb for better performance and indexing in PostgreSQL
            $table->jsonb('breakdown_snapshot')->nullable()->after('link_status');
            
            // Itinerary snapshot - stores itinerary days data when decoupled
            // Phase 1: remains null
            // Phase 2: stores itinerary days data
            // Using jsonb for better performance and indexing in PostgreSQL
            $table->jsonb('itinerary_snapshot')->nullable()->after('breakdown_snapshot');
            
            // Last sync timestamp - tracks when offer group was last synced with breakdown
            $table->timestamp('last_breakdown_sync_at')->nullable()->after('itinerary_snapshot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_offer_groups', function (Blueprint $table) {
            $table->dropColumn([
                'is_locked',
                'link_status',
                'breakdown_snapshot',
                'itinerary_snapshot',
                'last_breakdown_sync_at',
            ]);
        });
    }
};
