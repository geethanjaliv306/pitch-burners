<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentAndVerifiedColumnsToTournamentTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tournament_teams', function (Blueprint $table) {
            $table->boolean('payment')->default(0)->after('qualified');
            $table->boolean('verified')->default(0)->after('payment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tournament_teams', function (Blueprint $table) {
            $table->dropColumn('payment');
            $table->dropColumn('verified');
        });
    }
}
