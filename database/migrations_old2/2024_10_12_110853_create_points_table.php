<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained('tournaments')->onDelete('cascade');
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->foreignId('round_id')->constrained('tournament_rounds')->onDelete('cascade');
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');

            $table->integer('matches_played')->default(0);
            $table->integer('wins')->default(0);
            $table->integer('losses')->default(0);
            $table->integer('matches_not_played')->default(0);
            $table->integer('matches_tied')->default(0);
            $table->decimal('net_run_rate', 5, 3)->default(0.00);
            $table->integer('total_points')->default(0);

            $table->timestamps();
            $table->softDeletes(); // Add soft deletes column (deleted_at)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('points');
    }
}
