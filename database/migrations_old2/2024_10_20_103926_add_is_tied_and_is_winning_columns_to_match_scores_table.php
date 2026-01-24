<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsTiedAndIsWinningColumnsToMatchScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('match_scores', function (Blueprint $table) {
            //
            // $table->integer('is_winning')->nullable();
            $table->boolean('is_tied')->default(0)->after('is_winning');
            $table->integer('total_fours')->default(0)->after('total_wickets');
            $table->integer('total_sixes')->default(0)->after('total_fours');
            $table->integer('total_boundaries')->default(0)->after('total_sixes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('match_scores', function (Blueprint $table) {
            //
            $table->dropColumn('is_tied');
            $table->dropColumn('total_fours');
            $table->dropColumn('total_sixes');
            $table->dropColumn('total_boundaries');
        });
    }
}
