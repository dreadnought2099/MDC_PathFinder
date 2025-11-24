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
            $table->unsignedTinyInteger('rating')->nullable(); // 1-5 stars rating (changed to tinyint)
            $table->string('feedback_type', 50)->default('general'); // general, bug, feature, navigation, other
            $table->string('page_url', 500)->nullable(); // Where feedback was submitted
            $table->string('ip_hash', 64); // Hashed IP for rate limiting (SHA-256)
            $table->decimal('recaptcha_score', 3, 2)->nullable(); // reCAPTCHA v3 score (0.00-1.00)
            $table->enum('status', ['pending', 'reviewed', 'resolved', 'archived'])->default('pending');
            $table->softDeletes();
            $table->timestamps();

            // Indexes for better query performance
            $table->index('status');
            $table->index('feedback_type');
            $table->index('created_at');
            $table->index(['status', 'created_at']); // Composite index for filtered queries
            $table->index('deleted_at');
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
