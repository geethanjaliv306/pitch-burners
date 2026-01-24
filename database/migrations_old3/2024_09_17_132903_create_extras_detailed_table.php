<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtrasDetailedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extras_detailed', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('match_id')->unsigned();
            $table->bigInteger('team_id')->unsigned();
            $table->integer('wides')->nullable();
            $table->integer('no_balls')->nullable();
            $table->integer('byes')->nullable();
            $table->integer('leg_byes')->nullable();
            $table->integer('penalty_runs')->nullable();
            $table->integer('total_extras')->nullable();

            $table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');

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
        Schema::dropIfExists('extras_detailed');
    }
}
