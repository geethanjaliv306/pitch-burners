<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToAboutUsContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('about_us_content', function (Blueprint $table) {
            $table->text('mission')->nullable()->after('vision'); 
            $table->text('objective')->nullable()->after('mission'); 
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
            $table->dropColumn(['mission', 'objective']);
        });
    }
}
