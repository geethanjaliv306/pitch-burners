<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTeamsToQualifyToTournamentRoundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tournament_rounds', function (Blueprint $table) {
            $table->integer('teams_to_qualify')->default(0)->after('overs_per_bowler');
        });
    }    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tournament_rounds', function (Blueprint $table) {
            $table->dropColumn('teams_to_qualify');
        });
    }
    
}
