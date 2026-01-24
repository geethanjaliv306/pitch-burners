<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoundIdAndGroupIdToPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('points', function (Blueprint $table) {
            $table->foreignId('group_id')->constrained('groups')->after('matches_played')->onDelete('cascade');
            $table->foreignId('round_id')->constrained('tournament_rounds')->after('group_id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('points', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');

            $table->dropForeign(['round_id']);
            $table->dropColumn('round_id');
        });
    }
}
