<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDismissedBatsmenToBallByBallTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ball_by_ball', function (Blueprint $table) {
            $table->string('dismissed_batsmen')->nullable()->after('is_wicket');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ball_by_ball', function (Blueprint $table) {
            $table->dropColumn('dismissed_batsmen');
        });
    }
}
