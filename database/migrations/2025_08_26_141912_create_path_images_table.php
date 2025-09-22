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
        Schema::create('path_images', function (Blueprint $table) {
            $table->id();

            // Link to paths table
            $table->foreignId('path_id')
                ->constrained('paths')
                ->onDelete('cascade');

            // Image sequence (1,2,3...)
            $table->integer('image_order');

            // File path (can store relative path or full URL)
            $table->string('image_file');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('path_images');
    }
};
