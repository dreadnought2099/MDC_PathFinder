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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique(false);
            $table->foreignId('room_id')
                ->nullable()
                ->constrained('rooms')
                ->onDelete('set null');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();   // Jr., Sr., II, III, IV
            $table->string('credentials')->nullable(); // MD, RN, CPA, PhD, LPT
            $table->string('position')->nullable();
            $table->text('bio')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('full_name')->nullable()->index(); // For easier searching and sorting
            $table->fullText(['first_name', 'middle_name', 'last_name', 'suffix', 'full_name']);
            $table->unique(['id', 'room_id']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
