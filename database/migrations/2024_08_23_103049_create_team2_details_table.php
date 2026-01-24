<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeam2DetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team2_details', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('match_id'); 
            $table->unsignedBigInteger('player_id'); 
            $table->boolean('captain')->default(false); 
            $table->boolean('wicketkeeper')->default(false); 
            $table->boolean('12th_man')->default(false); 
            $table->timestamps();
            $table->softDeletes(); 

            $table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade');
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('team2_details');
    }
}
