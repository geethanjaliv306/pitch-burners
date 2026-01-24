<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerBowlingStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_bowling_stats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('match_id')->unsigned();
            $table->bigInteger('team_id')->unsigned();
            $table->bigInteger('player_id')->unsigned();
            $table->unsignedBigInteger('ball_by_ball_id'); // Foreign key to ball_by_ball table
            $table->decimal('overs_bowled', 5, 2)->nullable();
            $table->integer('balls_bowled')->nullable();
            $table->integer('valid_ball_count')->nullable();
            $table->integer('maiden_overs')->nullable();
            $table->integer('runs_conceded')->nullable();
            $table->integer('wickets_taken')->nullable();
            $table->decimal('economy_rate', 5, 2);
            $table->integer('no_balls')->nullable();
            $table->integer('wide_balls')->nullable();
            $table->integer('extras_bowled')->nullable();
            $table->integer('extras_type')->nullable();
            $table->integer('total_overs_bowled')->nullable();
            $table->string('dismissal_type')->nullable();
            $table->integer('extra_runs')->nullable();

            $table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('ball_by_ball_id')->references('id')->on('ball_by_ball')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('player_bowling_stats');
    }
}
