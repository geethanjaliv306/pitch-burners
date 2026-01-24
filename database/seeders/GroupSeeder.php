<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      
        DB::table('groups')->truncate();

        DB::table('groups')->insert([
            ['id' => '1', 'tournament_id' => '2', 'group_name' => 'Group A'],
            ['id' => '2', 'tournament_id' => '2', 'group_name' => 'Group B'],
            ['id' => '3', 'tournament_id' => '2', 'group_name' => 'Group C'],
            ['id' => '4', 'tournament_id' => '2', 'group_name' => 'Group D'],
            ['id' => '5', 'tournament_id' => '2', 'group_name' => 'Group E'],
            ['id' => '6', 'tournament_id' => '2', 'group_name' => 'Group F'],
            ['id' => '7', 'tournament_id' => '2', 'group_name' => 'Group G'],
        ]);  
      
    }
}
