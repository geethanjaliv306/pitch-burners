<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBowlersScoreboards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bowlers_scoreboards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id');
            $table->unsignedBigInteger('team_id');
            $table->tinyInteger('inning');
            $table->unsignedBigInteger('bowler_id');
            $table->boolean('is_max_overs_bowled')->default(0);
            $table->decimal('overs_bowled', 4, 1)->default(0.0);
            $table->integer('runs_conceded')->default(0);
            $table->integer('wickets')->default(0);
            $table->integer('maidens')->default(0);
            $table->decimal('economy', 5, 2)->default(0.00);
            $table->timestamps();

            $table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('bowler_id')->references('id')->on('players')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bowlers_scoreboards');
    }
}
