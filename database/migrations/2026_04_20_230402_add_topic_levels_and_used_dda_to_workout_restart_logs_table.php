<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('workout_restart_logs', function (Blueprint $table) {
            // Add topic_levels column to store topics with their levels
            if (!Schema::hasColumn('workout_restart_logs', 'topic_levels')) {
                $table->json('topic_levels')->nullable()->after('non_dda_difficulty');
            }
            
            // Add used_dda column to track whether DDA was used
            if (!Schema::hasColumn('workout_restart_logs', 'used_dda')) {
                $table->boolean('used_dda')->default(true)->after('topic_levels');
            }
        });
    }

    public function down()
    {
        Schema::table('workout_restart_logs', function (Blueprint $table) {
            if (Schema::hasColumn('workout_restart_logs', 'topic_levels')) {
                $table->dropColumn('topic_levels');
            }
            if (Schema::hasColumn('workout_restart_logs', 'used_dda')) {
                $table->dropColumn('used_dda');
            }
        });
    }
};
