<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('workout_restart_logs', function (Blueprint $table) {
            $table->string('non_dda_difficulty')->nullable()->after('dda_difficulty');
        });
    }

    public function down()
    {
        Schema::table('workout_restart_logs', function (Blueprint $table) {
            $table->dropColumn('non_dda_difficulty');
        });
    }
};
