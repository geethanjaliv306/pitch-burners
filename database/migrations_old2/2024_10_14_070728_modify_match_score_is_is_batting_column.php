<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyMatchScoreIsIsBattingColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('match_scores', function (Blueprint $table) {
            $table->boolean('is_batting')->default(0)->change();

            $table->boolean('is_first_inning')->default(0)->after('projected_score');
            $table->boolean('is_second_inning')->default(0)->after('is_first_inning');
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
            $table->boolean('is_batting')->change();

            $table->dropColumn(['is_first_inning', 'is_second_inning']);
        });
    }
}
