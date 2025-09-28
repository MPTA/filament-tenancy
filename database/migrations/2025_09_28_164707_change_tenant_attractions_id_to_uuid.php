<?php

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
        // First, drop the foreign key constraint from tenant_sub_attractions
        Schema::table('tenant_sub_attractions', function (Blueprint $table) {
            $table->dropForeign(['tenant_attraction_id']);
        });

        // Drop the tenant_attraction_id column temporarily
        Schema::table('tenant_sub_attractions', function (Blueprint $table) {
            $table->dropColumn('tenant_attraction_id');
        });

        // Change the id column to UUID in tenant_attractions
        DB::statement('ALTER TABLE tenant_attractions DROP CONSTRAINT tenant_attractions_pkey');
        DB::statement('ALTER TABLE tenant_attractions ALTER COLUMN id DROP DEFAULT');
        DB::statement('ALTER TABLE tenant_attractions ALTER COLUMN id TYPE uuid USING gen_random_uuid()');
        DB::statement('ALTER TABLE tenant_attractions ADD PRIMARY KEY (id)');

        // Add back the tenant_attraction_id column as UUID
        Schema::table('tenant_sub_attractions', function (Blueprint $table) {
            $table->uuid('tenant_attraction_id')->nullable();
            $table->foreign('tenant_attraction_id')->references('id')->on('tenant_attractions')->onDelete('cascade');
            $table->index('tenant_attraction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the foreign key constraint from tenant_sub_attractions
        Schema::table('tenant_sub_attractions', function (Blueprint $table) {
            $table->dropForeign(['tenant_attraction_id']);
        });

        // Change the id column back to bigint
        Schema::table('tenant_attractions', function (Blueprint $table) {
            $table->dropPrimary(['id']);
            $table->bigIncrements('id')->change();
        });

        // Update the foreign key in tenant_sub_attractions to match
        Schema::table('tenant_sub_attractions', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_attraction_id')->change();
            $table->foreign('tenant_attraction_id')->references('id')->on('tenant_attractions')->onDelete('cascade');
        });
    }
};