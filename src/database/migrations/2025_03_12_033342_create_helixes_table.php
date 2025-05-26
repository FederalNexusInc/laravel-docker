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
        Schema::create('helixes', function (Blueprint $table) {
            $table->id('helix_id');
            $table->unsignedBigInteger('anchor_id')->nullable();
            $table->foreign('anchor_id')->references('anchor_id')->on('anchors')->onDelete('cascade');
            $table->text('description');
            $table->float('size')->nullable();
            $table->float('thickness')->nullable();
            $table->float('rating')->nullable();
            $table->integer('helix_count')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('helixes');
    }
};
