<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('workouts', function (Blueprint $table) {
            $table->boolean('used_dda')->default(1)->after('score');
            $table->string('current_dda_difficulty')->nullable()->after('used_dda');
        });
    }

    public function down()
    {
        Schema::table('workouts', function (Blueprint $table) {
            $table->dropColumn(['used_dda', 'current_dda_difficulty']);
        });
    }
};
