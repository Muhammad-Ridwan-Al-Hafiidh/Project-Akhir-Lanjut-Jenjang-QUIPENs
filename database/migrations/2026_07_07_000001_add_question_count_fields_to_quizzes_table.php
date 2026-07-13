<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            if (!Schema::hasColumn('quizzes', 'easy_questions_count')) {
                $table->unsignedSmallInteger('easy_questions_count')->default(0)->after('random_question');
            }

            if (!Schema::hasColumn('quizzes', 'medium_questions_count')) {
                $table->unsignedSmallInteger('medium_questions_count')->default(0)->after('easy_questions_count');
            }

            if (!Schema::hasColumn('quizzes', 'hard_questions_count')) {
                $table->unsignedSmallInteger('hard_questions_count')->default(0)->after('medium_questions_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            if (Schema::hasColumn('quizzes', 'hard_questions_count')) {
                $table->dropColumn('hard_questions_count');
            }

            if (Schema::hasColumn('quizzes', 'medium_questions_count')) {
                $table->dropColumn('medium_questions_count');
            }

            if (Schema::hasColumn('quizzes', 'easy_questions_count')) {
                $table->dropColumn('easy_questions_count');
            }
        });
    }
};
