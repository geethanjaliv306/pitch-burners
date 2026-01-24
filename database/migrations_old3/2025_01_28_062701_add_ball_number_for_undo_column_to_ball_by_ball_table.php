<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBallNumberForUndoColumnToBallByBallTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ball_by_ball', function (Blueprint $table) {
            $table->integer('ball_number_for_undo')->nullable()->after('innings_completed');
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
            $table->dropColumn('ball_number_for_undo');
        });
    }
}
