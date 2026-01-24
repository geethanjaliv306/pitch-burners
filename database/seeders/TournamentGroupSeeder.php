<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class TournamentGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tournament_groups')->truncate();

        DB::table('tournament_groups')->insert([
            ['id' => '1', 'tournament_id' => '2', 'group_id' => '1', 'team_id' => '1'],
            ['id' => '2', 'tournament_id' => '2', 'group_id' => '2', 'team_id' => '2'],
            ['id' => '3', 'tournament_id' => '2', 'group_id' => '3', 'team_id' => '3'],
            ['id' => '4', 'tournament_id' => '2', 'group_id' => '4', 'team_id' => '4'],
            ['id' => '5', 'tournament_id' => '2', 'group_id' => '5', 'team_id' => '5'],
            ['id' => '6', 'tournament_id' => '2', 'group_id' => '6', 'team_id' => '6'],
            ['id' => '7', 'tournament_id' => '2', 'group_id' => '7', 'team_id' => '7'],
        ]);  
    }
}
