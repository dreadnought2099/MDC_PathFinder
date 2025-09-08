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
        Schema::create('paths', function (Blueprint $table) {
            $table->id();

            // Referencing to starting room
            $table->foreignId('from_room_id')
                ->constrained('rooms')
                ->onDelete('cascade');

            // Referencing to destination room
            $table->foreignId('to_room_id')
                ->constrained('rooms')
                ->onDelete('cascade');
                
            $table->softDeletes();
            $table->timestamps();

            // Unique index ensures no duplicates
            $table->unique(['from_room_id', 'to_room_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paths');
    }
};
