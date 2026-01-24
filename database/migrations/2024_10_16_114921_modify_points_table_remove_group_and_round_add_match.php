<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyPointsTableRemoveGroupAndRoundAddMatch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('points', function (Blueprint $table) {
            // Drop the foreign key and column for group_id
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
            
            // Drop the foreign key and column for round_id
            $table->dropForeign(['round_id']);
            $table->dropColumn('round_id');
            
            // Add the new match_id foreign key column
            $table->foreignId('match_id')->after('tournament_id')->constrained('matches')->onDelete('cascade');
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
            // Add back the group_id column
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            
            // Add back the round_id column
            $table->foreignId('round_id')->constrained('tournament_rounds')->onDelete('cascade');
            
            // Drop the match_id column
            $table->dropForeign(['match_id']);
            $table->dropColumn('match_id');
        });
    }
}
