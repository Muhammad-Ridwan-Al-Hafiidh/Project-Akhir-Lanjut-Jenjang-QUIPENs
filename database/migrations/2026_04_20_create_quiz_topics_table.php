<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Buat table quiz_topics (pivot table untuk many-to-many relationship)
        if (!Schema::hasTable('quiz_topics')) {
            Schema::create('quiz_topics', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('quiz_id');
                $table->string('topic'); // Topic name dari questions
                $table->timestamps();

                $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
                $table->unique(['quiz_id', 'topic']); // Prevent duplicate topics for same quiz
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('quiz_topics');
    }
};
