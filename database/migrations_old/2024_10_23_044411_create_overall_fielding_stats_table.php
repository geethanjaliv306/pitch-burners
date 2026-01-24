<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOverallFieldingStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('overall_fielding_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tournament_id');
            $table->unsignedBigInteger('player_id');
            $table->unsignedBigInteger('match_id');
            $table->integer('catches')->default(0);
            $table->integer('run_outs')->default(0);
            $table->integer('stumpings')->default(0);
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
        Schema::dropIfExists('overall_fielding_stats');
    }
}
