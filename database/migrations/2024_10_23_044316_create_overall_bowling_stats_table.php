<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOverallBowlingStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('overall_bowling_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tournament_id');
            $table->unsignedBigInteger('player_id');
            $table->unsignedBigInteger('match_id');
            $table->integer('total_wickets')->default(0);
            $table->integer('balls_bowled')->default(0);
            $table->integer('runs_given')->default(0);
            $table->decimal('economy_rate', 5, 2)->default(0.00);
            $table->timestamps();
            $table->softDeletes(); 

            // Foreign key constraints
            $table->foreign('tournament_id')->references('id')->on('tournaments');
            $table->foreign('player_id')->references('id')->on('players');
            $table->foreign('match_id')->references('id')->on('matches');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('overall_bowling_stats');
    }
}
