<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInningsCompletedToBallByBallTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ball_by_ball', function (Blueprint $table) {
            $table->boolean('innings_completed')->default(0)->after('extra_runs');
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
            $table->dropColumn('innings_completed');
        });
    }
}
