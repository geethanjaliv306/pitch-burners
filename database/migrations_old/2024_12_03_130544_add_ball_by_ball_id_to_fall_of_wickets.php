<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBallByBallIdToFallOfWickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fall_of_wickets', function (Blueprint $table) {
            //
            // if (!Schema::hasColumn('fall_of_wickets', 'ball_by_ball_id')) {
             $table->unsignedBigInteger('ball_by_ball_id')->after('id')->unsigned()->notNull();
            $table->foreign('ball_by_ball_id')->references('id')->on('ball_by_ball')->onDelete('cascade');
            // }
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fall_of_wickets', function (Blueprint $table) {
            $table->dropColumn('ball_by_ball_id');
        });
    }
}
