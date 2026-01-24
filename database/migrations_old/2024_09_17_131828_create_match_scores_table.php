<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('match_scores', function (Blueprint $table) {
            $table->bigIncrements('id'); // Primary key
            $table->bigInteger('match_id')->unsigned(); // Foreign key referencing matches.id
            $table->bigInteger('team_id')->unsigned(); // Foreign key referencing teams.id
            $table->integer('total_runs');
            $table->integer('total_wickets');
            $table->decimal('overs_faced', 5, 2); // 5 digits with 2 decimal places
            $table->decimal('run_rate', 5, 2); // 5 digits with 2 decimal places
            $table->integer('extras')->nullable();
            $table->boolean('is_batting');
            $table->integer('projected_score')->nullable(); // Nullable if not applicable

            // Foreign key constraints
            $table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');

            $table->timestamps(); // Created at and Updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('match_scores');
    }
}
