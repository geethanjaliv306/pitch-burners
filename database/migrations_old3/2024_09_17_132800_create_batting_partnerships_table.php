<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBattingPartnershipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batting_partnerships', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('match_id')->unsigned();
            $table->bigInteger('team_id')->unsigned();
            $table->bigInteger('player1_id')->unsigned();
            $table->bigInteger('player2_id')->unsigned();
            $table->integer('runs')->nullable();
            $table->integer('balls_faced')->nullable();
            $table->integer('fours')->nullable();
            $table->integer('sixes')->nullable();

            $table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('player1_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('player2_id')->references('id')->on('players')->onDelete('cascade');

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
        Schema::dropIfExists('batting_partnerships');
    }
}
