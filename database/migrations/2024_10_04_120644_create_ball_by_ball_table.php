<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBallByBallTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ball_by_ball', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('match_id')->unsigned();
            $table->unsignedBigInteger('batting_team_id');
            $table->unsignedBigInteger('bowling_team_id');
            $table->bigInteger('bowler_id')->unsigned()->nullable();
            $table->bigInteger('striker_id')->unsigned()->nullable();
            $table->bigInteger('non_striker_id')->unsigned()->nullable();
            $table->bigInteger('fielder_id')->unsigned()->nullable();
            $table->integer('over_number');
            $table->integer('ball_number');
            $table->integer('valid_ball_count');
            $table->integer('is_one')->nullable();
            $table->integer('is_two')->nullable();
            $table->integer('is_three')->nullable();
            $table->integer('is_four')->nullable();
            $table->integer('is_five')->nullable();
            $table->integer('is_six')->nullable();
            $table->integer('other_runs')->nullable();
            $table->integer('bye_runs')->nullable();
            $table->integer('total_runs')->nullable();
            $table->integer('is_over_completed')->nullable();
            $table->string('extra_type')->nullable();
            $table->string('current_run_rate')->nullable();
            $table->string('projected_score')->nullable();
            $table->boolean('is_wicket')->default(false);
            $table->string('wicket_type')->nullable();
            $table->string('dismissal_type')->nullable();
            $table->integer('extra_runs')->nullable();


            $table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade');
            $table->foreign('batting_team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('bowling_team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('striker_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('non_striker_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('bowler_id')->references('id')->on('players')->onDelete('cascade');
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
        Schema::dropIfExists('ball_by_ball');
    }
}
