<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSuperOverAndSuperOverWinSuperOverLossToTheTablePoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('points', function (Blueprint $table) {
            //
            $table->tinyInteger('super_over_win')->after('losses')->nullable();
            $table->tinyInteger('super_over_loss')->after('super_over_win')->nullable();
            $table->boolean('is_super_over')->after('super_over_loss')->default(0);
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
            //
            $table->dropColumn('super_over_win');
            $table->dropColumn('super_over_loss');
            $table->dropColumn('is_super_over');
        });
    }
}
