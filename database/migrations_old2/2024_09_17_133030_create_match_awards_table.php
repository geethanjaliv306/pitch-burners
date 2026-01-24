<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchAwardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('match_awards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('match_id')->unsigned();
            $table->bigInteger('player_id')->unsigned();
            $table->string('award_type')->nullable();

            $table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade');
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');

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
        Schema::dropIfExists('match_awards');
    }
}
