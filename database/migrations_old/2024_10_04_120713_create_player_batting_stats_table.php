<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerBattingStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_batting_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id')->unsigned();
            $table->unsignedBigInteger('player_id')->unsigned();
            $table->bigInteger('team_id')->unsigned();
            $table->unsignedBigInteger('ball_by_ball_id'); // Foreign key to ball_by_ball table
            $table->integer('score');
            $table->integer('one')->default(0);
            $table->integer('two')->default(0);
            $table->integer('three')->default(0);
            $table->integer('four')->default(0);
            $table->integer('five')->default(0);
            $table->integer('six')->default(0);
            $table->integer('other_runs')->default(0);
            $table->integer('bye_runs')->default(0);
            $table->decimal('strike_rate', 5, 2)->nullable();
            $table->integer('balls_faced')->default(0);
            $table->boolean('is_out')->default(false);
            $table->string('dismissal_type')->nullable(); // Bowled, Caught, etc.
            $table->timestamps();
            $table->softDeletes();

            // Foreign Key Constraints
            $table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('ball_by_ball_id')->references('id')->on('ball_by_ball')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('player_batting_stats');
    }
}


