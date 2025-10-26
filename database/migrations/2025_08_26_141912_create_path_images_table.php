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
            
            // Composite index for path_id + image_order
            // This speeds up queries like:
            // - WHERE path_id = ? ORDER BY image_order
            // - WHERE path_id = ? AND image_order = ?
            // - MAX(image_order) WHERE path_id = ?
            $table->index(['path_id', 'image_order'], 'path_images_path_order_index');
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
