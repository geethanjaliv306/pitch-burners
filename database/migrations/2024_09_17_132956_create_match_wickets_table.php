<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchWicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('match_wickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('match_id')->unsigned();
            $table->bigInteger('bowler_id')->unsigned();
            $table->bigInteger('batsman_id')->unsigned();
            $table->string('wicket_type')->nullable();
            $table->bigInteger('fielder_id')->unsigned()->nullable();
            $table->integer('over_number')->nullable();
            $table->integer('ball_number')->nullable();
            $table->integer('runs_conceded')->nullable();
            $table->integer('extras_conceded')->nullable();

            $table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade');
            $table->foreign('bowler_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('batsman_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('fielder_id')->references('id')->on('players')->onDelete('cascade');

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
        Schema::dropIfExists('match_wickets');
    }
}
