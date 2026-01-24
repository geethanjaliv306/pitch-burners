<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScoreboardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scoreboards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id');
            $table->tinyInteger('inning');
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('batter_id');
            $table->unsignedBigInteger('bowler_id')->nullable();
            $table->unsignedBigInteger('fielder_id')->nullable();
            $table->boolean('is_out')->default(0);
            $table->string('dismissal_type')->nullable();
            $table->integer('runs')->default(0);
            $table->integer('balls_faced')->default(0);
            $table->integer('fours')->default(0);
            $table->integer('sixes')->default(0);
            $table->decimal('strike_rate', 5, 2)->nullable();
            $table->timestamps();

            $table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('batter_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('bowler_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('fielder_id')->references('id')->on('players')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scoreboards');
    }
}
