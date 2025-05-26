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
        Schema::create('soil_layers', function (Blueprint $table) {
            $table->id('soil_layer_id');
            $table->unsignedBigInteger('soil_profile_id')->nullable();
            $table->foreign('soil_profile_id')->references('soil_profile_id')->on('soil_profiles')->onDelete('cascade');
            $table->float('start_depth');
            $table->float('blow_count')->nullable();
            $table->unsignedBigInteger('soil_layer_type_id')->nullable();
            $table->foreign('soil_layer_type_id')->references('soil_layer_type_id')->on('soil_layer_types')->onDelete('set null');
            $table->float('cohesion')->nullable();
            $table->float('coefficient_of_adhesion')->nullable();
            $table->float('angle_of_internal_friction')->nullable();
            $table->float('coefficient_of_external_friction')->nullable();
            $table->float('moist_unit_weight')->nullable();
            $table->float('saturated_unit_weight')->nullable();
            $table->float('nc')->nullable();
            $table->float('nq')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soil_layers');
    }
};
