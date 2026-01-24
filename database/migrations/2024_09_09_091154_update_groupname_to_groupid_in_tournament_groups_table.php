<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateGroupnameToGroupidInTournamentGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tournament_groups', function (Blueprint $table) {
            $table->dropColumn('group_name');
            
            // Add the new group_id column
            $table->unsignedBigInteger('group_id')->after('round_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tournament_groups', function (Blueprint $table) {
            // Add the group_name column back in case of rollback
            $table->string('group_name')->after('round_type');

            // Drop the group_id column
            $table->dropColumn('group_id');
        });
    }
}
