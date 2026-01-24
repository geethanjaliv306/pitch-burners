<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuperOverScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('super_over_scores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('match_id')->unsigned();
            $table->bigInteger('match_score_id')->unsigned();
            $table->bigInteger('team_id')->unsigned();
            $table->integer('runs_scored')->nullable();
            $table->integer('wickets_lost')->nullable();
            $table->decimal('overs_bowled', 5, 2)->nullable();
            $table->integer('extras')->nullable();
            $table->boolean('is_winning')->default(0)->nullable();
            $table->boolean('is_tied')->default(0)->nullable();
            $table->boolean('is_first_inning_super_over')->default(0)->nullable();
            $table->boolean('is_second_inning_super_over')->default(0)->nullable();
            $table->integer('total_fours')->default(0);
            $table->integer('total_sixes')->default(0);
            $table->integer('total_boundaries')->default(0);
            $table->boolean('first_super_over')->default(0)->nullable();
            $table->boolean('second_super_over')->default(0)->nullable();

            // Foreign keys
            $table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade');
            $table->foreign('match_score_id')->references('id')->on('match_scores')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');

            // Timestamps and soft delete
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
        Schema::dropIfExists('super_over_scores');
    }
}
