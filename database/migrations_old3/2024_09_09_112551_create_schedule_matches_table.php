<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tournament_id');
            $table->unsignedBigInteger('team1');
            $table->unsignedBigInteger('team2');
            $table->integer('number_of_overs')->nullable(); 
            $table->integer('overs_per_bowler')->nullable(); 
            $table->string('type')->nullable(); 
            $table->string('category')->nullable();
            $table->string('ground')->nullable(); 
            $table->dateTime('match_date_time')->nullable();
            $table->timestamps();
            $table->softDeletes(); 

            $table->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('cascade');
            $table->foreign('team1')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('team2')->references('id')->on('teams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_matches');
    }
}
