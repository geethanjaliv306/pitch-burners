<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchSummaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('match_summary', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('match_id')->unsigned();
            $table->bigInteger('winning_team_id')->unsigned();
            $table->bigInteger('losing_team_id')->unsigned();
            $table->string('match_result')->nullable();
            $table->integer('total_runs')->nullable();
            $table->integer('total_wickets')->nullable();
            $table->bigInteger('player_of_the_match')->unsigned();

            $table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade');
            $table->foreign('winning_team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('losing_team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('player_of_the_match')->references('id')->on('players')->onDelete('cascade');

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
        Schema::dropIfExists('match_summary');
    }
}
