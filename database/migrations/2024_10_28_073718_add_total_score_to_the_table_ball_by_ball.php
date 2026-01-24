<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalScoreToTheTableBallByBall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ball_by_ball', function (Blueprint $table) {

            $table->integer('total_score')->after('total_runs')->nullable();
            $table->boolean('is_striker_on_strike')->after('total_runs')->nullable();
            $table->integer('total_wickets')->after('is_striker_on_strike')->nullable();
            $table->string('total_overs', 5)->after('total_wickets')->nullable();
            $table->string('display_run')->after('total_overs')->nullable();

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ball_by_ball', function (Blueprint $table) {

            $table->dropColumn('total_score');
            $table->dropColumn('is_striker_on_strike');
            $table->dropColumn('total_wickets');
            $table->dropColumn('total_overs');
            $table->dropColumn('display_run');
        });
    }
}
