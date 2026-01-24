<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBallPreferencesToPlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('players', function (Blueprint $table) {
            if (!Schema::hasColumn('players', 'ball_preferences')) { 
                $table->string('ball_preferences')->default('All')->after('bowling_style');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('players', function (Blueprint $table) {
            if (Schema::hasColumn('players', 'ball_preferences')) { 
                $table->dropColumn('ball_preferences');
            }
        });
    }
}
