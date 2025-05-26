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
        Schema::create('soil_layer_types', function (Blueprint $table) {
            $table->id('soil_layer_type_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->float('default_cohesion')->nullable();
            $table->float('default_coefficient_of_adhesion')->nullable();
            $table->float('default_angle_of_internal_friction')->nullable();
            $table->float('default_coefficient_of_external_friction')->nullable();
            $table->float('default_moist_unit_weight')->nullable();
            $table->float('default_saturated_unit_weight')->nullable();
            $table->float('default_nc')->nullable();
            $table->float('default_nq')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soil_layer_types');
    }
};
