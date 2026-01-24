<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInningsDataToPlayerBowlingStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('player_bowling_stats', function (Blueprint $table) {
            //
            $table->integer('innings')->nullable()->after('extra_runs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('player_bowling_stats', function (Blueprint $table) {
            //
            $table->dropColumn('innings');
        });
    }
}
