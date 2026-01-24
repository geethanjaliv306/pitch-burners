<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailedMatchEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detailed_match_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('match_id')->unsigned();
            $table->integer('over_number')->nullable();
            $table->integer('ball_number')->nullable();
            $table->string('event_type')->nullable();
            $table->integer('runs_scored')->nullable();
            $table->string('extra_type')->nullable();
            $table->text('description')->nullable();

            $table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade');

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
        Schema::dropIfExists('detailed_match_events');
    }
}
