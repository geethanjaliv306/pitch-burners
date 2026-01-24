<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnsInAboutUsContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('about_us_content', function (Blueprint $table) {
            $table->renameColumn('vision', 'sub_details1');
            $table->renameColumn('mission', 'sub_details2');
            $table->renameColumn('objective', 'sub_details3');
            $table->text('title1')->nullable()->before('sub_details1');
            $table->text('title2')->nullable()->before('sub_details2');
            $table->text('title3')->nullable()->before('sub_details3');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('about_us_content', function (Blueprint $table) {
            $table->renameColumn('sub_details1', 'vision');
            $table->renameColumn('sub_details2', 'mission');
            $table->renameColumn('sub_details3', 'objective');
            $table->dropColumn(['title1', 'title2', 'title3']);
        });
    }
}
