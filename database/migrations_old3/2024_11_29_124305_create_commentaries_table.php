<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commentaries', function (Blueprint $table) {
            $table->id();
            $table->integer('ball_by_ball_id');
            $table->integer('match_id');
            $table->integer('inning');
            $table->integer('over');
            $table->string('ball');
            $table->string('display_run');
            $table->integer('total_score');
            $table->integer('striker_id');
            $table->string('non_striker_id');
            $table->string('bowler_id');
            $table->string('fielder_id')->nullable();
            $table->string('commentary_text');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commentaries');
    }
}
