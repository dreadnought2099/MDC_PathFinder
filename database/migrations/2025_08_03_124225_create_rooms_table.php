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
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->string('marker_id')->unique()->nullable();
            $table->string('image_path')->nullable();
            $table->string('video_path')->nullable();
            $table->string('office_days')->nullable();      // e.g. "Mon,Tue,Wed"
            $table->time('office_hours_start')->nullable(); // e.g. "09:00:00"
            $table->time('office_hours_end')->nullable();   // e.g. "17:00:00"
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
