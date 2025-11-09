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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->integer('rating')->nullable(); // 1-5 stars rating
            $table->string('feedback_type')->default('general'); // general, bug, feature, etc.
            $table->string('page_url')->nullable(); // Where feedback was submitted
            $table->string('ip_hash'); // Hashed IP for rate limiting
            $table->float('recaptcha_score')->nullable(); // reCAPTCHA v3 score (0-1)
            $table->enum('status', ['pending', 'reviewed', 'resolved', 'archived'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
