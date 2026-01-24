<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoundIdToScheduleMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedule_matches', function (Blueprint $table) {
            $table->unsignedBigInteger('round_id')->nullable()->after('tournament_id');

            // $table->foreign('round_id')->references('id')->on('tournaments_rounds')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedule_matches', function (Blueprint $table) {
            $table->dropColumn('round_id');
        });
    }
}
