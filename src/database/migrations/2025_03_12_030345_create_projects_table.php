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
        Schema::create('projects', function (Blueprint $table) {
            $table->id('project_id');
            $table->string('project_name')->nullable();
            $table->string('project_number')->nullable();
            $table->string('run_id')->nullable();
            $table->string('soil_reporter')->nullable();
            $table->string('soil_report_number')->nullable();
            $table->date('soil_report_date')->nullable();
            $table->string('pile_type');
            $table->string('boring_number')->nullable();
            $table->date('boring_log_date')->nullable();
            $table->integer('termination_depth')->nullable();
            $table->string('project_address')->nullable();
            $table->string('project_city')->nullable();
            $table->string('project_state', 2)->nullable();
            $table->string('project_zip_code')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
