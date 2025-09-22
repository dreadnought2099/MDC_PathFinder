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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique(false);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->string('marker_id')->unique()->nullable();
            $table->string('image_path')->nullable();
            $table->string('video_path')->nullable();
            $table->enum('room_type', ['regular', 'entrance_point'])->default('regular');
            $table->unique(['name', 'deleted_at']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
