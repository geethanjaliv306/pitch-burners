<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMvpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mvps', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys
            $table->foreignId('match_id')
                  ->constrained('matches')
                  ->onDelete('cascade');
            
            $table->foreignId('team_id')
                  ->constrained('teams')
                  ->onDelete('cascade');
            
            $table->foreignId('player_id')
                  ->constrained('players')
                  ->onDelete('cascade');

            // Timestamp for when MVP was selected
            $table->timestamp('timestamp');

            // Created at and updated at timestamps
            $table->timestamps();

            // Unique constraint to ensure only one MVP per match
            $table->unique('match_id');
            
            // Index for faster lookups
            $table->index(['match_id', 'team_id', 'player_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mvps');
    }
}