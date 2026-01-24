<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchImagesTable extends Migration
{
    public function up()
    {
        Schema::create('match_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mvp_id')->constrained('mvps')->onDelete('cascade');
            $table->string('image_path');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('match_images');
    }
}