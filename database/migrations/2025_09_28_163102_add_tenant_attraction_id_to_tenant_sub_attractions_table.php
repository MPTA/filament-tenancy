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
        Schema::table('tenant_sub_attractions', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_attraction_id')->nullable();
            $table->foreign('tenant_attraction_id')->references('id')->on('tenant_attractions')->onDelete('cascade');
            $table->index('tenant_attraction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_sub_attractions', function (Blueprint $table) {
            $table->dropForeign(['tenant_attraction_id']);
            $table->dropIndex(['tenant_attraction_id']);
            $table->dropColumn('tenant_attraction_id');
        });
    }
};