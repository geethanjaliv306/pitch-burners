<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBonafideToTeams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('bonafide')->after('password')->nullable();
        });
        Schema::table('tournament_teams', function (Blueprint $table) {
            if(Schema::hasColumn('tournament_teams', 'team_bonafide')) {
                $table->dropColumn('team_bonafide');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('bonafide');
        });
    }
}
