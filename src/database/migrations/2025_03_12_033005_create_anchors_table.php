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
        Schema::create('anchors', function (Blueprint $table) {
            $table->id('anchor_id');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->foreign('project_id')->references('project_id')->on('projects')->onDelete('cascade');
            $table->string('lead_shaft_od')->nullable();
            $table->float('lead_shaft_length')->nullable();
            $table->string('extension_shaft_od')->nullable();
            $table->float('wall_thickness')->nullable();
            $table->float('yield_strength')->nullable();
            $table->float('tensile_strength')->nullable();
            $table->float('empirical_torque_factor')->nullable();
            $table->float('required_allowable_capacity')->nullable();
            $table->integer('anchor_type')->nullable()->default(1);
            $table->float('required_safety_factor')->nullable();
            $table->float('anchor_declination_degree')->nullable();
            $table->float('pile_head_position')->nullable();
            $table->float('x1')->default(0);
            $table->float('y1')->default(0);
            $table->float('x2')->default(0);
            $table->float('y2')->default(0);
            $table->float('x3')->default(0);
            $table->float('y3')->default(0);
            $table->float('x4')->default(0);
            $table->float('y4')->default(0);
            $table->float('x5')->default(0);
            $table->float('y5')->default(0);
            $table->boolean('omit_shaft_resistance')->default(false);
            $table->boolean('omit_helix_mechanical_strength_check')->default(false);
            $table->boolean('omit_shaft_mechanical_strength_check')->default(false);
            $table->text('field_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anchors');
    }
};
