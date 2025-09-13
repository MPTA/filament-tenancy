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
        Schema::create('transportations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('departure_date')->nullable();
            $table->date('arrival_date')->nullable();
            $table->time('departure_time')->nullable();
            $table->time('arrival_time')->nullable();
            $table->uuid('from_city_id');
            $table->uuid('to_city_id');
            $table->string('transport_number')->nullable();
            $table->string('transport_mode');
            $table->uuid('entry_border_id')->nullable();
            $table->uuid('exit_border_id')->nullable();
            $table->string('departure_airport_terminal')->nullable();
            $table->string('arrival_airport_terminal')->nullable();
            $table->uuidMorphs('transportable');
            $table->string('tenant_id');
            $table->timestamps();

            $table->foreign('from_city_id')->references('id')->on('cities')->onDelete('restrict');
            $table->foreign('to_city_id')->references('id')->on('cities')->onDelete('restrict');
            $table->foreign('entry_border_id')->references('id')->on('border_points')->onDelete('restrict');
            $table->foreign('exit_border_id')->references('id')->on('border_points')->onDelete('restrict');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            $table->index('from_city_id');
            $table->index('to_city_id');
            $table->index('transport_mode');
            $table->index('tenant_id');
            $table->index(['transportable_id', 'transportable_type']);
            $table->index('departure_date');
            $table->index('arrival_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transportations');
    }
};