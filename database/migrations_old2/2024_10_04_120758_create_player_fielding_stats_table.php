<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerFieldingStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_fielding_stats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('match_id')->unsigned();
            $table->bigInteger('team_id')->unsigned();
            $table->bigInteger('player_id')->nullable();
            $table->unsignedBigInteger('ball_by_ball_id'); // Foreign key to ball_by_ball table
            $table->integer('catches')->nullable();
            $table->integer('run_outs')->nullable();
            $table->integer('stumpings')->nullable();

            // Missing fielding columns
            $table->integer('bowled')->nullable(); // Bowled by the fielder
            $table->integer('direct_hit')->nullable(); // Direct hit run outs
            $table->bigInteger('throwing_end_id')->nullable(); // End of the throw, if applicable
            $table->integer('fielding_caught_behind')->nullable(); // Caught behind (by keeper)
            $table->integer('fielding_caught_and_bowled')->nullable(); // Caught and bowled
            $table->integer('retired_hurt')->nullable(); // Retired hurt
            $table->integer('fielding_mankaded')->nullable(); // Mankaded dismissal
            $table->integer('hit_wicket')->nullable(); // Hit wicket dismissal
            $table->integer('retired_out')->nullable(); // Retired out

            $table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
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
        Schema::dropIfExists('player_fielding_stats');
    }
}




