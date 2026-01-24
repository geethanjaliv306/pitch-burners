<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('teamA_id'); 
            $table->unsignedBigInteger('teamB_id'); 
            $table->string('venue')->nullable(); 
            $table->datetime('match_date_time')->nullable(); 
            $table->string('type')->nullable();
            $table->integer('overs')->nullable();
            $table->string('first_umpire')->nullable();
            $table->string('second_umpire')->nullable();
            $table->string('third_umpire')->nullable();
            $table->string('first_scorer')->nullable();
            $table->string('second_scorer')->nullable();
            $table->string('status')->nullable();
            $table->timestamps(); 
            $table->softDeletes(); 

            $table->foreign('teamA_id')->references('id')->on('teams');
            $table->foreign('teamB_id')->references('id')->on('teams');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matches');
    }
}
