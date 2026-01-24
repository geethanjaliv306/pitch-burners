<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMatchImagesTableChangeForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('match_images', function (Blueprint $table) {
            $table->dropForeign(['mvp_id']);
            $table->dropColumn('mvp_id');
            $table->foreignId('match_id')->constrained('matches')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('match_images', function (Blueprint $table) {
            $table->dropForeign(['match_id']);
            $table->dropColumn('match_id');
            $table->foreignId('mvp_id')->constrained('mvps')->onDelete('cascade');
        });
    }
}
