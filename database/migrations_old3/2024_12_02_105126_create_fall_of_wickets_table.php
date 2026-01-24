<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFallOfWicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fall_of_wickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id');
            $table->tinyInteger('inning');
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('dismissed_batsmen');
            $table->integer('wicket_number');
            $table->integer('score_at_dismissal')->default(0);
            $table->decimal('batsmen_dismissed_by_over', 4,1);

            $table->timestamps();

            $table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('dismissed_batsmen')->references('id')->on('players')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fall_of_wickets');
    }
}
